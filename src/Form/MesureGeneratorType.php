<?php

namespace App\Form;

use App\Entity\Mesure;
use App\Entity\NumeroCapteur;
use App\Entity\Station;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MesureGeneratorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateTime', DateTimeType::class)
            ->add('valeur', NumberType::class)
            ->add('station', EntityType::class, [
                'class' => Station::class,
                'choice_label' => 'stationName'
            ])
            ->add('numeroCapteur', EntityType::class, [
                'class' => NumeroCapteur::class,
                'choice_label' => 'numero'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Mesure::class,
        ]);
    }
}
