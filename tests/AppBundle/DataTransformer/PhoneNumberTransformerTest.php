<?php
namespace Tests\AppBundle\DataTransformer;

use AppBundle\DataTransformer\PhoneNumberTransformer;
use PHPUnit\Framework\TestCase;


class PhoneNumberTransformerTest extends TestCase
{
    public function testTransforms()
    {
        $cases = array(
            "0499549055",
            "0499 54 90 55",
            "+32499549055",
            "+32 499 54 90 55",
            "+32 (0)499 54 90 55",
            "+32 0499/549055",
            "0032499549055",
            "0032 499 54 90 55",
            "0032 (0)499 54 90 55",
            "0032 0499/549055"
        );

        $t = new PhoneNumberTransformer();

        foreach($cases as $case) {
            $parsed = $t->reverseTransform($case);
            $formatted = $t->transform($parsed);

            $this->assertEquals("+32499549055", $parsed);
            $this->assertEquals("+32 499 54 90 55", $formatted);
        }
    }
}