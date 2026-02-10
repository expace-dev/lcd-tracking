<?php

namespace App\Form\Worker;

use App\Entity\Intervention;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;

final class InterventionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $yesNo = [
            'Oui' => true,
            'Non' => false,
        ];

        $builder
            // Sortie voyageurs (optionnel)
            ->add('exitOnTime', ChoiceType::class, [
                'label' => 'Voyageurs sortis à l’heure ?',
                'required' => false,
                'placeholder' => '—',
                'choices' => $yesNo,
            ])
            ->add('instructionsRespected', ChoiceType::class, [
                'label' => 'Consignes respectées ?',
                'required' => false,
                'placeholder' => '—',
                'choices' => $yesNo,
            ])
            ->add('exitComment', TextareaType::class, [
                'label' => 'Commentaire (optionnel)',
                'required' => false,
                'attr' => [
                    'rows' => 3,
                    'placeholder' => 'Ex: retard de 20 min, consigne non respectée...',
                ],
            ])

            // Ménage (5 checks)
            ->add('checkBedMade', CheckboxType::class, [
                'label' => 'Lit fait',
                'required' => false,
            ])
            ->add('checkFloorClean', CheckboxType::class, [
                'label' => 'Sol propre (aspiré / lavé)',
                'required' => false,
            ])
            ->add('checkBathroomOk', CheckboxType::class, [
                'label' => 'Salle de bain OK',
                'required' => false,
            ])
            ->add('checkKitchenOk', CheckboxType::class, [
                'label' => 'Cuisine OK (plan + vaisselle)',
                'required' => false,
            ])
            ->add('checkLinenChanged', CheckboxType::class, [
                'label' => 'Linge changé (serviettes + torchons)',
                'required' => false,
            ])
            ->add('cleaningComment', TextareaType::class, [
                'label' => 'Info ménage (optionnel)',
                'required' => false,
                'attr' => [
                    'rows' => 3,
                    'placeholder' => 'Ex: tâche persistante, manque un torchon...',
                ],
            ])
            ->add('newPhotos', FileType::class, [
                'label' => 'Photos (max 10)',
                'mapped' => false,
                'required' => false,
                'multiple' => true,
                'attr' => [
                    'accept' => 'image/*',
                    'capture' => 'environment',
                ],
                'constraints' => [
                    new All([
                        new File(
                            maxSize: '8M',
                            maxSizeMessage: 'Fichier trop volumineux (max 8 Mo).',
                            mimeTypes: ['image/jpeg', 'image/png', 'image/webp', 'image/heic', 'image/heif'],
                            mimeTypesMessage: 'Format image invalide (jpg/png/webp/heic).',
                        ),
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Intervention::class,
        ]);
    }
}
