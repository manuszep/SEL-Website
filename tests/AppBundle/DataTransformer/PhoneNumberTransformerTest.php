<?php
namespace Tests\AppBundle\DataTransformer;

use AppBundle\DataTransformer\PhoneNumberTransformer;
use PHPUnit\Framework\TestCase;


class PhoneNumberTransformerTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->transformer = new PhoneNumberTransformer();
    }

    public function testvalidPhoneNumber()
    {
        $t = new PhoneNumberTransformer();

        $this->assertEquals(true, $t->validPhoneNumber(''));

        $this->assertEquals('fixe', $t->validPhoneNumber('02 123 45 67'));
        $this->assertEquals('fixe', $t->validPhoneNumber('021234567'));
        $this->assertEquals('fixe', $t->validPhoneNumber('02/123.45.67'));

        $this->assertEquals('fixe', $t->validPhoneNumber('+32 2 123 45 67'));
        $this->assertEquals('fixe', $t->validPhoneNumber('+3221234567'));
        $this->assertEquals('fixe', $t->validPhoneNumber('+32 (0)2/123.45.67'));

        $this->assertEquals('fixe', $t->validPhoneNumber('0032 2 123 45 67'));
        $this->assertEquals('fixe', $t->validPhoneNumber('003221234567'));
        $this->assertEquals('fixe', $t->validPhoneNumber('0032 (0)2/123.45.67'));

        $this->assertEquals('mobile', $t->validPhoneNumber('0477 12 34 56'));
        $this->assertEquals('mobile', $t->validPhoneNumber('0477123456'));
        $this->assertEquals('mobile', $t->validPhoneNumber('0477/12.34.56'));

        $this->assertEquals('mobile', $t->validPhoneNumber('+32 477 12 34 56'));
        $this->assertEquals('mobile', $t->validPhoneNumber('+32477123456'));
        $this->assertEquals('mobile', $t->validPhoneNumber('+32 (0)477/12.34.56'));

        $this->assertEquals('mobile', $t->validPhoneNumber('0032 477 12 34 56'));
        $this->assertEquals('mobile', $t->validPhoneNumber('0032477123456'));
        $this->assertEquals('mobile', $t->validPhoneNumber('0032 (0)477/12.34.56'));


        $this->assertEquals(false, $t->validPhoneNumber('02 123 45 678'));
        $this->assertEquals(false, $t->validPhoneNumber('02 123 45 6'));
        $this->assertEquals(false, $t->validPhoneNumber('11 111 11 11'));
        $this->assertEquals(false, $t->validPhoneNumber('0477 12 34 56 78'));
        $this->assertEquals(false, $t->validPhoneNumber('0477 12 34'));
        $this->assertEquals(false, $t->validPhoneNumber('+3 2 123 45 67'));
        $this->assertEquals(false, $t->validPhoneNumber('003 2 123 45 67'));
        $this->assertEquals(false, $t->validPhoneNumber('+3 477 12 34 56'));
        $this->assertEquals(false, $t->validPhoneNumber('003 477 12 34 56'));
    }

    protected function transform($expected1, $expected2, $input) {
        $parsed = $this->transformer->reverseTransform($input);
        $formatted = $this->transformer->transform($parsed);

        $this->assertEquals($expected1, $parsed);
        $this->assertEquals($expected2, $formatted);
    }

    public function testTransforms()
    {
        //$this->$this->testTransform('', '', '');

        $this->transform('+3221234567', '+32 2 123 45 67', '02 123 45 67');
        $this->transform('+3221234567', '+32 2 123 45 67', '021234567');
        $this->transform('+3221234567', '+32 2 123 45 67', '02/123.45.67');

        $this->transform('+3221234567', '+32 2 123 45 67', '+32 2 123 45 67');
        $this->transform('+3221234567', '+32 2 123 45 67', '+3221234567');
        $this->transform('+3221234567', '+32 2 123 45 67', '+32 (0)2/123.45.67');

        $this->transform('+3221234567', '+32 2 123 45 67', '0032 2 123 45 67');
        $this->transform('+3221234567', '+32 2 123 45 67', '003221234567');
        $this->transform('+3221234567', '+32 2 123 45 67', '0032 (0)2/123.45.67');


        $this->transform('+32477123456', '+32 477 12 34 56', '0477 12 34 56');
        $this->transform('+32477123456', '+32 477 12 34 56', '0477123456');
        $this->transform('+32477123456', '+32 477 12 34 56', '0477/12.34.56');

        $this->transform('+32477123456', '+32 477 12 34 56', '+32 477 12 34 56');
        $this->transform('+32477123456', '+32 477 12 34 56', '+32477123456');
        $this->transform('+32477123456', '+32 477 12 34 56', '+32 (0)477/12.34.56');

        $this->transform('+32477123456', '+32 477 12 34 56', '0032 477 12 34 56');
        $this->transform('+32477123456', '+32 477 12 34 56', '0032477123456');
        $this->transform('+32477123456', '+32 477 12 34 56', '0032 (0)477/12.34.56');
    }
}