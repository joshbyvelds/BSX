<?php

namespace App\Form;

use App\Entity\Option;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddOptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('ticker')
            ->add('bought')
            ->add('type', ChoiceType::class, [
                'choices'  => [
                    'Long Call' => 'Long Call',
                    'Long Put' => 'Long Put',
                ],
            ])
            ->add('contracts')
            ->add('strike_price')
            ->add('average')
            ->add('expires')
            ->add('current_price')
            ->add('stock_price')
            ->add('buy_currency', ChoiceType::class, [
                'choices'  => [
                    'CAD' => 1,
                    'USD' => 2,
                ],
            ])
            ->add('currency', ChoiceType::class, [
                'choices'  => [
                    'CAD' => 1,
                    'USD' => 2,
                ],
            ])
            ->add('profit_calc_url')
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
            'data_class' => Option::class,
        ]);
    }
}
