<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastname', TextType::class, [
                    "attr" => [
                        "placeholder" => "Votre nom",
                        "class" => "form-control"
                    ]
                ])    
            ->add('firstname', TextType::class, [
                "attr" => [
                    "placeholder" => "Votre prénom",
                    "class" => "form-control"
                ],
                "label_attr" => [
                    "class" => "form-label mt-3"
                ]
            ])
            ->add('email', EmailType::class, [
                "attr" => [
                    "placeholder" => "Votre email",
                    "class" => "form-control"
                ],
                "label_attr" => [
                    "class" => "form-label mt-3",
                ]
            ])
            ->add('plainPassword', RepeatedType::class, [
                "type" => PasswordType::class,
                "invalid_message" => "Le mot de passe et la confirmation doivent être identiques",
                "first_options" => [
                    "attr" => [
                        "placeholder" => "Votre mot de passe",
                        "class" => "form-control"
                    ],
                    "label" => "Mot de passe",
                    "label_attr" => [
                        "class" => "form-label mt-3"
                    ],
                ],
                "second_options" => [
                    "attr" => [
                        "placeholder" => "Confirmez votre mot de passe",
                        "class" => "form-control"
                    ],
                    "label" => "Confirmez votre mot de passe",
                    "label_attr" => [
                        "class" => "form-label mt-3"
                    ]
                ],
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'S\'il vous plaît, entrez un mot de passe',
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
