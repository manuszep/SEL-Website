<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Page controller.
 *
 * @Route("/")
 */
class PageController extends Controller
{
    /**
     * Show Page
     *
     * @Route("/{slug}", name="page_show")
     * @Method("GET")
     */
    public function showAction($slug)
    {
        if ( $this->get('templating')->exists('page/' . $slug . '.html.twig') ) {
            return $this->render('page/' . $slug . '.html.twig');
        }

        throw $this->createNotFoundException('Cette page n\'existe pas.');
    }
}