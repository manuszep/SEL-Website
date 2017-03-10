<?php

namespace SelExchangeBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SelExchangeBundle\Entity\Exchange;
use AppBundle\Entity\User;
use SelExchangeBundle\Form\ExchangeType;

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
     * @Route("/", name="sel_exchange_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $exchanges = $em->getRepository('SelExchangeBundle:Exchange')->findAll();

        return $this->render('SelExchangeBundle::index.html.twig', array(
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

        $qb = $em->getRepository('SelExchangeBundle:Exchange')->createQueryBuilder('e');
        $exchanges = $qb
            ->where($qb->expr()->orX(
                $qb->expr()->eq('e.creditUser', ':cu'),
                $qb->expr()->eq('e.debitUser', ':du')
            ))
            ->setParameters(array('cu' => $user->getId(), 'du' => $user->getId()))
            ->orderBy('e.created', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('SelExchangeBundle::listForUser.html.twig', array(
            'user' => $user,
            'exchanges' => $exchanges,
            'partial' => $partial
        ));
    }

    /**
     * Creates a new Exchange entity.
     *
     * @Route("/ajouter", name="sel_exchange_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $exchange = new Exchange();
        $form = $this->createForm('SelExchangeBundle\Form\ExchangeType', $exchange);
        $form->handleRequest($request);
        $error = false;

        if ($form->isSubmitted() && $form->isValid()) {
            $current_user = $this->getUser();
            if (!$exchange->getDebitUser()) {
                $exchange->setDebitUser($current_user);
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

                $this->addFlash(
                    'success',
                    'L\'échange a bien été enregistré.'
                );

                $message = \Swift_Message::newInstance()
                    ->setSubject('Bouts de fiSEL - Nouvel échange')
                    ->setFrom('info@boutsdefisel.be')
                    ->setTo($exchange->getCreditUser()->getEmail())
                    ->setBody(
                        $this->renderView(
                        // app/Resources/views/Emails/registration.html.twig
                            'SelExchangeBundle::new_email.html.twig',
                            array('exchange' => $exchange)
                        ),
                        'text/html'
                    )->addPart(
                        $this->renderView(
                            'SelExchangeBundle::new_email.txt.twig',
                            array('exchange' => $exchange)
                        ),
                        'text/plain'
                    );
                $this->get('mailer')->send($message);

                return $this->redirect($this->generateUrl('user_show', array('id' => $exchange->getDebitUser()->getId())) . '#section1');
            }
        }

        return $this->render('SelExchangeBundle::new.html.twig', array(
            'exchange' => $exchange,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Exchange entity.
     *
     * @Route("/{id}/modifier", name="sel_exchange_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Exchange $exchange)
    {
        $deleteForm = $this->createDeleteForm($exchange);
        $editForm = $this->createForm('SelExchangeBundle\Form\ExchangeType', $exchange);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $current_user = $this->getUser();
            $error = false;

            if (!$exchange->getDebitUser()) {
                $exchange->setDebitUser($current_user);
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

                $this->addFlash(
                    'success',
                    'L\'échange a bien été enregistré.'
                );

                return $this->redirect($this->generateUrl('user_show', array('id' => $current_user->getId())) . '#section1');
            }
        }

        return $this->render('SelExchangeBundle::edit.html.twig', array(
            'exchange' => $exchange,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Exchange entity.
     *
     * @Route("/{id}", name="sel_exchange_delete")
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

            $this->addFlash(
                'success',
                'L\'échange a bien été supprimé.'
            );
        }

        return $this->redirectToRoute('sel_exchange_index');
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
            ->setAction($this->generateUrl('sel_exchange_delete', array('id' => $exchange->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
