<?php

namespace SelServiceBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use SelServiceBundle\Entity\Service;

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
                "expires_at" => new \DateTime('2016-5-10'),
                "picture_path" => "571f9697e7784.jpeg",
                "category" => $this->getReference('category9')
            ],
            [
                "title" => "Transport de matériel et de personnes",
                "user" => $this->getReference("Zoé-user"),
                "body" => "<p>Bonjour,<br /><br />Nous disposons d'une skoda break (Fabia combi) qui permet de déménager quelques meubles, du matériel... ou des personnes ;-)</p><p>(/!\ pas d'attache pour remorque)</p><p>Elle nous sert assez peu en semaine, c'est pourquoi nous la mettons à disposition du SEL, du lundi au jeudi, avec ou sans chauffeur.</p>",
                "type" => 1,
                "domain" => 1,
                "promote" => false,
                "expires_at" => null,
                "picture_path" => null,
                "category" => $this->getReference('category37')
            ],
            [
                "title" => "Problème informatique (hardware ou software)",
                "user" => $this->getReference("Michaël-user"),
                "body" => "<p>Je vous propose mes services d'assistance informatique.</p><p>Quelques exemples de prestations:</p><ul><li>Depannage informatique pc fixe et portable (Réinstallation et réparation du Windows, os...etc) les Mac je connais moins</li><li>Suppression de virus, spyware, malware, trojan...etc</li><li>Installation de périphériques (Imprimantes, webcam...)</li><li>Formation et initiation à l'informatique</li></ul>",
                "type" => 1,
                "domain" => 1,
                "promote" => false,
                "expires_at" => null,
                "picture_path" => null,
                "category" => $this->getReference('category2')
            ],
            [
                "title" => "Portage bébé en écharpe",
                "user" => $this->getReference("Michaël-user"),
                "body" => "<p>Je vous apprends à bien intaller votre bébé dans une écharpe de portage... En toute sécurité et tout confort pour le porteur comme pour le bébé porté. Pratique et confortable de 0 à 3 ans.</p><p>Conseils quant au choix du type porte-bébé et plusieurs modèles dispo à tester. </p>",
                "type" => 1,
                "domain" => 1,
                "promote" => false,
                "expires_at" => null,
                "picture_path" => null,
                "category" => $this->getReference('category26')
            ],
            [
                "title" => "Déménagement (encore)",
                "user" => $this->getReference("Jean-user"),
                "body" => "<p>Modus/Marianne et Jos. déménagent ce Samedi 04/01/2014</p><p>Appel à \"Monsieur Propre\" (Madame convient aussi !) est fait pour un nettoyage du Bd de l'Est, 25 à Ath, forcément après le déménagement... dates à convenir dans les deux semaines qui suivent.</p><p>Déjà un grand merci aux volontaires.</p>",
                "type" => 4,
                "domain" => 1,
                "promote" => false,
                "expires_at" => new \DateTime('2016-5-15'),
                "picture_path" => null,
                "category" => $this->getReference('category33')
            ],
            [
                "title" => "Ma cheminée fume et l'hiver approche...",
                "user" => $this->getReference("Jean-user"),
                "body" => "<p>J'aimerais cet hiver réutiliser ma cheminée mais mon salon devient vite tout gris ! La cheminée a été ramonée, je ne sais donc pas d'où vient le problème... qui peut m'aider ?</p>",
                "type" => 4,
                "domain" => 1,
                "promote" => false,
                "expires_at" => new \DateTime('2016-5-15'),
                "picture_path" => null,
                "category" => $this->getReference('category23')
            ],
            [
                "title" => "Pret de machines",
                "user" => $this->getReference("Jean-user"),
                "body" => "<p>Pret de machine tel que foreuse, visseuse, ponceuse, débroussailleuse, outillage,...</p>",
                "type" => 1,
                "domain" => 2,
                "promote" => false,
                "expires_at" => null,
                "picture_path" => null,
                "category" => $this->getReference('category19')
            ],
            [
                "title" => "Aide pour le déménagement",
                "user" => $this->getReference("Jacques-user"),
                "body" => "<p>Quelques bras supplémentaires pour vous aider à déménager, mettre en caisse, trier, porter...</p>",
                "type" => 1,
                "domain" => 1,
                "promote" => false,
                "expires_at" => null,
                "picture_path" => null,
                "category" => $this->getReference('category33')
            ],
            [
                "title" => "Montage de meubles",
                "user" => $this->getReference("Jacques-user"),
                "body" => "<p>Montage ou démontage de meubles.</p>",
                "type" => 1,
                "domain" => 1,
                "promote" => false,
                "expires_at" => null,
                "picture_path" => null,
                "category" => $this->getReference('category19')
            ],
            [
                "title" => "Garde d'animaux",
                "user" => $this->getReference("Jacques-user"),
                "body" => "<p>Je m'occupe de vos petits animaux sédentaires pendant que vous êtes partis.</p><p>Poules, chats, lapins, zoizeaux, hamsters...</p><p>Pas les chiens. Je les aime mais trop de contraintes que je ne saurais assumer.</p><p>Mais j'accepte d'aller sortir et promener un chien, ponctuellement, un samedi ou un dimanche où il a fallu le laisser à la maison.</p>",
                "type" => 1,
                "domain" => 1,
                "promote" => false,
                "expires_at" => null,
                "picture_path" => null,
                "category" => $this->getReference('category11')
            ],
            [
                "title" => "Aide pour la réalisation de documents",
                "user" => $this->getReference("Jacques-user"),
                "body" => "<p>Je vous aide pour réaliser tous vos documents sur les logiciels Word (lettres, mémoires, autres docs...), powerpoint (présentations), publisher (réalisation d'un folder de publicité).</p>",
                "type" => 1,
                "domain" => 1,
                "promote" => false,
                "expires_at" => null,
                "picture_path" => null,
                "category" => $this->getReference('category0')
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