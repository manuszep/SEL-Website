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
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var ServiceManager $service_manager */
        $service_manager = $this->container->get('sel_service.manager.service');
        /** @var User $user */
        $user= $this->getUser();

        // Set a time limit if user is anonymous
        // Set a limit at last login otherwise
        if ($user instanceof User && $user->getLastLogin()) {
            $limit = $user->getLastLogin();
        } else {
            $now = new \DateTime();
            $limit = $now->modify('- 2 weeks');
        }

        // Get list of all services grouped by flash or normal
        // Flash services are not limited
        $flash_services = $service_manager->findAllFlash(true);
        $normal_services = $service_manager->findAllNormal(true, $limit);

        // Fetch new exchanges in the same limit
        $exchange_qb = $em->getRepository('SelExchangeBundle:Exchange')->createQueryBuilder('e');

        $exchange_qb->where(
                $exchange_qb->expr()->gt('e.updated', ':limit')
            )
            ->andWhere(
                'e.hide = 0 OR e.hide is null'
            )
            ->setParameters(array(
                'limit' => $limit->format("Y-m-d H:i:s")
            ))
            ->orderBy('e.updated', 'DESC');

        $exchanges = $exchange_qb->getQuery()->getResult();

        // Fetch new users in the same limit
        $user_qb = $em->getRepository('AppBundle:User')->createQueryBuilder('u');

        $user_qb->where(
            $user_qb->expr()->gt('u.created', ':limit')
        )
        ->andWhere(
            'u.locked = 0 OR u.locked is null'
        )
        ->setParameters(array(
            'limit' => $limit->format("Y-m-d H:i:s")
        ))
        ->orderBy('u.created', 'DESC');

        $users = $user_qb->getQuery()->getResult();

        $journal = array_merge($normal_services, $exchanges, $users);

        uasort($journal, function ($a, $b) {
            $a_date = (method_exists($a,'getUsername')) ? $a->getCreated() : $a->getUpdated();
            $b_date = (method_exists($b,'getUsername')) ? $b->getCreated() : $b->getUpdated();

            return ($a_date > $b_date) ? -1 : 1;
        });

        return $this->render('default/index.html.twig', [
            'flash_services' => $flash_services,
            'journal' => $journal,
            'user' => $user
        ]);
    }
}
