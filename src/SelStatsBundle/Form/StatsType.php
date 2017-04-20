<?php

namespace SelStatsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;

class StatsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('period', Filters\ChoiceFilterType::class, array(
            'choices'  => array(
                '15 jours' => 15,
                '30 jours' => 30,
                '60 jours' => 60,
                '90 jours' => 90,
                '6 mois' => 183,
            ),
            'multiple' => false,
            'expanded' => true,
            'label' => 'label.statsPeriod',
            'placeholder' => false,
            'data' => 30
        ));
    }

    public function getBlockPrefix()
    {
        return 'stats_filter';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection'   => false,
            'validation_groups' => array('filtering') // avoid NotBlank() constraint-related message
        ));
    }
}
