<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Faq;
use AppBundle\Form\FaqType;

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
     * @Route("/", name="faq_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $q_public = $em->getRepository('AppBundle:Faq')->findByType(0);
        $q_member = $em->getRepository('AppBundle:Faq')->findByType(1);
        $q_editor = $em->getRepository('AppBundle:Faq')->findByType(2);

        return $this->render('faq/index.html.twig', array(
            'q_public' => $q_public,
            'q_member' => $q_member,
            'q_editor' => $q_editor,
        ));
    }

    /**
     * Creates a new Faq entity.
     *
     * @Route("/ajouter", name="faq_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_EDITOR');

        $faq = new Faq();
        $form = $this->createForm('AppBundle\Form\FaqType', $faq);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($faq);
            $em->flush();

            $this->addFlash(
                'success',
                'La question a bien été enregistrée.'
            );

            return $this->redirect($this->generateUrl('faq_index'));
        }

        return $this->render('faq/new.html.twig', array(
            'faq' => $faq,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Faq entity.
     *
     * @Route("/{id}/modifier", name="faq_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Faq $faq)
    {
        $deleteForm = $this->createDeleteForm($faq);
        $editForm = $this->createForm('AppBundle\Form\FaqType', $faq);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($faq);
            $em->flush();

            $this->addFlash(
                'success',
                'La question a bien été enregistrée.'
            );

            return $this->redirect($this->generateUrl('faq_index'));
        }

        return $this->render('faq/edit.html.twig', array(
            'faq' => $faq,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Faq entity.
     *
     * @Route("/{id}", name="faq_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Faq $faq)
    {
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

        return $this->redirectToRoute('faq_index');
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
            ->setAction($this->generateUrl('faq_delete', array('id' => $faq->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
