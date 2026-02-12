<?php

namespace App\Form\Owner;

use App\Entity\Property;
use App\Entity\User;
use App\Entity\Worker;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

final class PropertyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var User|null $owner */
        $owner = $options['owner'] ?? null;

        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du logement',
                'attr' => [
                    'placeholder' => 'Ex: Studio Centre, Appartement Quartier Nord…',
                    'maxlength' => 120,
                ],
                'constraints' => [
                    new NotBlank(message: 'Le nom est obligatoire.'),
                    new Length(max: 120),
                ],
            ])
            ->add('assignedWorker', EntityType::class, [
                'label' => 'Intervenant assigné',
                'class' => Worker::class,
                'required' => false,
                'placeholder' => '—',
                'disabled' => $owner === null,
                'help' => 'Optionnel. Vous pourrez l’assigner plus tard.',

                // On limite aux workers liés à ce owner
                'choices' => $owner ? $owner->getWorkers()->toArray() : [],
                'choice_label' => static fn (Worker $w) => $w->getFullName().' — '.$w->getPhone(),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Property::class,
            'owner' => null,
        ]);

        $resolver->setAllowedTypes('owner', [User::class, 'null']);
    }
}
