<?php

namespace AppBundle\Controller;

use SelServiceBundle\Entity\ServiceManager;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
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

        if ($user instanceof User && $user->getLastLogin()) {
            $limit = $user->getLastLogin();
        } else {
            $now = new \DateTime();
            $limit = $now->modify('- 2 weeks');
        }

        $flash_services = $service_manager->findAllFlash(true);
        $normal_services = $service_manager->findAllNormal(true, $limit);

        $exchange_qb = $em->getRepository('SelExchangeBundle:Exchange')->createQueryBuilder('e');

        $exchange_qb->where(
                $exchange_qb->expr()->gt('e.updated', ':limit')
            )
            ->andWhere(
                'e.hide = 0 OR e.hide is null'
            )
            ->setParameters(array(
                'limit' => $limit->format("Y-m-d H:i:s")
            ));

        $exchanges = $exchange_qb->getQuery()->getResult();

        $journal = array_merge($normal_services, $exchanges);

        usort(
            $journal,
            function($a, $b) {
                if ($a->getUpdated() === $b->getUpdated()) {
                    return 0;
                }

                return ($a->getUpdated() < $b->getUpdated())? 1 : -1;
            }
        );

        return $this->render('default/index.html.twig', [
            'flash_services' => $flash_services,
            'journal' => $journal,
            'user' => $user
        ]);
    }
}
