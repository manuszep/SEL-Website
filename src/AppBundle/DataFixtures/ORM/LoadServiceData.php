<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Entity\User;
use AppBundle\Entity\Service;

class LoadServiceData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
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
                "title" => "Soutien et conseil au portage des bébés et petits enfants",
                "user" => $this->getReference('Bob-user'),
                "body" => "<p>Je serai ravie de conseiller et soutenir les papas et les mamans pour le portage de leur enfant en écharpe. Je peux également prêter une écharpe de portage, et coudre à vos mesures un hamac de portage (écharpe super simplifiée super pratique pour le portage à la maison par exemple). Mal de dos absolument non compris dans la proposition, mais bonheur de toute la famille garanti !!</p>",
                "type" => 1,
                "domain" => 1,
                "promote" => false,
                "expires_at" => null,
                "picture_path" => "571e85c7367e7.jpg",
                "category" => $this->getReference('category29')
            ],
            [
                "title" => "Conseils juridiques",
                "user" => $this->getReference("Jean-user"),
                "body" => "<p>Je peux vous donner un conseil d'ordre juridique, un avis sur un dossier, aider à clarifier une situation ou rédiger des documents. Je suis bien entendu juriste et j'ai été avocat pendant une quinzaine d'années. La confidentialité et l'indépendance font toujours partie de mes règles d'action.</p>",
                "type" => 2,
                "domain" => 1,
                "promote" => false,
                "expires_at" => null,
                "picture_path" => "571e7a3352545.gif",
                "category" => $this->getReference('category1')
            ],
            [
                "title" => "Pommes à donner",
                "user" => $this->getReference("Zoé-user"),
                "body" => "<p>J'ai des pommes à donner. La saison ayant été excellente, n'hésitez pas à venir nombreux !</p>",
                "type" => 3,
                "domain" => 2,
                "promote" => false,
                "expires_at" => null,
                "picture_path" => "571f9697e7784.jpeg",
                "category" => $this->getReference('category9')
            ]
        ];

        foreach($data as $s) {
            $service = new Service();
            
            $service->setTitle($s["title"]);
            $service->setUser($s["user"]);
            $service->setBody($s["body"]);
            $service->setType($s["type"]);
            $service->setDomain($s["domain"]);
            $service->setpromote($s["promote"]);
            $service->setExpiresAt($s["expires_at"]);
            $service->setPicturePath($s["picture_path"]);
            $service->setCategory($s["category"]);

            $manager->persist($service);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        // the order in which fixtures will be loaded
        // the lower the number, the sooner that this fixture is loaded
        return 3;
    }
}