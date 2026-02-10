<?php

namespace App\Form;

use App\Entity\Property;
use App\Entity\User;
use App\Entity\Worker;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PropertyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var User $owner */
        $owner = $options['owner'];

        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du logement',
            ])
            ->add('assignedWorker', EntityType::class, [
                'label' => 'Intervenant assigné',
                'class' => Worker::class,
                'required' => false,
                'placeholder' => '—',
                // Important : on limite aux workers liés à ce owner
                'choices' => $owner->getWorkers()->toArray(),
                'choice_label' => static fn(Worker $w) => $w->getFullName() . ' — ' . $w->getPhone(),
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Property::class,
            'owner' => null,
        ]);

        $resolver->setAllowedTypes('owner', User::class);
    }
}
