<?php

namespace App\Form\Owner;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\NotBlank;

final class WorkerSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('phone', TextType::class, [
            'label' => 'Téléphone intervenant',
            'attr' => [
                'placeholder' => '06XXXXXXXX',
                'maxlength' => 10,
                'inputmode' => 'tel',
            ],
            'constraints' => [
                new NotBlank(message: 'Le téléphone est obligatoire.'),
                new Regex(pattern: '/^(06|07)\d{8}$/', message: 'Téléphone invalide (06/07 + 8 chiffres).'),
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => true,
        ]);
    }
}
