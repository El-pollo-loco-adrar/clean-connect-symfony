<?php

namespace App\Form;

use App\Entity\Employer;
use App\Entity\Role;
use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class UserProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['data'] ?? null;

        $builder
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'placeholder' => 'Entrez votre nom',
                ],
                'constraints' => [
                    new NotBlank(
                        message: 'Le nom est obligatoire'
                    ),
                    new Length(
                        min:2,
                        minMessage: 'Le nom doit contenir au moins 2 caractères'
                    ),
                    new Regex(
                        pattern: '/^[a-zA-ZÀ-ÿ\s\-]+$/',
                        message: 'Le nom ne doit pas contenir de chiffres'
                    )
                ]
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'attr' => [
                    'placeholder' => 'Entrez votre prénom',
                ],
                'constraints' => [
                    new NotBlank(
                        message: 'Le prénom est obligatoire'
                    ),
                    new Length(
                        min:2,
                        minMessage: 'Le prénom doit contenir au moins 2 caractères'
                    ),
                    new Regex(
                        pattern: '/^[a-zA-ZÀ-ÿ\s\-]+$/',
                        message: 'Le nom ne doit pas contenir de chiffres'
                    )
                ]
            ])
            ->add('phoneNumber', TelType::class, [
                'label' => 'Numéro de téléphone',
                'attr' => [
                    'placeholder' => 'Entrez votre numéro de téléphone (sans espaces ni signes)',
                ],
                'constraints' => [
                    new NotBlank(
                        message: 'Le numéro de téléphone est obligatoire'
                    ),
                    new Regex(
                        pattern : '/^[0-9]{10}$/',
                        message: 'Merci de rentrer uniquement 10 chiffres uniquement (sans espaces ni signes)'
                    )

                ]
            ])
        ;
        if($user instanceof Employer) {
            $builder
                ->add('companyName', TextType::class, [
                    'label' => 'Nom de l\'entreprise',
                    'attr' => [
                        'placeholder' => 'Nom de la société',
                    ],
                    'constraints'=> [
                        new Length(
                            min:2,
                            max: 50,
                            minMessage: 'La nom de la société doit contenir entre 2 et 50 caractères minimum',
                            maxMessage: 'La nom de la société doit contenir entre 2 et 50 caractères minimum',
                        )
                    ]
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
