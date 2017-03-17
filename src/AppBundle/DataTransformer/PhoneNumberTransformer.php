<?php

namespace AppBundle\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class PhoneNumberTransformer implements DataTransformerInterface
{
    public function validPhoneNumber($number) {
        $number = $this->reverseTransform($number);
        $ret = FALSE;

        if ($number == "") {
            return TRUE;
        }

        $pattern = '/^((\+|00)32\s?|0)(\d\s?\d{3}|\d{2}\s?\d{2})(\s?\d{2}){2}$/';
        $pattern_mobile = '/^((\+|00)32\s?|0)4(60|[789]\d)(\s?\d{2}){3}$/';

        if (preg_match($pattern, $number, $matches)) {
            $ret = "fixe";
        } else if (preg_match($pattern_mobile, $number, $matches)) {
            $ret = "mobile";
        }

        return $ret;
    }

    public function transform($number)
    {
        $number = trim($number);
        $valid_phone_number = $this->validPhoneNumber($number);

        if ($valid_phone_number !== FALSE && $number !== "") {
            if ($valid_phone_number == 'mobile') {
                $pattern = '/^(\+\d{2})(\d{3})(\d{2})(\d{2})(\d{2})$/i';
            } else {
                $pattern = '/^(?|(0[2349])\s?(\d{3})|(\d{3})\s?(\d{2}))\s?(\d{2})\s?(\d{2})$/i';
            }

            preg_match($pattern, $number, $matches);
            $number = $matches[1] . ' ' . $matches[2] . ' ' . $matches[3] . ' ' . $matches[4] . ' ' . $matches[5];
        }

        return $number;
    }

    public function reverseTransform($input)
    {
        if (!$input || $input == "") {
            return "";
        }
        $number = preg_replace('/[^0-9+]/', '', $input);
        $number = preg_replace('/^00/', '+', $number);
        $number = preg_replace('/^0/', '', $number);

        if (substr($number, 3, 1) == "0") {
            $number = substr($number, 0, 3) . substr($number, 4);
        }

        if (substr($number, 0, 1) != '+') {
            $number = '+32' . $number;
        }

        return $number;
    }
}
