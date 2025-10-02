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
                    ]
                ])    
            ->add('firstname', TextType::class, [
                "attr" => [
                    "placeholder" => "Votre prénom",
                ]
            ])
            ->add('email', EmailType::class, [
                "attr" => [
                    "placeholder" => "Votre email"
                ]
            ])
            ->add('plainPassword', RepeatedType::class, [
                "type" => PasswordType::class,
                "invalid_message" => "Le mot de passe et la confirmation doivent être identiques",
                "first_options" => [
                    "attr" => [
                        "placeholder" => "Votre mot de passe"
                    ],
                    "label" => "Mot de passe",
                ],
                "second_options" => [
                    "attr" => [
                        "placeholder" => "Confirmez votre mot de passe"
                    ],
                    "label" => "Confirmez votre mot de passe"
                ],
                'mapped' => false,
                'constraints' => [
                    new NotBlank(message: 'Veuillez entrer un mot de passe'),
                    new Length(
                        min: 3,
                        minMessage: 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        max: 4096,
                    ),
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
