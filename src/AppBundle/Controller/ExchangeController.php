<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Exchange;
use AppBundle\Form\ExchangeType;

/**
 * Exchange controller.
 *
 * @Route("/echanges")
 */
class ExchangeController extends Controller
{
    /**
     * Lists all Exchange entities.
     *
     * @Route("/", name="exchange_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $exchanges = $em->getRepository('AppBundle:Exchange')->findAll();

        return $this->render('exchange/index.html.twig', array(
            'exchanges' => $exchanges,
        ));
    }

    /**
     * Creates a new Exchange entity.
     *
     * @Route("/ajouter", name="exchange_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $exchange = new Exchange();
        $form = $this->createForm('AppBundle\Form\ExchangeType', $exchange);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($exchange);
            $em->flush();

            return $this->redirectToRoute('exchange_show', array('id' => $exchange->getId()));
        }

        return $this->render('exchange/new.html.twig', array(
            'exchange' => $exchange,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Exchange entity.
     *
     * @Route("/{id}", name="exchange_show")
     * @Method("GET")
     */
    public function showAction(Exchange $exchange)
    {
        $deleteForm = $this->createDeleteForm($exchange);

        return $this->render('exchange/show.html.twig', array(
            'exchange' => $exchange,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Exchange entity.
     *
     * @Route("/{id}/modifier", name="exchange_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Exchange $exchange)
    {
        $deleteForm = $this->createDeleteForm($exchange);
        $editForm = $this->createForm('AppBundle\Form\ExchangeType', $exchange);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($exchange);
            $em->flush();

            return $this->redirectToRoute('exchange_edit', array('id' => $exchange->getId()));
        }

        return $this->render('exchange/edit.html.twig', array(
            'exchange' => $exchange,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Exchange entity.
     *
     * @Route("/{id}", name="exchange_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Exchange $exchange)
    {
        $form = $this->createDeleteForm($exchange);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($exchange);
            $em->flush();
        }

        return $this->redirectToRoute('exchange_index');
    }

    /**
     * Creates a form to delete a Exchange entity.
     *
     * @param Exchange $exchange The Exchange entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Exchange $exchange)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('exchange_delete', array('id' => $exchange->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
