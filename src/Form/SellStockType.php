<?php

namespace App\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SellStockType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder
            ->add('ticker', EntityType::class, [
                'class' => 'App\Entity\Stock',
                'choice_label' => 'ticker'
            ])
            ->add('sell_date', DateType::class)
            ->add('shares', NumberType::class)
            ->add('sell_currency', ChoiceType::class, [
                'choices'  => [
                    'CAD' => 1,
                    'USD' => 2,
                ],
            ])
            ->add('price', NumberType::class)
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary float-right'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        // $resolver->setDefaults([
        //     'data_class' => Stock::class,
        // ]);
    }
}
