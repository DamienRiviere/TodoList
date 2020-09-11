<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class UserType
 * @package App\Form
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class UserType extends AbstractType
{

    protected const ADMIN = "ROLE_ADMIN";
    protected const USER = "ROLE_USER";

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'username',
                TextType::class,
                [
                    'label' => "Nom d'utilisateur"
                ]
            )
            ->add(
                'password',
                RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'invalid_message' => 'Les deux mots de passe doivent correspondre.',
                    'required' => true,
                    'first_options'  => [
                        'label' => 'Mot de passe'
                    ],
                    'second_options' => [
                        'label' => 'Tapez le mot de passe à nouveau'
                    ],
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'label' => 'Adresse email'
                ]
            )
            ->add(
                'roles',
                ChoiceType::class,
                [
                'choices' => [
                    'Utilisateur' => self::USER,
                    'Administrateur' => self::ADMIN
                ],
                    'expanded' => false,
                    'multiple' => false,
                    'label' => "Choix du rôle",
                    'required' => true,
                    'empty_data' => self::USER
                ]
            )
        ;

        $builder->get('roles')
            ->addModelTransformer(new CallbackTransformer(
                function ($rolesArray) {
                    if (is_array($rolesArray)) {
                        return $rolesArray[0];
                    }

                    if (is_null($rolesArray)) {
                        return '';
                    }
                },
                function ($rolesString) {
                    return [$rolesString];
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class
        ]);
    }
}
