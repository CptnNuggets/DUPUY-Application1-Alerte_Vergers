<?php

namespace App\Form;

use App\Entity\MessageAlerte;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessageAlerteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('alerteCode', ChoiceType::class, [
                'choices' => [
                    'risque moyen' => 'lowRisk',
                    'risque élevé' => 'mediumRisk',
                    'risque très élevé' => 'highRisk'
                ],
            ])
            ->add('alerteMessage')
            ->add('alerteLevel')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MessageAlerte::class,
        ]);
    }
}
