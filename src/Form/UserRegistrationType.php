<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class UserRegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'Pseudo*',
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom*',
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom*',
            ])
            ->add('phone', TextType::class, [
                'label' => 'Téléphone*',
            ])
            ->add('email', TextType::class, [
                'label' => 'Mail*',
            ])
//            ->add('plainPassword', RepeatedType::class, [
//                'type' => PasswordType::class,
//                'first_options'  => ['label' => 'Mot de passe*'],
//                'second_options' => ['label' => 'Confirmation du mot de passe*'],
//                'mapped' => false,
//                'constraints' => [
//                    new Assert\Regex(
//                        '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&\-_])[A-Za-z\d@$!%*?&\-_]{8,}$/',
//                        message: "Votre mot de passe d'au moins 8 caractères doit inclure au moins une lettre minuscule, une lettre majuscule, une chiffre et une symbole (@ $ ! % * ? & - _)."),
//                ]
//            ])
            ->add('campus', EntityType::class, [
                'label' => 'Campus*',
                'class' => Campus::class,
                'choice_label' => 'name',
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Role(s)*',
                'choices' => [
                    'Administrateur' => 'ROLE_ADMIN',
                    'Utilisateur' => 'ROLE_USER',
                ],
                'multiple' => true,
                'expanded' => true,
            ])
//            ->add('active', ChoiceType::class, [
//                'label' => 'Statut*',
//                'choices' => [
//                    'Actif' => 1,
//                    'Inactif' => 0,
//                ],
//                'multiple' => false,
//                'expanded' => true,
//            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
