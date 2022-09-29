<?php

namespace App\Form;

use App\Entity\Paiement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class PaiementFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add(
            'modepaiement', 
            ChoiceType::class, 
            [
                'choices' => [
                    'Cartebleu' => 'CB',
                    'Mastercard' => 'Mastercard',
                    'Paypal' => 'PayPal',
                ],
            'expanded' => true
            ]
        )
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
            ->add('numerocb', IntegerType::class, [
                'label' => 'Numéro de carte bleue',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide',
                    ]),
                    new Length([
                        'max' => 16,
                        'min' => 16,
                        'maxMessage' => 'Votre numéro de carte bleu doit comporter {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('dateexpiration', DateType::class,[
                'label' => 'Date d\'expiration',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide',
                    ]),
                ],         
            ])
            ->add('codeverification', NumberType::class, [
                'label' => 'Code de vérification',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide',
                    ]),
                    new Length([
                        'max' => 3,
                        'min' => 3,
                        'maxMessage' => 'Votre code de vérification doit comporter {{ limit }} caractères',
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
            'data_class' => Paiement::class,
        ]);
    }
}
