<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ServiceManager;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Service;
use AppBundle\Form\ServiceType;
use AppBundle\Form\ServiceFilterType;
use AppBundle\Entity\User;

/**
 * Service controller.
 *
 * @Route("/service")
 */
class ServiceController extends Controller
{
    /**
     * Lists all Service entities.
     *
     * @Route("/", name="service_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        // TODO: Filter expired items
        // TODO: Split "offre flash" and "demande flash" to display them separately
        $form = $this->createForm('AppBundle\Form\ServiceFilterType');

        $service_manager = $this->getServiceManager();

        if ($request->query->has($form->getName())) {
            // manually bind values from the request
            $form->submit($request->query->get($form->getName()));

            $services = $service_manager->findFiltered($form);
        } else {
            $services = $service_manager->findAll();
        }

        return $this->render('service/index.html.twig', array(
            'services' => $this->getPagination($services, $request),
            'filter' => $form->createView()
        ));
    }

    public function listForUserAction(Request $request, User $user, $partial) {
        $service_manager = $this->getServiceManager();

        $services = $service_manager->findByUser($user);

        return $this->render('service/listForUser.html.twig', array(
            'services' => $this->getPagination($services, $request),
            'partial' => $partial
        ));
    }

    /**
     * Creates a new Service entity.
     *
     * @Route("/new", name="service_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $service_manager = $this->getServiceManager();

        // TODO: Add logic so when the user chooses to make a "flash" service, the expiration date is mandatory and is set in 15 days by default.
        $service = $service_manager->createService();

        $form = $this->createForm('AppBundle\Form\ServiceType', $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $service_manager->saveService($service);

            return $this->redirectToRoute('service_show', array('id' => $service->getId()));
        }

        return $this->render('service/new.html.twig', array(
            'service' => $service,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Service entity.
     *
     * @Route("/{id}", name="service_show")
     * @Method("GET")
     */
    public function showAction(Service $service)
    {
        $deleteForm = $this->createDeleteForm($service);

        return $this->render('service/show.html.twig', array(
            'service' => $service,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Service entity.
     *
     * @Route("/{id}/edit", name="service_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Service $service)
    {
        $service_manager = $this->getServiceManager();
        $deleteForm = $this->createDeleteForm($service);
        $editForm = $this->createForm('AppBundle\Form\ServiceType', $service);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $service_manager->saveService($service);

            return $this->redirectToRoute('service_show', array('id' => $service->getId()));
        }

        return $this->render('service/edit.html.twig', array(
            'service' => $service,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Service entity.
     *
     * @Route("/{id}", name="service_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Service $service)
    {
        $form = $this->createDeleteForm($service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $service_manager = $this->getServiceManager();
            $service_manager->deleteService($service);
        }

        return $this->redirectToRoute('service_index');
    }

    /**
     * Creates a form to delete a Service entity.
     *
     * @param Service $service The Service entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Service $service)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('service_delete', array('id' => $service->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * @return ServiceManager
     */
    protected function getServiceManager()
    {
        return $this->container->get('app.manager.service');
    }

    public function getPagination($services, $request, $limit = 10) {
        $paginator  = $this->get('knp_paginator');
        return $paginator->paginate(
            $services,
            $request->query->getInt('page', 1),
            $limit
        );
    }
}
