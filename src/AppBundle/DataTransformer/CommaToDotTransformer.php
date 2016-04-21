<?php

namespace AppBundle\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/* Allow using comma, when only dot is accepted by locale as decimal separator */
class CommaToDotTransformer implements DataTransformerInterface
{
    public function transform($number)
    {
        return $number;
    }

    public function reverseTransform($input)
    {
        return str_replace(',', '.', $input);
    }
}
