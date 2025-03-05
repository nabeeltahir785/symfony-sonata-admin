<?php

namespace App\Form;

use App\Entity\ProductImage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('file', FileType::class, [
                'label' => 'Image',
                'required' => false,
                'mapped' => false
            ])
            ->add('alt', TextType::class, [
                'label' => 'Alt Text',
                'required' => false
            ])
            ->add('isThumbnail', CheckboxType::class, [
                'label' => 'Use as thumbnail',
                'required' => false
            ])
            ->add('position', IntegerType::class, [
                'label' => 'Sort Order',
                'required' => false,
                'attr' => ['min' => 0]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductImage::class,
        ]);
    }
}