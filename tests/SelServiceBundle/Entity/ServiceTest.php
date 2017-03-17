<?php
namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Service;
use AppBundle\Entity\User;
use PHPUnit\Framework\TestCase;


class ServiceTest extends TestCase
{
    private $s = null;
    private $u = null;

    public function setUp()
    {
        $this->s = new Service();
        $this->u = new User();

        $this->s->setTypesList(array(
            1 => "Offre",
            2 => "Demande",
            3 => "Offre Flash",
            4 => "Demande Flash"
        ));

        $this->s->setDomainsList(array(
            1 => "Service / savoir",
            2 => "Preterie / donnerie"
        ));
    }

    public function testServicesTypeLabel()
    {
        $this->s->setType(1);
        $this->assertEquals("Offre", $this->s->getTypeLabel());
        $this->s->setType(2);
        $this->assertEquals("Demande", $this->s->getTypeLabel());
        $this->s->setType(3);
        $this->assertEquals("Offre Flash", $this->s->getTypeLabel());
        $this->s->setType(4);
        $this->assertEquals("Demande Flash", $this->s->getTypeLabel());
    }

    public function testServicesDomainLabel()
    {
        $this->s->setDomain(1);
        $this->assertEquals("Service / savoir", $this->s->getDomainLabel());
        $this->s->setDomain(2);
        $this->assertEquals("Preterie / donnerie", $this->s->getDomainLabel());
    }

    public function testSelectLabel()
    {
        $this->u->setUsername('User1');
        $this->s->setTitle('Service1');
        $this->s->setUser($this->u);

        $this->assertEquals("User1 - Service1", $this->s->getSelectLabel());
    }

    public function testExpired()
    {
        // ExpiresAt is null so it can't be expired
        $this->assertEquals(false, $this->s->isExpired());

        $this->s->setExpiresAt(new \DateTime('2000-01-01'));
        $this->assertEquals(true, $this->s->isExpired());

        $this->s->setExpiresAt(new \DateTime('2100-01-01'));
        $this->assertEquals(false, $this->s->isExpired());
    }
}