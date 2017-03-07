<?php

namespace SelFaqBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SelFaqBundle\Entity\Faq;
use SelFaqBundle\Form\FaqType;

/**
 * Faq controller.
 *
 * @Route("/faq")
 */
class FaqController extends Controller
{
    /**
     * Lists all Faq entities.
     *
     * @Route("/", name="sel_faq_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $q_public = $em->getRepository('SelFaqBundle:Faq')->findByType(0);
        $q_member = $em->getRepository('SelFaqBundle:Faq')->findByType(1);
        $q_editor = $em->getRepository('SelFaqBundle:Faq')->findByType(2);

        return $this->render('SelFaqBundle::index.html.twig', array(
            'q_public' => $q_public,
            'q_member' => $q_member,
            'q_editor' => $q_editor,
        ));
    }

    /**
     * Creates a new Faq entity.
     *
     * @Route("/ajouter", name="sel_faq_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_EDITOR');

        $faq = new Faq();
        $form = $this->createForm('SelFaqBundle\Form\FaqType', $faq);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($faq);
            $em->flush();

            $this->addFlash(
                'success',
                'La question a bien été enregistrée.'
            );

            return $this->redirect($this->generateUrl('sel_faq_index'));
        }

        return $this->render('SelFaqBundle::new.html.twig', array(
            'faq' => $faq,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Faq entity.
     *
     * @Route("/{id}/modifier", name="sel_faq_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Faq $faq)
    {
        $this->denyAccessUnlessGranted('ROLE_EDITOR');
        $deleteForm = $this->createDeleteForm($faq);
        $editForm = $this->createForm('SelFaqBundle\Form\FaqType', $faq);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($faq);
            $em->flush();

            $this->addFlash(
                'success',
                'La question a bien été enregistrée.'
            );

            return $this->redirect($this->generateUrl('sel_faq_index'));
        }

        return $this->render('SelFaqBundle::edit.html.twig', array(
            'faq' => $faq,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Faq entity.
     *
     * @Route("/{id}", name="sel_faq_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Faq $faq)
    {
        $this->denyAccessUnlessGranted('ROLE_EDITOR');
        $form = $this->createDeleteForm($faq);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($faq);
            $em->flush();

            $this->addFlash(
                'success',
                'La question a bien été supprimée.'
            );
        }

        return $this->redirectToRoute('sel_faq_index');
    }

    /**
     * Creates a form to delete a Faq entity.
     *
     * @param Faq $faq The Faq entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Faq $faq)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('sel_faq_delete', array('id' => $faq->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
