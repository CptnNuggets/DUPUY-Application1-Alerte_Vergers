<?php

namespace App\Form;

use App\Entity\Station;
use App\Entity\Verger;
use App\Entity\AssocStationVerger;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AssocStationVergerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('station', EntityType::class, [
                'class'=> Station::class,
                'choice_label' => 'stationName'
            ])
            ->add('verger', EntityType::class, [
                'class' => Verger::class, 
                'choice_label' => 'idVerger'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AssocStationVerger::class,
        ]);
    }
}
