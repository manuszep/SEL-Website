<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Exchange;
use AppBundle\Entity\User;
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
     * Lists all Exchange entities for a user.
     *
     * @param User $user
     * @param boolean $partial
     * @Method("GET")
     */
    public function listForUserAction(User $user, $partial) {
        $em = $this->getDoctrine()->getManager();

        $qb = $em->getRepository('AppBundle:Exchange')->createQueryBuilder('e');
        $exchanges = $qb
            ->where($qb->expr()->orX(
                $qb->expr()->eq('e.creditUser', ':cu'),
                $qb->expr()->eq('e.debitUser', ':du')
            ))
            ->setParameters(array('cu' => $user->getId(), 'du' => $user->getId()))
            ->orderBy('e.created', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('exchange/listForUser.html.twig', array(
            'user' => $user,
            'exchanges' => $exchanges,
            'partial' => $partial
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
        $error = false;

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$exchange->getDebitUser()) {
                $exchange->setDebitUser($this->getUser());
            }

            if ($exchange->getCreditUser()->getId() == $exchange->getDebitUser()->getId()) {
                $this->addFlash(
                    'error',
                    'Le donneur et le receveur doivent être des utilisateurs différents.'
                );
                $error = true;
            }
            
            if (!$error) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($exchange);
                $em->flush();

                return $this->redirectToRoute('exchange_index');
            }
        }

        return $this->render('exchange/new.html.twig', array(
            'exchange' => $exchange,
            'form' => $form->createView(),
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
            $error = false;

            if (!$exchange->getDebitUser()) {
                $exchange->setDebitUser($this->getUser());
            }

            if ($exchange->getCreditUser()->getId() == $exchange->getDebitUser()->getId()) {
                $this->addFlash(
                    'error',
                    'Le donneur et le receveur doivent être des utilisateurs différents.'
                );
                $error = true;
            }
            if (!$error) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($exchange);
                $em->flush();

                return $this->redirectToRoute('exchange_edit', array('id' => $exchange->getId()));
            }
        }

        return $this->render('exchange/edit.html.twig', array(
            'exchange' => $exchange,
            'form' => $editForm->createView(),
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
