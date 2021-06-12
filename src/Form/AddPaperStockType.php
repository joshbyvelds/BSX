<?php

namespace App\Form;

use App\Entity\PaperStock;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddPaperStockType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('ticker')
            ->add('shares')
            ->add('first_bought')
            ->add('last_bought')
            ->add('average_price')
            ->add('type', ChoiceType::class, [
                'choices'  => [
                    'Blue Chip Joint' => 1,
                    'Blue Chip Ind' => 2,
                    'Red Chip Joint' => 3,
                    'Red Chip Ind' => 4,
                ],
            ])
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
            'data_class' => PaperStock::class,
        ]);
    }
}
