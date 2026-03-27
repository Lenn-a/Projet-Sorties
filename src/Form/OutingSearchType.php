<?php

namespace App\Form;

use App\Entity\Campus;
use App\Form\Model\OutingSearch;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OutingSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'name',
                'placeholder' => 'Sélectionner',
            ])
            ->add('name', TextType::class, [
                'label' => 'Le nom de la sortie contient :',
            ])
            ->add('startSearchDate', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Entre',
            ])
            ->add('endSearchDate', DateType::class, [
                'widget' => 'single_text',
                'label' => 'et',
            ])
            ->add('outingOrganiser', CheckboxType::class, [
                'label' => 'Sorties dont je l\'organisateur/trice',
            ])
            ->add('outingParticipant', CheckboxType::class, [
                'label' => 'Sorties auxquelles je suis inscrit/e',
            ])
            ->add('outingNotParticipant', CheckboxType::class, [
                'label' => 'Sorties auxquelles je ne suis pas inscrit/e',
            ])
            ->add('outingPassed', CheckboxType::class, [
                'label' => 'Sorties passées',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OutingSearch::class,
            'required' => false,
        ]);
    }
}
