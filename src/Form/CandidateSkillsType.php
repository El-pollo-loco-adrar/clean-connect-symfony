<?php

namespace App\Form;

use App\Entity\Skills;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CandidateSkillsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('skills', EntityType::class, [
                'class'=> Skills::class,
                'choice_label' => 'nameSkill',
                'group_by' => function(Skills $skill) {
                    return $skill->getSkillCategory()->getNameCategory();
                },
                'multiple' => true,
                'expanded' => true,
                'label' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
