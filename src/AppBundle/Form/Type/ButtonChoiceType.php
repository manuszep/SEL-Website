<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;

class ButtonChoiceType extends AbstractType
{
    public function getParent()
    {
        return Filters\ChoiceFilterType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'button_choice';
    }
}
