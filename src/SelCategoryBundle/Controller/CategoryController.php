<?php

namespace SelCategoryBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SelCategoryBundle\Entity\Category;
use SelCategoryBundle\Form\CategoryType;

/**
 * Category controller.
 *
 * @Route("/categories")
 */
class CategoryController extends Controller
{
    /**
     * Lists all Category entities.
     *
     * @Route("/", name="sel_category_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $this->denyAccessUnlessGranted('manage-category');

        $em = $this->getDoctrine()->getManager();

        $categories = $em->getRepository('SelCategoryBundle:Category')->findAll();

        return $this->render('SelCategoryBundle::index.html.twig', array(
            'categories' => $categories,
        ));
    }

    /**
     * Creates a new Category entity.
     *
     * @Route("/ajouter", name="sel_category_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $this->denyAccessUnlessGranted('manage-category');

        $category = new Category();
        $form = $this->createForm('SelCategoryBundle\Form\CategoryType', $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            $this->addFlash(
                'success',
                'La catégorie a bien été enregistrée.'
            );

            return $this->redirectToRoute('sel_category_show', array('id' => $category->getId()));
        }

        return $this->render('SelCategoryBundle::new.html.twig', array(
            'category' => $category,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Category entity.
     *
     * @Route("/{id}/modifier", name="sel_category_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Category $category)
    {
        $this->denyAccessUnlessGranted('manage-category');

        $deleteForm = $this->createDeleteForm($category);
        $editForm = $this->createForm('SelCategoryBundle\Form\CategoryType', $category);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            $this->addFlash(
                'success',
                'La catégorie a bien été enregistrée.'
            );

            return $this->redirectToRoute('sel_category_edit', array('id' => $category->getId()));
        }

        return $this->render('SelCategoryBundle::edit.html.twig', array(
            'category' => $category,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Category entity.
     *
     * @Route("/{id}", name="sel_category_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Category $category)
    {
        $this->denyAccessUnlessGranted('manage-category');
        
        $form = $this->createDeleteForm($category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($category);
            $em->flush();

            $this->addFlash(
                'success',
                'La catégorie a bien été supprimée.'
            );
        }

        return $this->redirectToRoute('sel_category_index');
    }

    /**
     * Creates a form to delete a Category entity.
     *
     * @param Category $category The Category entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Category $category)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('sel_category_delete', array('id' => $category->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}

