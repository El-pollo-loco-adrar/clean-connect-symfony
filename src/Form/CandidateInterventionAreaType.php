<?php

namespace App\Form;

use App\Entity\Candidate;
use App\Form\DataTransformer\TownToString;
use App\Entity\InterventionArea;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CandidateInterventionAreaType extends AbstractType
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('interventionArea', CollectionType::class, [
                'entry_type' => TextType::class,
                'entry_options' => [
                    'attr' => [
                        'placeholder' => 'Code postal ou ville ...',
                        'class' => 'city-autocomplete w-full rounded-md border-gray-300 shadow-sm',
                        'autocomplete' => 'off'
                    ],
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => false,
        ]);

        $townTransformer = new TownToString($this->entityManager);

        $builder
            ->get('interventionArea')->addModelTransformer(new CallbackTransformer(
                // 1. De la BDD (Collection d'objets) vers le Formulaire (Tableau de Strings)
                function($areasAsCollection) use ($townTransformer) {
                    if(!$areasAsCollection) 
                    return [];

                    $strings = [];
                    foreach($areasAsCollection as $area) {
                        $strings[] = $townTransformer->transform($area);
                    }
                    return $strings;
                },
                // 2. Du Formulaire (Tableau de Strings) vers la BDD (Tableau d'objets)
                function ($areasAsArray) use ($townTransformer) {
                    if(!$areasAsArray)
                        return[];

                    $entities = [];
                    foreach ($areasAsArray as $string) {
                        if(empty($string)) continue;

                        // On utilise la logique de ton TownToString pour chaque ligne
                        $entities[] = $townTransformer->reverseTransform($string);
                    }
                    return $entities;
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Candidate::class,
        ]);
    }
}
