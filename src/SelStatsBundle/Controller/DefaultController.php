<?php

namespace SelStatsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/stats")
     */
    public function indexAction()
    {
        $um = $this->get('fos_user.user_manager');
        $users = $um->findUsers();

        $em = $this->getDoctrine()->getManager();
        $exchanges = $em->getRepository('SelExchangeBundle:Exchange')->findAll();


        return $this->render('SelStatsBundle:Default:index.html.twig', array(
            'users' => $users,
            'exchanges' => $exchanges
        ));
    }
}
