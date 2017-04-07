<?php
namespace Tests\SelExchangeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\StringInput;

class ExchangeControllerTest extends WebTestCase
{
    private $client = null;
    protected static $application;

    public function setUp()
    {
        parent::setUp();

        self::runCommand('doctrine:database:create');
        self::runCommand('doctrine:schema:update --force');
        self::runCommand('doctrine:fixtures:load --no-interaction');

        $this->client = static::createClient();
    }

    protected static function runCommand($command)
    {
        $command = sprintf('%s --quiet', $command);

        return self::getApplication()->run(new StringInput($command));
    }

    protected static function getApplication()
    {
        if (null === self::$application) {
            $client = static::createClient();

            self::$application = new Application($client->getKernel());
            self::$application->setAutoExit(false);
        }

        return self::$application;
    }

    private function logIn($username)
    {
        $firewall = 'secured';

        $em = $this->client->getContainer()->get('doctrine')->getManager();
        $user = $em->getRepository('AppBundle:User')->findOneByUsername($username);

        $token = new UsernamePasswordToken($user, $user->getPassword(), $firewall, $user->getRoles());

        $session = $this->client->getContainer()->get('session');
        $session->set('_security_'.$firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }

    public function testNewExchange()
    {
        $this->login('admin', 'ROLE_ADMIN');
        $crawler = $this->client->request('GET', '/echanges/ajouter');

        $this->assertEquals(
            "Nouvel échange",
            $crawler->filter('.page-header h1')->text(),
            "Le titre de la page Nouvel échange est 'Nouvel échange'"
        );
    }
}