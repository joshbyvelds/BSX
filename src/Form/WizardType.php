<?php

namespace App\Form;

use App\Entity\WizardPlay;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WizardType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('ticker')
            ->add('currentPrice')
            ->add('entryPrice')
            ->add('targets')
            ->add('save', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary float-right'
                ]    
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => WizardPlay::class,
        ]);
    }
}
