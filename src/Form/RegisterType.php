<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
            ])
            ->add('companyName', TextType::class, [
                'label' => 'Entreprise',
            ])
            ->add('email', EmailType::class, [
                'label' => 'Mail',
                'constraints' => [
                    new NotBlank(),
                    new Email([
                        'message' => 'Veuillez saisir un email valide.',
                    ]),
                ],
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse',
            ])
            ->add('companyWebsite', UrlType::class, [
                'label' => "Site de l'entreprise",
                'required' => false,
            ])
            ->add('siret', TextType::class, [
                'label' => 'Numéro de SIRET',
                'constraints' => [
                    new NotBlank(),
                    new Regex([
                        'pattern' => '/^\d{14}$/',
                        'message' => 'Le SIRET doit contenir 14 chiffres.',
                    ]),
                ],
            ])
            ->add('companyDescription', TextareaType::class, [
                'label' => "Description de l'entreprise",
                'attr' => [
                    'rows' => 5,
                ],
            ])
            ->add('tags', ChoiceType::class, [
                'label' => 'Tags',
                'choices' => [
                    'Transport' => 'transport',
                    'Logistique' => 'logistique',
                ],
                'expanded' => true,
                'multiple' => true,
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'first_options' => [
                    'label' => 'Mot de passe',
                ],
                'second_options' => [
                    'label' => 'Confirmer le mot de passe',
                ],
                'invalid_message' => 'Les mots de passe doivent correspondre.',
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères.',
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
