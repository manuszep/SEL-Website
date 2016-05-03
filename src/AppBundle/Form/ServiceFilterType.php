<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;
use AppBundle\Entity\Service;

class ServiceFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $serivce = new Service();

        $types = array_flip($serivce->getTypes());
        $domains = array_flip($serivce->getDomains());

        $builder->add('category', Filters\EntityFilterType::class, array(
            'class' => 'AppBundle:Category',
            'choice_label' => 'select_label',
            'placeholder' => 'Catégorie'
        ));
        $builder->add('type', Filters\ChoiceFilterType::class, array(
            'choices'  => $types,
            'multiple' => true,
            'expanded' => true
        ));
        $builder->add('domain', Filters\ChoiceFilterType::class, array(
            'choices'  => $domains,
            'multiple' => true,
            'expanded' => true
        ));
    }

    public function getBlockPrefix()
    {
        return 'service_filter';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection'   => false,
            'validation_groups' => array('filtering') // avoid NotBlank() constraint-related message
        ));
    }
}