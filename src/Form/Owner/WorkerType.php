<?php

namespace App\Form\Owner;

use App\Entity\Worker;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

final class WorkerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $phoneReadonly = (bool) ($options['phone_readonly'] ?? false);

        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [
                    new NotBlank(message: 'Le prénom est obligatoire.'),
                    new Length(max: 80),
                ],
                'attr' => ['maxlength' => 80],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new NotBlank(message: 'Le nom est obligatoire.'),
                    new Length(max: 80),
                ],
                'attr' => ['maxlength' => 80],
            ])
            ->add('phone', TextType::class, [
                'label' => 'Téléphone',
                'constraints' => [
                    new NotBlank(message: 'Le téléphone est obligatoire.'),
                    new Regex(pattern: '/^(06|07)\d{8}$/', message: 'Téléphone invalide (06/07 + 8 chiffres).'),
                ],
                'attr' => [
                    'maxlength' => 10,
                    'inputmode' => 'tel',
                    'readonly' => $phoneReadonly ? 'readonly' : null,
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email (optionnel)',
                'required' => false,
                'constraints' => [
                    new Email(message: 'Email invalide.'),
                    new Length(max: 180),
                ],
                'attr' => ['maxlength' => 180, 'placeholder' => 'optionnel'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Worker::class,
            'phone_readonly' => false,
        ]);
    }
}
