<?php

namespace App\Form;

use App\Entity\Property;
use App\Entity\User;
use App\Model\InterventionSearch;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class InterventionSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var User $owner */
        $owner = $options['owner'];

        $builder
            ->setMethod('GET')
            ->add('from', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
                'label' => 'Du',
            ])
            ->add('to', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
                'label' => 'Au',
            ])
            ->add('property', EntityType::class, [
                'required' => false,
                'class' => Property::class,
                'choice_label' => 'name',
                'placeholder' => 'Tous les logements',
                'label' => 'Logement',
                'query_builder' => fn(EntityRepository $er) => $er->createQueryBuilder('p')
                    ->andWhere('p.owner = :owner')
                    ->setParameter('owner', $owner)
                    ->orderBy('p.name', 'ASC'),
            ])
            ->add('conform', ChoiceType::class, [
                'required' => false,
                'placeholder' => 'Tous les statuts',
                'label' => 'Statut',
                'choices' => [
                    'Conforme' => true,
                    'Non conforme' => false,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InterventionSearch::class,
            'csrf_protection' => false, // GET -> pas besoin
            'owner' => null,
        ]);

        $resolver->setAllowedTypes('owner', ['null', User::class]);
    }
}