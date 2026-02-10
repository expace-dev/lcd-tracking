<?php

namespace App\DataFixtures;

use App\Entity\Intervention;
use App\Entity\InterventionPhoto;
use App\Entity\Property;
use App\Entity\User;
use App\Entity\Worker;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {}

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $tz = new \DateTimeZone('Europe/Paris');

        $ownersCount = random_int(3, 5);

        // --- 1) Owners (Users)
        $owners = [];
        for ($i = 0; $i < $ownersCount; $i++) {
            $owner = new User();
            $owner->setEmail($faker->unique()->safeEmail());
            $owner->setFirstName($faker->firstName());
            $owner->setLastName($faker->lastName());

            // password dev simple (à changer si besoin)
            $owner->setPassword($this->passwordHasher->hashPassword($owner, 'password'));

            $manager->persist($owner);
            $owners[] = $owner;
        }

        // --- 2) Workers pool (shared across owners)
        // un peu plus d'intervenants que de proprios, pour simuler une conciergerie/partage futur
        $workersCount = random_int(8, 14);
        $workers = [];

        for ($i = 0; $i < $workersCount; $i++) {
            $w = new Worker();
            $w->setFirstName($faker->firstName());
            $w->setLastName($faker->lastName());
            $w->setPhone($this->uniqueFrenchMobile($faker));
            $w->setEmail($faker->unique()->safeEmail());

            // accessToken est déjà auto-généré dans ton constructeur,
            // mais on le régénère pour être certain d’avoir quelque chose de frais
            $w->regenerateAccessToken();

            $manager->persist($w);
            $workers[] = $w;
        }

        // Associer chaque worker à 1..3 owners (many-to-many)
        foreach ($workers as $worker) {
            $ownersToLink = $faker->randomElements($owners, random_int(1, min(3, count($owners))));
            foreach ($ownersToLink as $owner) {
                $owner->addWorker($worker);
            }
        }

        // --- 3) Properties (per owner) + assigned worker (must be linked to owner)
        $properties = [];
        foreach ($owners as $owner) {
            $ownerWorkers = $owner->getWorkers()->toArray();
            if (count($ownerWorkers) === 0) {
                // sécurité : ne devrait pas arriver vu l'association ci-dessus
                $ownerWorkers = $workers;
            }

            $propertiesCount = random_int(3, 8);
            for ($i = 0; $i < $propertiesCount; $i++) {
                $p = new Property();
                $p->setName($this->propertyName($faker));
                $p->setOwner($owner);

                // 1 intervenant actif par logement
                /** @var Worker $assigned */
                $assigned = $faker->randomElement($ownerWorkers);
                $p->setAssignedWorker($assigned);

                $manager->persist($p);
                $properties[] = $p;
            }
        }

        // --- 4) Interventions on last 14 days (0/1 per day per property)
        // businessDate = date (Europe/Paris)
        $today = (new \DateTimeImmutable('now', $tz))->setTime(0, 0, 0);
        $daysBack = 14;

        foreach ($properties as $property) {
            $assignedWorker = $property->getAssignedWorker();

            for ($d = 0; $d < $daysBack; $d++) {
                $date = $today->sub(new \DateInterval('P' . $d . 'D'));

                // probabilité de présence d'une intervention ce jour-là
                if ($faker->boolean(70) === false) {
                    continue;
                }

                $intervention = new Intervention();
                $intervention->setProperty($property);

                // normalement toujours assigné, mais on sécurise
                $intervention->setCreatedBy($assignedWorker ?? $faker->randomElement($workers));

                $intervention->setBusinessDate($date);

                // sortie voyageurs (nullable)
                if ($faker->boolean(85)) {
                    $intervention->setExitOnTime($faker->boolean(90));
                    $intervention->setInstructionsRespected($faker->boolean(90));
                } else {
                    $intervention->setExitOnTime(null);
                    $intervention->setInstructionsRespected(null);
                }

                $intervention->setExitComment($faker->boolean(35) ? $faker->sentence(10) : null);

                // checks ménage (drive conformity)
                // conforme si 5/5 true -> on mixe pour générer des non conformes
                $allOk = $faker->boolean(70);

                if ($allOk) {
                    $intervention->setCheckBedMade(true);
                    $intervention->setCheckFloorClean(true);
                    $intervention->setCheckBathroomOk(true);
                    $intervention->setCheckKitchenOk(true);
                    $intervention->setCheckLinenChanged(true);
                } else {
                    // on met quelques points à false
                    $intervention->setCheckBedMade($faker->boolean(80));
                    $intervention->setCheckFloorClean($faker->boolean(80));
                    $intervention->setCheckBathroomOk($faker->boolean(80));
                    $intervention->setCheckKitchenOk($faker->boolean(80));
                    $intervention->setCheckLinenChanged($faker->boolean(80));
                }

                $intervention->setCleaningComment($faker->boolean(30) ? $faker->sentence(12) : null);

                // photos fake (0..5), max 10 respecté
                $photosCount = random_int(0, 5);
                for ($k = 0; $k < $photosCount; $k++) {
                    $photo = new InterventionPhoto();
                    $photo->setPath($this->fakePhotoPath($faker));
                    $intervention->addPhoto($photo);
                }

                $manager->persist($intervention);
            }
        }

        $manager->flush();
    }

    private function uniqueFrenchMobile(\Faker\Generator $faker): string
    {
        // 10 chiffres, commence par 06 ou 07
        // ex: 06 + 8 digits
        $prefix = $faker->randomElement(['06', '07']);
        $suffix = (string) $faker->unique()->numberBetween(10_000_000, 99_999_999); // 8 digits
        return $prefix . $suffix;
    }

    private function propertyName(\Faker\Generator $faker): string
    {
        $types = ['Studio', 'T2', 'T3', 'Appartement', 'Maison', 'Loft'];
        $areas = ['Centre', 'Gare', 'Vieux Port', 'Plage', 'Quartier Nord', 'Cathédrale', 'Place'];
        return sprintf('%s %s', $faker->randomElement($types), $faker->randomElement($areas));
    }

    private function fakePhotoPath(\Faker\Generator $faker): string
    {
        // pas besoin de fichier réel pour le moment
        // tu pourras mapper ça vers /uploads/... plus tard
        $id = $faker->uuid();
        return 'uploads/interventions/fake/' . $id . '.jpg';
    }
}
