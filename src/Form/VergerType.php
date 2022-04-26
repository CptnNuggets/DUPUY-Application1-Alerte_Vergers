<?php

namespace App\Form;

use App\Entity\MessageAlerte;
use App\Entity\Verger;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VergerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('idVerger', TextType::class, ['label'=>'Nom du verger']  )
            ->add('contact')
            ->add('MessageAlerte', EntityType::class, [
                'class' => MessageAlerte::class,
                'choice_label' => 'alerteCode'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Verger::class,
        ]);
    }
}
