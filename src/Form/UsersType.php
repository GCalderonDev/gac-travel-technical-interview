<?php

namespace App\Form;

use App\Entity\Users;
use App\Transformer\StringToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Role\RoleHierarchy;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UsersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a username'
                    ])
                ],
                'data' => isset($options['data']) && !is_null($options['data']->getUserIdentifier()) ? $options['data']->getUserIdentifier() : ''
            ])
            ->add('password', PasswordType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'mapped' => false,
                'validation_groups' => ['creation'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password'
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        'max' => 4096
                    ])
                ],
                'required' => is_null($builder->getData()->getId())
            ])
            ->add('active', ChoiceType::class, [
                'choices'  => [
                    'Active' => true,
                    'Inactive' => false,
                ],
                'attr' => [
                    'class' => 'selectpicker form-control'
                ],
                'required' => true,
                'data' => isset($options['data']) && !is_null($options['data']->getActive()) ? $options['data']->getActive() : ''
            ])
            ->add('roles', ChoiceType::class, [
                'attr' => [
                    'class' => 'selectpicker form-control'
                ],
                'required' => true,
                'multiple' => true,
                'expanded' => false,
                'choices'  => [
                    'Admin' => 'ROLE_ADMIN',
                    'User' => 'ROLE_USER',
                ],
                'data' => isset($options['data']) && !is_null($options['data']->getRoles()) ? $options['data']->getRoles() : ''
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}
