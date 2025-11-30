<?php

namespace App\Form;

use App\Entity\User;
use App\Enum\UserRole;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class)
            ->add('email', EmailType::class)
            ->add('role', ChoiceType::class, [
                'choices' => array_combine(
                    array_map(static fn(UserRole $r) => $r->value, UserRole::cases()),
                    array_map(static fn(UserRole $r) => $r->value, UserRole::cases())
                ),
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'required' => $options['require_password'],
                'attr' => ['autocomplete' => 'new-password'],
                'label' => 'Password',
                'empty_data' => '',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'require_password' => true,
        ]);
    }
}
