<?php

namespace SelCategoryBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SelCategoryBundle\Entity\Category;

class LoadCategoryData extends AbstractFixture implements OrderedFixtureInterface
{
    private $indexes = 0;
    private function addCategories(ObjectManager $manager, $src, $parent = null) {
        foreach ($src as $cat){
            $item = new Category();

            if (is_array($cat)) {
                $item->setTitle($cat["title"]);
            } else {
                $item->setTitle($cat);
            }

            if ($parent !== null) {
                $item->setParent($parent);
            }

            $manager->persist($item);

            $this->addReference('category' . $this->indexes, $item);
            $this->indexes++;

            if (is_array($cat) && $cat["child"]) {
                $this->addCategories($manager, $cat["child"], $item);
            }
        }
    }

    public function load(ObjectManager $manager)
    {
        $items = [
            [
                "title" => "Administratif et travaux de bureau", // 0
                "child" => [
                    "Aide administrative, sociale ou juridique", // 1
                    "Informatique: hardware", // 2
                    "Informatique: software", // 3
                    "Job coaching", // 4
                    "Langues et traductions" // 5
                ]
            ],
            [
                "title" => "Alimentation", // 6
                "child" => [
                    "Fêtes", // 7
                    "Gastronomie et Œnologie", // 8
                    "Quotidien" // 9
                ]
            ],
            [
                "title" => "Animaux", // 10
                "child" => [
                    "Gardiennage" // 11
                ]
            ],
            [
                "title" => "Bien-être et loisirs", // 12
                "child" => [
                    "Créativité, art et artisanat", // 13
                    "Détente, relaxation, santé", // 14
                    "Multimédia, audiovisuel", // 15
                    "Musique, cinéma, BD et livres", // 16
                    "Nature", // 17
                    "Sport" // 18
                ]
            ],
            [
                "title" => "Bricolage", // 19
                "child" => [
                    "Bois et menuiserie", // 20
                    "Electricité, électroménager, soudure", // 21
                    "Gros œuvre", // 22
                    "Main d'œuvre", // 23
                    "Mécanique", // 24
                    "Peinture et déco" // 25
                ]
            ],
            [
                "title" => "Enfance", // 26
                "child" => [
                    "Babysitting", // 27
                    "Cours et apprentissages", // 28
                    "Loisirs" // 29
                ]
            ],
            [
                "title" => "Jardin", // 30
                "child" => [
                    "Jardin d'agrément", // 31
                    "Potager et verger" // 32
                ]
            ],
            [
                "title" => "Maison et ménage", // 33
                "child" => [
                    "Courses", // 34
                    "Couture, tricot, repassage et broderie", // 35
                    "Gardiennage de maison" // 36
                ]
            ],
            [
                "title" => "Mobilité", // 37
                "child" => [
                    "Transport de marchandises", // 38
                    "Transport de personnes" // 39
                ]
            ],
            "Pouvoir d'achat" // 40
        ];

        $this->addCategories($manager, $items);

        $manager->flush();
    }

    public function getOrder()
    {
        // the order in which fixtures will be loaded
        // the lower the number, the sooner that this fixture is loaded
        return 2;
    }
}