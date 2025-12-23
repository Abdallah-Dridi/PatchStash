<?php

namespace App\Form;

use App\Entity\Asset;
use App\Entity\Module;
use App\Entity\PatchCycle;
use App\Entity\Vulnerability;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Repository\VulnerabilityRepository;

class PatchCycleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('cycleId')
            ->add('status')
            ->add('description')
            ->add('deadline', \Symfony\Component\Form\Extension\Core\Type\DateType::class, [
                'widget' => 'single_text',
                'html5' => true,
            ])
            ->add('appliedDate', \Symfony\Component\Form\Extension\Core\Type\DateType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'required' => false,
            ])
            ->add('cvss')
            ->add('info')
            ->add('module', EntityType::class, [
                'class' => Module::class,
                'choice_label' => 'name',
                'required' => false,
                'placeholder' => 'Select a module...',
            ])
            ->add('vulnerabilities', EntityType::class, [
                'class' => Vulnerability::class,
                'choice_label' => function(Vulnerability $vulnerability) {
                    return sprintf('%s - %s (CVSS: %s)', $vulnerability->getCveId(), $vulnerability->getName(), $vulnerability->getCvss());
                },
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'query_builder' => function(VulnerabilityRepository $repo) {
                    return $repo->createQueryBuilder('v')
                        ->orderBy('v.cvss', 'DESC');
                },
            ])
            ->add('asset', EntityType::class, [
                'class' => Asset::class,
                'choice_label' => 'name',
                'required' => false,
                'placeholder' => 'Select an asset (optional)...',
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

