<?php

namespace AppBundle\Controller;

use SelServiceBundle\Entity\ServiceManager;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    private function getLimit($user, $limit = false) {
        if (!$limit) {
            // Set a time limit if user is anonymous
            // Set a limit at last login otherwise
            if ($user instanceof User && $user->getLastLogin()) {
                return $user->getLastLogin();
            } else {
                $now = new \DateTime();
                return $now->modify('- 2 weeks');
            }
        } else {
            return $limit;
        }
    }

    private function getUsers($em, $limit) {
        $user_qb = $em->getRepository('AppBundle:User')->createQueryBuilder('u');

        if ($limit instanceof \DateTime) {
            $user_qb->where(
                $user_qb->expr()->gt('u.created', ':limit')
            )
                ->setParameters(array(
                    'limit' => $limit->format("Y-m-d H:i:s")
                ));
        } else {
            $user_qb->setMaxResults($limit);
        }

        $user_qb->andWhere(
            'u.locked = 0 OR u.locked is null'
        )
        ->orderBy('u.created', 'DESC');

        return $user_qb->getQuery()->getResult();
    }

    private function getExchanges($em, $limit) {
        $exchange_qb = $em->getRepository('SelExchangeBundle:Exchange')->createQueryBuilder('e');

        if ($limit instanceof \DateTime) {
            $exchange_qb->where(
                $exchange_qb->expr()->gt('e.updated', ':limit')
            )
            ->setParameters(array(
                'limit' => $limit->format("Y-m-d H:i:s")
            ));
        } else {
            $exchange_qb->setMaxResults($limit);
        }

        $exchange_qb->andWhere(
            'e.hide = 0 OR e.hide is null'
        )
        ->orderBy('e.updated', 'DESC');

        return $exchange_qb->getQuery()->getResult();
    }

    private function getArticles($em, $limit) {
        $articles_qb = $em->getRepository('ArticleBundle:Article')->findPublished('a', false);

        if ($limit instanceof \DateTime) {
            $articles_qb->andWhere(
                $articles_qb->expr()->orX(
                    $articles_qb->expr()->gt('a.updated', ':limit'),
                    $articles_qb->expr()->gt('a.published_at', ':limit')
                )
            )
            ->setParameter('limit', $limit->format("Y-m-d H:i:s"));
        } else {
            $articles_qb->setMaxResults($limit);
        }

        return $articles_qb->getQuery()->getResult();
    }

    private function getData($limit = false) {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var ServiceManager $service_manager */
        $service_manager = $this->container->get('sel_service.manager.service');
        /** @var User $user */
        $user= $this->getUser();

        $current_limit = $this->getLimit($user, $limit);

        // Get list of all services grouped by flash or normal
        // Flash services are not limited
        $flash_services = $service_manager->findAllFlash(true);
        $normal_services = $service_manager->findAllNormal(true, $current_limit);

        // Fetch new exchanges in the same limit
        $exchanges = $this->getExchanges($em, $current_limit);

        // Fetch new users in the same limit
        $users = $this->getUsers($em, $current_limit);

        $articles = $this->getArticles($em, $current_limit);

        $journal = array_merge($normal_services, $exchanges, $users, $articles);

        uasort($journal, function ($a, $b) {
            $a_date = (method_exists($a,'getUsername')) ? $a->getCreated() : $a->getUpdated();
            $b_date = (method_exists($b,'getUsername')) ? $b->getCreated() : $b->getUpdated();

            return ($a_date > $b_date) ? -1 : 1;
        });

        if (! $limit instanceof \DateTime) {
            $journal = array_slice($journal, 0, $limit);
        }

        return [
            'flash_services' => $flash_services,
            'journal' => $journal,
            'user' => $user
        ];
    }
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        return $this->render('default/index.html.twig', $this->getData(20));
    }

    /**
     * @Route("/feed", defaults={"_format"="xml"}, name="feed")
     */
    public function feedAction(Request $request)
    {
        return $this->render('default/feed.xml.twig', $this->getData(100));
    }
}
