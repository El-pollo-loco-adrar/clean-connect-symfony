<?php

namespace App\Form;

use App\Entity\InterventionArea;
use App\Entity\Skills;
use DateTime;
use App\Form\DataTransformer\TownToString;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Dom\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use App\Entity\Mission;
use App\Entity\WageScale;
use function PHPUnit\Framework\returnArgument;

class AddMissionType extends AbstractType
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager= $entityManager;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'w-full rounded-md border-gray-300 shadow-sm'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description : ',
                'attr' => [
                    'class' => 'w-full rounded-md border-gray-300 shadow-sm'
                ]
            ])
            ->add('startAt', DateTimeType::class, [
                'widget'=> 'single_text',
                'label'=> 'Date et heure de début : ',
                'attr' => [
                    'class' => 'w-full rounded-md border-gray-300 shadow-sm'
                ]
            ])
            ->add('endAt', DateTimeType::class, [
                'widget'=> 'single_text',
                'label' => 'Date et heure de fin : ',
                'attr' => [
                    'class' => 'w-full rounded-md border-gray-300 shadow-sm'
                ]
            ])
            // ------------------------------------------------
            ->add(
                $builder->create('areaLocation', TextType::class, [
                    'attr' => [
                        'placeholder' => 'Code postal ou ville ...',
                        'class' => 'w-full rounded-md border-gray-300 shadow-sm'
                    ],
                    'invalid_message' => 'Lieu Invalide',
                    'label' => false,
                ]) ->addModelTransformer(new TownToString($this->entityManager))
            )

            ->add('skills', EntityType::class, [
                'class' => Skills::class,
                'choice_label' => function(Skills $skill) {
                    return $skill->getNameSkill();
                },
                'multiple' => true,
                'expanded' => true,
                'label' => 'Techniques : ',
                'attr' => ['class' => 'hidden-skills-container hidden']
            ])
            ->add('wageScale', EntityType::class, [
                'class'=> WageScale::class,
                'choice_label' => function(WageScale $wage) {
                    return 'Niveau ' . $wage->getNiveau() . ' -  ' . $wage->getLevel() . ' (' . $wage->getHourlyRate() . '€/h)';
                },
                'placeholder' => 'Salaire ',
                'label' => 'Salaire : ',
                'attr' => [
                    'class' => 'w-full rounded-md border-gray-300 shadow-sm'
                ]
            ])

            ->add('submit', SubmitType::class, [
                'label' => 'Publier la mission',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Mission::class,
        ]);
    }
}
