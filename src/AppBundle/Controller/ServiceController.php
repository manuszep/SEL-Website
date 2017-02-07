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
        $print_list = $request->get('print_list');
        $form = $this->createForm('AppBundle\Form\ServiceFilterType');
        $template_ext = '';

        $service_manager = $this->getServiceManager();

        if ($request->query->has($form->getName())) {
            // manually bind values from the request
            $form->submit($request->query->get($form->getName()));

            $services = $service_manager->findFiltered($form);
        } else {
            $services = $service_manager->findAll();
        }

        if ($print_list) {
            $template_ext = '_print';
            $data = $services;
        } else {
            $data = $this->getPagination($services, $request);
        }

        return $this->render('service/index' . $template_ext . '.html.twig', array(
            'services' => $data,
            'filter' => $form->createView(),
            'print_list' => $print_list
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
     * @Route("/ajouter", name="service_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $this->denyAccessUnlessGranted('create-service');

        $service_manager = $this->getServiceManager();

        $service = $service_manager->createService();

        $form = $this->createForm('AppBundle\Form\ServiceType', $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $flash_services_ids = $this->container->getParameter('app_bundle.service.flash_types_ids');

            if (in_array($service->getType(), $flash_services_ids) && !$service->getExpiresAt()) {
                $now = new \DateTime();
                $service->setExpiresAt($now->modify('+ 2 weeks'));
            }

            $service_manager->saveService($service);

            $this->addFlash(
                'success',
                'Le service a bien été enregistré.'
            );

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
        if (!$service->getUser()->isAccountNonLocked() || $service->isExpired()) {
            throw $this->createNotFoundException('Ce service n\'existe pas');
        }

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
        $this->denyAccessUnlessGranted('edit', $service);

        $service_manager = $this->getServiceManager();
        $deleteForm = $this->createDeleteForm($service);
        $editForm = $this->createForm('AppBundle\Form\ServiceType', $service);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $flash_services_ids = $this->container->getParameter('app_bundle.service.flash_types_ids');

            if (in_array($service->getType(), $flash_services_ids) && !$service->getExpiresAt()) {
                $now = new \DateTime();
                $service->setExpiresAt($now->modify('+ 2 weeks'));
            }

            $service_manager->saveService($service);

            $this->addFlash(
                'success',
                'Le service a bien été enregistré.'
            );

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
        $this->denyAccessUnlessGranted('delete', $service);

        $form = $this->createDeleteForm($service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $service_manager = $this->getServiceManager();
            $service_manager->deleteService($service);

            $this->addFlash(
                'success',
                'Le service a bien été supprimé.'
            );
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
