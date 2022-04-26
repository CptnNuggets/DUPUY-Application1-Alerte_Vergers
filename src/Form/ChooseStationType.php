<?php

namespace App\Form;

use App\Entity\Station;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChooseStationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('station', EntityType::class, [
                'class' => Station::class,
                'choice_label' => 'stationName',
                'placeholder' => 'Selectionnez une station',
            ])
        ;
    }
}
