<?php
namespace Tests\AppBundle\EventListener;

use AppBundle\EventListener\BalanceKeeperListener;
use SelExchangeBundle\Entity\Exchange;
use Doctrine\ORM\EntityRepository;
use Liip\FunctionalTestBundle\Test\WebTestCase;


class BalanceKeeperTest extends WebTestCase
{
    private $em;
    private $um;
    private $repository;
    private $user_repository;
    private $balance_keeper;

    public function setUp()
    {
        parent::setUp();
        self::bootKernel();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->um = static::$kernel->getContainer()
            ->get('fos_user.user_manager');

        $this->repository = $this->em->getRepository(Exchange::class);

        $fixtures = $this->loadFixtures(array(
            'AppBundle\DataFixtures\ORM\LoadUserData',
        ));

        $this->user_repository = $fixtures->getReferenceRepository();
        $this->balance_keeper = new BalanceKeeperListener();
    }

    public function testExchangePersist()
    {
        $e = new Exchange();
        $u1 = $this->um->findUserByUsername('Bob');
        $u2 = $this->um->findUserByUsername('Jean');

        $u1_initial_balance = $u1->getBalance();
        $u2_initial_balance = $u2->getBalance();

        $e->setTitle("Test1");
        $e->setCreditUser($u1);
        $e->setDebitUser($u2);
        $e->setMessage("Message");
        $e->setAmount(10);

        $this->em->persist($e);
        $this->em->flush();

        $this->assertEquals($u1_initial_balance + 10, $u1->getBalance());
        $this->assertEquals($u2_initial_balance - 10, $u2->getBalance());

        $e->setAmount(5);

        $this->em->persist($e);
        $this->em->flush();

        $this->assertEquals($u1_initial_balance + 5, $u1->getBalance());
        $this->assertEquals($u2_initial_balance - 5, $u2->getBalance());

        $this->em->remove($e);
        $this->em->flush();

        $this->assertEquals($u1_initial_balance, $u1->getBalance());
        $this->assertEquals($u2_initial_balance, $u2->getBalance());
    }
}