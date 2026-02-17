<?php

namespace App\Form;

use App\Entity\Availability;
use App\Entity\Day;
use App\Entity\Time;
use App\Repository\DayRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Validator\Constraints\Valid;

class AvailabilityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('day', EntityType::class, [
                'class'=> Day::class,
                'choice_label' => 'day',
                'label' => 'Jour | ',
                'query_builder' => function (DayRepository $er) {
                    return $er->createQueryBuilder('d')->orderBy('d.id', 'ASC');//Je mets en place un trie pour que les jours sortent par ordre de l'ID
                }
            ])

            ->add('startTime', EntityType::class, [
                'class'=> Time::class,
                'choice_label' => 'hour',
                'label' => ' de '
            ])

            ->add('endTime', EntityType::class, [
                'class'=> Time::class,
                'choice_label' => 'hour',
                'label' => ' à '
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Availability::class
        ]);
    }
}
