<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Location;
use App\Entity\Outing;
use App\Repository\CampusRepository;
use App\Repository\LocationRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OutingModifyType extends AbstractType
{
        public function buildForm(FormBuilderInterface $builder, array $options): void{
        $builder
            ->add('name', TextType::class, [
                'label'=>'Nom de la sortie*',
                'attr' => [
                    'placeholder' => 'Ma chouette sortie',
                ],
            ])
            ->add('startDateTime', DateTimeType::class, [
                'label' => 'Date et heure de début*',
                'widget' => 'single_text',
            ])
            ->add('duration', ChoiceType::class, [
                'label' => 'Durée*',
                'choices' => [
                    '30 minutes' => 30,
                    '1 heure' => 60,
                    '1 heure 30 minutes' => 90,
                    '2 heures' => 120,
                    '2 heures 30 minutes'=> 150,
                    '3 heures'=> 180,
                    '3 heures 30 minutes'=> 210,
                    '4 heures'=> 2400,
                ]
            ])
            ->add('signUpDateLimit', DateTimeType::class, [
                'label' => 'Date limite d\'inscription*',
                'widget' => 'single_text',
            ])
            ->add('nbSignupsMax', IntegerType::class, [
                'label'=>'Nombre de places (max.)',
                'attr' => [
                    'placeholder' => 'ex. 5',
                ],
            ])
            ->add('outingInfo', TextareaType::class, [
                'label'=>'Déscription et informations',
                'attr' => [
                    'placeholder' => 'Soirée inoubliable...',
                ],
            ])
            ->add('campus', EntityType::class, [
                'label' => 'Campus*',
                'class' => Campus::class,
                'choice_label' => 'name',
                'query_builder' => function (CampusRepository $campusRepository) {
                    return $campusRepository->createQueryBuilder('c');
                }
            ])
            ->add('location', EntityType::class, [
                'label'=>'Lieu de la sortie*',
                'class' => Location::class,
                'choice_label' => 'name',
                'placeholder' => 'Sélectionner un lieu',
                'query_builder' => function (LocationRepository $locationRepository) {
                    return $locationRepository->createQueryBuilder('l');
                }
            ])
//            ->add('photo', FileType::class, [
//                'label' => 'Photo (facultatif)',
//            ])
        ;
    }
        public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Outing::class,
            'required' => false,
        ]);
    }
}
