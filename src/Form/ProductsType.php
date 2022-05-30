<?php

namespace App\Form;

use App\Entity\Categories;
use App\Entity\Products;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProductsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a name'
                    ])
                ],
                'data' => isset($options['data']) && !is_null($options['data']->getName()) ? $options['data']->getName() : ''
            ])
            ->add('stock', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'data' => isset($options['data']) && !is_null($options['data']->getStock()) ? $options['data']->getStock() : 0
            ])
            ->add('category', EntityType::class, [
                'attr' => [
                    'class' => 'selectpicker form-control'
                ],
                'class' => Categories::class,
                'choice_label' => 'name',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Products::class,
        ]);
    }
}
