<?php

namespace App\Form;

use App\Entity\AssocCapteurStation;
use App\Entity\Capteur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AssocCapteurStationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('codeCapteur')
            ->add('capteur', EntityType::class, [
                'class' => Capteur::class,
                'choice_label' => 'capteurName'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AssocCapteurStation::class,
        ]);
    }
}
