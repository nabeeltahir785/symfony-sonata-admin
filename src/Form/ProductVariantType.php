<?php

namespace App\Form;

use App\Entity\ProductVariant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductVariantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Variant Name',
                'attr' => ['placeholder' => 'e.g., Small/Red']
            ])
            ->add('sku', TextType::class, [
                'label' => 'SKU',
                'required' => false,
                'attr' => ['placeholder' => 'Stock Keeping Unit']
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Price Override',
                'required' => false,
                'currency' => 'USD',
                'help' => 'Leave empty to use parent product price'
            ])
            ->add('stock', IntegerType::class, [
                'label' => 'Stock Quantity',
                'required' => false,
                'attr' => ['min' => 0],
                'help' => 'Leave empty for unlimited stock'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductVariant::class,
        ]);
    }
}