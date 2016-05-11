<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Entity\User;
use Symfony\Component\Validator\Constraints\DateTime;

class LoadUserData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $data = [
            [
                "name" => "Bob",
                "mail" => "bob@example.com",
                "locked" => false,
                "balance" => 50.00
            ],
            [
                "name" => "Jean",
                "mail" => "jean@example.com",
                "locked" => false,
                "balance" => 32.25,
                "phone" => "021234567",
                "mobile" => "0499123456",
                "street" => "Rue de l'église",
                "street_number" => "12",
                "street_box" => "3",
                "zip" => "1000",
                "city" => "Bruxelles"
            ],
            [
                "name" => "Jacques",
                "mail" => "jacques@example.com",
                "locked" => false,
                "balance" => 40.75,
                "phone" => "022345678",
                "mobile" => "0499234567",
                "street" => "BD. de l'empereur",
                "street_number" => "22b",
                "zip" => "1000",
                "city" => "Bruxelles"
            ],
            [
                "name" => "Anne",
                "mail" => "anne@example.com",
                "locked" => false,
                "balance" => 49.25,
                "mobile" => "0499345678",
                "zip" => "1000",
                "city" => "Bruxelles"
            ],
            [
                "name" => "Zoé",
                "mail" => "zoe@example.com",
                "locked" => false,
                "balance" => 33.00,
                "phone" => "023456789"
            ],
            [
                "name" => "Michaël",
                "mail" => "michael@example.com",
                "locked" => false,
                "balance" => 21.5,
                "mobile" => "0499456789"
            ],
            [
                "name" => "Locked",
                "mail" => "locked@example.com",
                "locked" => true,
                "balance" => 12.75
            ]
        ];
        
        $userManager = $this->container->get('fos_user.user_manager');
        $encoder = $this->container->get('security.password_encoder');

        $userAdmin = $userManager->createUser();

        $userAdmin->setUsername('admin');
        $userAdmin->setEmail('admin@example.com');
        $userAdmin->setEnabled(true);
        $userAdmin->setSuperAdmin(true);


        $password = $encoder->encodePassword($userAdmin, 'selesel');
        $userAdmin->setPassword($password);


        $userManager->updateUser($userAdmin);

        foreach($data as $u) {
            $user = $userManager->createUser();

            $user->setUsername($u["name"]);
            $user->setEmail($u["mail"]);
            $user->setEnabled(true);

            $password = $encoder->encodePassword($user, 'pass');
            $user->setPassword($password);

            $user->setLocked($u["locked"]);
            if (isset($u["balance"]))
                $user->setBalance($u["balance"]);
            if (isset($u["phone"]))
                $user->setPhone($u["phone"]);
            if (isset($u["mobile"]))
                $user->setMobile($u["mobile"]);
            if (isset($u["street"]))
                $user->setStreet($u["street"]);
            if (isset($u["street_number"]))
                $user->setStreetNumber($u["street_number"]);
            if (isset($u["street_box"]))
                $user->setStreetBox($u["street_box"]);
            if (isset($u["zip"]))
                $user->setZip($u["zip"]);
            if (isset($u["city"]))
                $user->setCity($u["city"]);

            $userManager->updateUser($user);

            $this->addReference($u["name"] . '-user', $user);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        // the order in which fixtures will be loaded
        // the lower the number, the sooner that this fixture is loaded
        return 1;
    }
}
