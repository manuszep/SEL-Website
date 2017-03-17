<?php
namespace Tests\AppBundle\DataTransformer;

use AppBundle\DataTransformer\CommaToDotTransformer;
use PHPUnit\Framework\TestCase;


class CommaToDotTransformerTest extends TestCase
{
    public function testTransforms()
    {
        $t = new CommaToDotTransformer();

        $this->assertEquals("10", $t->reverseTransform("10"));
        $this->assertEquals("10.1", $t->reverseTransform("10.1"));
        $this->assertEquals("10.1", $t->reverseTransform("10,1"));
    }
}