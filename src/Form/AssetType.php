<?php

namespace App\Form;

use App\Entity\Asset;
use App\Entity\Module;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AssetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('type')
            ->add('environment')
            ->add('status')
            ->add('softwareName', null, [
                'label' => 'Software Component (e.g. apache)',
                'required' => false,
            ])
            ->add('softwareVersion', null, [
                'label' => 'Software Version (e.g. 2.4.49)',
                'required' => false,
            ])
            ->add('info')
            ->add('module', EntityType::class, [
                'class' => Module::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Asset::class,
        ]);
    }
}
