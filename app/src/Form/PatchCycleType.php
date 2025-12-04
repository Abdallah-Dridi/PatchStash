<?php

namespace App\Form;

use App\Entity\Asset;
use App\Entity\PatchCycle;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PatchCycleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('cycleId')
            ->add('status')
            ->add('description')
            ->add('deadline')
            ->add('appliedDate')
            ->add('cvss')
            ->add('info')
            ->add('asset', EntityType::class, [
                'class' => Asset::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PatchCycle::class,
        ]);
    }
}
