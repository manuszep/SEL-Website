<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Category;

class LoadCategoryData implements FixtureInterface
{
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

            if (is_array($cat) && $cat["child"]) {
                $this->addCategories($manager, $cat["child"], $item);
            }
        }
    }

    public function load(ObjectManager $manager)
    {
        $items = [
            [
                "title" => "Administratif et travaux de bureau",
                "child" => [
                    "Aide administrative, sociale ou juridique",
                    "Informatique: hardware",
                    "Informatique: software",
                    "Job coaching",
                    "Langues et traductions"
                ]
            ],
            [
                "title" => "Alimentation",
                "child" => [
                    "Fêtes",
                    "Gastronomie et Œnologie",
                    "Quotidien"
                ]
            ],
            [
                "title" => "Animaux",
                "child" => [
                    "Gardiennage"
                ]
            ],
            [
                "title" => "Bien-être et loisirs",
                "child" => [
                    "Créativité, art et artisanat",
                    "Détente, relaxation, santé",
                    "Multimédia, audiovisuel",
                    "Musique, cinéma, BD et livres",
                    "Nature",
                    "Sport"
                ]
            ],
            [
                "title" => "Bricolage",
                "child" => [
                    "Bois et menuiserie",
                    "Electricité, électroménager, soudure",
                    "Gros œuvre",
                    "Main d'œuvre",
                    "Mécanique",
                    "Peinture et déco"
                ]
            ],
            [
                "title" => "Enfance",
                "child" => [
                    "Babysitting",
                    "Cours et apprentissages",
                    "Loisirs"
                ]
            ],
            [
                "title" => "Jardin",
                "child" => [
                    "Jardin d'agrément",
                    "Potager et verger"
                ]
            ],
            [
                "title" => "Maison et ménage",
                "child" => [
                    "Courses",
                    "Couture, tricot, repassage et broderie",
                    "Gardiennage de maison"
                ]
            ],
            [
                "title" => "Mobilité",
                "child" => [
                    "Transport de marchandises",
                    "Transport de personnes"
                ]
            ],
            "Pouvoir d'achat"
        ];

        $this->addCategories($manager, $items);

        $manager->flush();
    }
}