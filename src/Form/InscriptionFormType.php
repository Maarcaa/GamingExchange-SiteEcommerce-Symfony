<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class InscriptionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('email', EmailType::class, [
            'label' => 'Email',
            'constraints' => [
                new NotBlank([
                    'message' => 'Ce champ ne peut être vide',
                ]),
                new Length([
                    'max' => 180,
                    'maxMessage' => 'Votre email ne peut dépasser {{ limit }} caractères',
                ]),
                new Email([
                    'message' => 'Votre email n\'est pas au bon format: ex. mail@example.com'
                ]),
            ],
        ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide',
                    ]),
                    new Length([
                        'max' => 255,
                        'min' => 4,
                        'maxMessage' => 'Votre mot de passe ne peut dépasser {{ limit }} caractères',
                        'minMessage' => 'Votre mot de passe doit avoir au minimum {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide',
                    ]),
                    new Length([
                        'max' => 50,
                        'min' => 2,
                        'maxMessage' => 'Votre nom ne peut dépasser {{ limit }} caractères',
                        'minMessage' => 'Votre nom doit avoir au minimum {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide',
                    ]),
                    new Length([
                        'max' => 50,
                        'min' => 2,
                        'maxMessage' => 'Votre prénom ne peut dépasser {{ limit }} caractères',
                        'minMessage' => 'Votre prénom doit avoir au minimum {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('gender', ChoiceType::class, [
                'label' => 'Civilité',
                'expanded' => true,
                'choices' => [
                    'Homme' => 'h',
                    'Femme' => 'f'
                ],
                'attr' => [
                    'class' => 'd-flex gap-3',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez choisir un genre',
                    ]),
                ]
            ])
            
            ->add('adresse', TextType::class, [
                'label' => 'Adresse',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide',
                    ]),
                    new Length([
                        'max' => 200,
                        'min' => 10,
                        'maxMessage' => 'Votre adresse ne peut dépasser {{ limit }} caractères',
                        'minMessage' => 'Votre adresse doit avoir au minimum {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('code_postal', IntegerType::class, [
                'label' => 'Code postal',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide',
                    ]),
                    new Length([
                        'max' => 5,
                        'min' => 5,
                        'maxMessage' => 'Votre code postal doit comporter {{ limit }} caractères',
                        'minMessage' => 'Votre code postal doit comporter {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('ville', TextType::class, [
                'label' => 'Ville',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide',
                    ]),
                    new Length([
                        'max' => 100,
                        'min' => 3,
                        'maxMessage' => 'Votre adresse ne peut dépasser {{ limit }} caractères',
                        'minMessage' => 'Votre adresse doit avoir au minimum {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('telephone', NumberType::class, [
                    'label' => 'Téléphone',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Ce champ ne peut être vide',
                        ]),
                        new Length([
                            'max' => 10,
                            'min' => 10,
                            'maxMessage' => 'Votre téléphone doit compoter {{ limit }} caractères',
                            'minMessage' => 'Votre téléphone doit compoter {{ limit }} caractères',
                        ]),
                    ],
                ])
            ->add('pseudo', TextType::class, [
                'label' => 'Pseudo',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide',
                    ]),
                    new Length([
                        'max' => 100,
                        'min' => 4,
                        'maxMessage' => 'Votre pseudo ne peut dépasser {{ limit }} caractères',
                        'minMessage' => 'Votre pseudo doit avoir au minimum {{ limit }} caractères',
                    ]),
                ],
            ])
            
            ->add('submit', SubmitType::class, [
                'label' => 'Valider',
                // Cette option permet de désactiver le validator HTML (front), comme on a fait en twig (voir ci-dessous)
                    # => form_start(form, {'attr': {'novalidate': novalidate}})
                'validate' => false,
                'attr' => [
                    'class' => 'd-block mx-auto col-2 btn btn-primary'
                ]
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
