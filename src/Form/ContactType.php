<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Votre nom',
                'attr' => [
                    'placeholder' => 'Ex: Dupont',
                    'class' => 'input input-bordered input-lg w-full focus:outline-secondaryTheme'
                ],
                'constraints' => [new NotBlank()]
            ])

            ->add('email', EmailType::class, [
                'label' => 'Votre Email',
                'attr' => [
                    'placeholder' => 'dupont@exemple.fr',
                    'class' => 'input input-bordered input-lg w-full focus:outline-secondaryTheme'
                ],
                'constraints' => [new NotBlank()]
            ])

            ->add('subject', ChoiceType::class, [
                'label' => 'Sujet',
                'choices'  => [
                    'Objet de votre demande' => '',
                    'Problème technique' => 'problème technique',
                    'Demande d\'information' => 'information',
                    'Autre...' => 'autre',
                ],
                'attr' => ['class' => 'select select-bordered select-lg w-full focus:outline-secondaryTheme'],
                'choice_attr' => [
                    'Objet de votre demande' => ['disabled' => 'disabled', 'selected' => 'selected'],
                ],
            ])

            ->add('message', TextareaType::class, [
                'label' => 'Votre message',
                'attr' => [
                    'placeholder' => 'Comment pouvons-nous vous aider?',
                    'class' => 'textarea textarea-bordered h-48 w-full text-lg focus:outline-secondaryTheme'
                ],
                'constraints' => [new NotBlank()]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
