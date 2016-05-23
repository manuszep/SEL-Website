<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ServiceManager;
use AppBundle\Entity\User;
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
        /** @var ServiceManager $service_manager */
        $service_manager = $this->container->get('app.manager.service');
        /** @var User $user */
        $user= $this->getUser();

        if ($user instanceof User && $user->getLastLogin()) {
            $limit = $user->getLastLogin();
        } else {
            $limit = 20;
        }

        $flash_services = $service_manager->findAll(true, 'flash');

        /**
         * TODO: Replace the normal_services query with a joint query of services, exchanges, new users and events
         */
        $normal_services = $service_manager->findAll(true, 'normal', $limit);

        return $this->render('default/index.html.twig', [
            'services_groups' => array($flash_services, $normal_services),
            'user' => $user
        ]);
    }
}
