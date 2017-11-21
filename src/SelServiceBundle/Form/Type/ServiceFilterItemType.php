<?php

namespace SelServiceBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

class ServiceFilterItemType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'required' => false
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $selection = array();
        $values = $view->vars["value"];

        foreach ($form->all() as $key => $child) {
            $label = $child->getConfig()->getOption('label');
            $value = $child->getConfig()->getOption('value');

            if (in_array ($value, $values)) $selection[] = $label;
        }

        if (count($selection) < 1) {
            $view->vars['toggle_text'] = 'Tous';
        } else {
            $view->vars['toggle_text'] = join(', ', $selection);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ChoiceType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'service_filter_item';
    }
}
