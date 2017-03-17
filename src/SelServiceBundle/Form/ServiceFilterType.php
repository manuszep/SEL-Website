<?php

namespace SelServiceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;

class ServiceFilterType extends AbstractType
{
    private $serviceTypes;
    private $serviceDomains;

    public function __construct($serviceTypes, $serviceDomains)
    {
        $this->serviceTypes = $serviceTypes;
        $this->serviceDomains = $serviceDomains;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $types = array_flip($this->serviceTypes);
        $domains = array_flip($this->serviceDomains);

        $builder->add('category', Filters\EntityFilterType::class, array(
            'class' => 'SelCategoryBundle:Category',
            'choice_label' => 'select_label',
            'placeholder' => 'placeholder.pleaseChoose',
            'label' => 'label.category'
        ));
        $builder->add('type', Filters\ChoiceFilterType::class, array(
            'choices'  => $types,
            'multiple' => true,
            'expanded' => true,
            'label' => 'label.serviceType'
        ));
        $builder->add('domain', Filters\ChoiceFilterType::class, array(
            'choices'  => $domains,
            'multiple' => true,
            'expanded' => true,
            'label' => 'label.domain'
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
