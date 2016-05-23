<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ServiceManager;
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
        $service_manager = $this->container->get('app.manager.service');
        /** @var User $user */
        $user= $this->getUser();

        if ($user instanceof User && $user->getLastLogin()) {
            $limit = $user->getLastLogin();
        } else {
            $now = new \DateTime();
            $limit = $now->modify('- 2 weeks');
        }

        $flash_services = $service_manager->findAll(true, 'flash');
        $normal_services = $service_manager->findAll(true, 'normal', $limit);

        /**
         * TODO: Fetch other resources
         *
         * - In the same way as done for the normal services, fetch exchanges, events, new users, ...
         * - Merge all arrays
         * - Sort them on the updated field
         */

        $exchange_qb = $em->getRepository('AppBundle:Exchange')->createQueryBuilder('e');

        $exchange_qb->where(
                $exchange_qb->expr()->gt('e.updated', ':limit')
            )
            ->setParameter('limit', $limit->format("Y-m-d H:i:s"));

        $exchanges = $exchange_qb->getQuery()->getResult();

        $journal = array_merge($normal_services, $exchanges);

        usort(
            $journal,
            function($a, $b) {
                if ($a->getUpdated() === $b->getUpdated()) {
                    return 0;
                }

                return ($a->getUpdated() < $b->getUpdated())? -1 : 1;
            }
        );

        return $this->render('default/index.html.twig', [
            'flash_services' => $flash_services,
            'journal' => $journal,
            'user' => $user
        ]);
    }
}
