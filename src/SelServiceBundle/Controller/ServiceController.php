<?php

namespace SelServiceBundle\Controller;

use SelServiceBundle\Entity\ServiceManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SelServiceBundle\Entity\Service;
use SelServiceBundle\Form\ServiceType;
use SelServiceBundle\Form\ServiceFilterType;
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
     * @Route("/", name="sel_service_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $print_list = $request->get('print_list');
        $form = $this->createForm('SelServiceBundle\Form\ServiceFilterType');
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

        return $this->render('SelServiceBundle::index' . $template_ext . '.html.twig', array(
            'services' => $data,
            'filter' => $form->createView(),
            'print_list' => $print_list
        ));
    }

    public function listForUserAction(Request $request, User $user, $partial) {
        $service_manager = $this->getServiceManager();

        $services = $service_manager->findByUser($user);

        return $this->render('SelServiceBundle::listForUser.html.twig', array(
            'services' => $this->getPagination($services, $request),
            'user' => $user,
            'partial' => $partial
        ));
    }

    /**
     * Creates a new Service entity.
     *
     * @Route("/ajouter", name="sel_service_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $now = new \DateTime();
        $year1 = $now->modify('+ 1 year');
        $week2 = $now->modify('+ 2 weeks');

        $this->denyAccessUnlessGranted('create-service');

        $service_manager = $this->getServiceManager();

        $service = $service_manager->createService();

        $service->setExpiresAt($year1);

        $form = $this->createForm('SelServiceBundle\Form\ServiceType', $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $flash_services_ids = $this->container->getParameter('sel_service_bundle.service.flash_types_ids');

            if (in_array($service->getType(), $flash_services_ids) && !$service->getExpiresAt()) {
                $service->setExpiresAt($week2);
            }

            $service_manager->saveService($service);

            $this->addFlash(
                'success',
                'Le service a bien été enregistré.'
            );

            return $this->redirectToRoute('sel_service_show', array('id' => $service->getId()));
        }

        return $this->render('SelServiceBundle::new.html.twig', array(
            'service' => $service,
            'form' => $form->createView(),
            'flash_default_date' => date('d/m/Y',strtotime(date("Y-m-d", mktime()) . " + 14 day")),
            'normal_default_date' => date('d/m/Y',strtotime(date("Y-m-d", mktime()) . " + 365 day"))
        ));
    }

    /**
     * Finds and displays a Service entity.
     *
     * @Route("/{id}", name="sel_service_show")
     * @Method("GET")
     */
    public function showAction(Service $service)
    {
        if (!$service->getUser()->isAccountNonLocked() || $service->isExpired()) {
            throw $this->createNotFoundException('Ce service n\'existe pas');
        }

        $deleteForm = $this->createDeleteForm($service);

        return $this->render('SelServiceBundle::show.html.twig', array(
            'service' => $service,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Service entity.
     *
     * @Route("/{id}/edit", name="sel_service_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Service $service)
    {
        $this->denyAccessUnlessGranted('edit', $service);

        $service_manager = $this->getServiceManager();
        $deleteForm = $this->createDeleteForm($service);
        $editForm = $this->createForm('SelServiceBundle\Form\ServiceType', $service);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $flash_services_ids = $this->container->getParameter('sel_service_bundle.service.flash_types_ids');

            if (in_array($service->getType(), $flash_services_ids) && !$service->getExpiresAt()) {
                $now = new \DateTime();
                $service->setExpiresAt($now->modify('+ 2 weeks'));
            }

            $service_manager->saveService($service);

            $this->addFlash(
                'success',
                'Le service a bien été enregistré.'
            );

            return $this->redirectToRoute('sel_service_show', array('id' => $service->getId()));
        }

        return $this->render('SelServiceBundle::edit.html.twig', array(
            'service' => $service,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'flash_default_date' => date('d/m/Y',strtotime(date("Y-m-d", mktime()) . " + 14 day")),
            'normal_default_date' => date('d/m/Y',strtotime(date("Y-m-d", mktime()) . " + 365 day"))
        ));
    }

    /**
     * Deletes a Service entity.
     *
     * @Route("/{id}", name="sel_service_delete")
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

        return $this->redirectToRoute('sel_service_index');
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
            ->setAction($this->generateUrl('sel_service_delete', array('id' => $service->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }

    /**
     * @return ServiceManager
     */
    protected function getServiceManager()
    {
        return $this->container->get('sel_service.manager.service');
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
