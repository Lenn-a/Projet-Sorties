<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class OutingType
{

    public function bildForm(FormBuilderInterface $builder, array $options): void{
        $builder
            ->add('name', TextType::class, [
                'label'=>'Your outing name',
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
            ->add('nbSignUpMax', TextType::class, [
                'label'=>'Number of sign up max',
            ])
            ->add('outingInfos', TextareaType::class, [
                'label'=>'Your outing infos',
            ])
            ->add('photo', FileType::class, [
                'mapped' => false,
            ]);
    }
}
