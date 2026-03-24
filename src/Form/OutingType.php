<?php

namespace App\Form;

use App\Entity\Campus;
use App\Repository\CampusRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class OutingType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void{
        $builder
            ->add('name', TextType::class, [
                'label'=>'your outing name',
            ])
            ->add('startDateTime', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('duration', ChoiceType::class, [
                'choices' => [
                    '30 minutes' =>'30 minutes',
                    '1 heure' =>'1 heure',
                    '1 heure 30 minutes' =>'1 heure 30 minutes',
                    '2 heures' =>'2 heures',
                    '2 heures 30 minutes' =>'2 heures 30 minutes',
                    '3 heures' =>'3 heures',
                    '3 heures 30 minutes' =>'3 heures 30 minutes',
                    '4 heures' =>'4 heures',
                ]
            ])
            ->add('signUpDateLimit', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('nbSignupsMax', TextType::class, [
                'label'=>'Number of sign up max',
            ])
            ->add('outingInfo', TextareaType::class, [
                'label'=>'Your outing infos',
            ])
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'name',
                'query_builder' => function (CampusRepository $campusRepository) {
                return $campusRepository->createQueryBuilder('c');
                }
            ])
            ->add('location', TextType::class, [
                'label'=>'Your location',
            ])
            ->add('photo', FileType::class, [
                'mapped' => false,
            ]);
    }
}
