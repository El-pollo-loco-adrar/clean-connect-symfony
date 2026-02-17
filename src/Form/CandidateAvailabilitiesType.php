<?php

namespace App\Form;

use App\Entity\Candidate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Validator\Constraints\Valid;

class CandidateAvailabilitiesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('availabilities', CollectionType::class, [
                'entry_type' => AvailabilityType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true, //Permet d'ajouter des lignes en JS
                'allow_delete' => true,
                'by_reference' => false,
                'constraints' => [
                    new Valid(),
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Candidate::class
        ]);
    }
}
