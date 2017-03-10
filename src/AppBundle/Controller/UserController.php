<?php

namespace AppBundle\Controller;

use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Util\TokenGenerator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * User controller.
 *
 * @Route("/membres")
 */
class UserController extends Controller
{
    /**
     * Lists all User entities.
     *
     * @Route("/", name="user_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $this->denyAccessUnlessGranted('list users');
        $print_list = $request->get('print_list');
        $um = $this->get('fos_user.user_manager');

        $users = $um->findUsers();

        /**
         *  TODO: Check for an alternative way to filter objects
         *  This block removes items that the current user has not the right to see.
         *  Qerying all items and then looping them to test security may not be the most optimal option
         *  It would be more efficient to inject a where clause in the query but how to keep that logic in the voter ?
         */
        foreach($users as $key =>$user) {
            if (!$this->isGranted('show user', $user)) {
                unset($users[$key]);
            }
        }

        if ($print_list) {
            $data = $users;
        } else {
            $data = $this->getPagination($users, $request);
        }

        return $this->render('user/index.html.twig', array(
            'users' => $data,
            'print_list' => $print_list
        ));
    }

    /**
     * @Route("/csv", name="user_csv_index")
     * @Method("GET")
     */
    public function exportCSV() {
        $um = $this->get('fos_user.user_manager');

        $users = $um->findUsers();

        foreach($users as $key => $user) {
            if (!$this->isGranted('show user', $user)) {
                unset($users[$key]);
            }
        }

        $response = new StreamedResponse();
        $response->setCallback(function() use ($users) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, array(
                'Name',
                'Given Name',
                'Additional Name',
                'Family Name',
                'Yomi Name',
                'Given Name Yomi',
                'Additional Name Yomi',
                'Family Name Yomi',
                'Name Prefix',
                'Name Suffix',
                'Initials',
                'Nickname',
                'Short Name',
                'Maiden Name',
                'Birthday',
                'Gender',
                'Location',
                'Billing Information',
                'Directory Server',
                'Mileage',
                'Occupation',
                'Hobby',
                'Sensitivity',
                'Priority',
                'Subject',
                'Notes',
                'Group Membership',
                'E-mail 1 - Type',
                'E-mail 1 - Value',
                'Phone 1 - Type',
                'Phone 1 - Value',
                'Phone 2 - Type',
                'Phone 2 - Value',
                'Address 1 - Type',
                'Address 1 - Formatted',
                'Address 1 - Street',
                'Address 1 - City',
                'Address 1 - PO Box',
                'Address 1 - Region',
                'Address 1 - Postal Code',
                'Address 1 - Country',
                'Address 1 - Extended Address',
                'Website 1 - Type',
                'Website 1 - Value'
            ), ',');

            /**
             * @var User $user
             */
            foreach($users as $user) {
                fputcsv($handle, array(
                    $user->getUsername(),
                    $user->getUsername(),
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '* My Contacts',
                    '* Principal',
                    $user->getEmail(),
                    'Fixe',
                    $user->getPhone(),
                    'Mobile',
                    $user->getMobile(),
                    'Home',
                    '',
                    $user->getStreet() . ' ' . $user->getStreetNumber(),
                    $user->getCity(),
                    $user->getStreetBox(),
                    '',
                    $user->getZip(),
                    'Belgique',
                    '',
                    'Profile',
                    'http://www.boutsdefisel.be/membres/' . $user->getId()
                ), ',');
            }

            fclose($handle);
        });

        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition','attachment; filename="boutsdefisel.csv"');

        return $response;
    }

    /**
     * Creates a new User entity.
     *
     * @Route("/ajouter", name="user_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $this->denyAccessUnlessGranted('create user');

        $um = $this->get('fos_user.user_manager');
        $user = $um->createUser();
        $user->setEnabled(false);
        $user->setLocked(false);

        $form = $this->createForm('AppBundle\Form\UserType', $user);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $tokenGenerator = $this->container->get('fos_user.util.token_generator');
            $password = substr($tokenGenerator->generateToken(), 0, 12);

            $user->setPassword($password);
            $user->setBalance(50);

            if (null === $user->getConfirmationToken()) {
                $user->setConfirmationToken($tokenGenerator->generateToken());
            }

            $um->updateUser($user);

            $this->get('fos_user.mailer')->sendConfirmationEmailMessage($user);
            $this->addFlash(
                'success',
                'L\'utilisateur <strong>' . $user->getUsername() . '</strong> a bien été enregistré. Un email de confirmation lui a été envoyé.'
            );

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/new.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
        ));
    }

    /**
     * Disables a User entity.
     *
     * @Route("/{id}/bloquer", name="user_disable")
     * @Method("GET")
     */
    public function disableAction(User $user)
    {
        if ($this->getUser()->getId() == $user->getId()) {
            $this->addFlash(
                'error',
                'L\'utilisateur connecté ne peut désactiver son propre profil.'
            );

            return $this->redirectToRoute('user_index');
        }

        $this->denyAccessUnlessGranted('lock user', $user);

        $user->setLocked(true);
        $this->get('fos_user.user_manager')->updateUser($user, false);
        $this->getDoctrine()->getManager()->flush();

        $this->addFlash(
            'success',
            'L\'utilisateur ' . $user->getUsername() . ' a bien été bloqué.'
        );

        return $this->redirectToRoute('user_index');
    }

    /**
     * Enables a User entity.
     *
     * @Route("/{id}/debloquer", name="user_enable")
     * @Method("GET")
     */
    public function enableAction(User $user)
    {
        if ($this->getUser()->getId() == $user->getId()) {
            $this->addFlash(
                'error',
                'L\'utilisateur connecté ne peut activer son propre profil.'
            );

            return $this->redirectToRoute('user_index');
        }

        $this->denyAccessUnlessGranted('unlock user', $user);

        $user->setLocked(false);
        $this->get('fos_user.user_manager')->updateUser($user, false);
        $this->getDoctrine()->getManager()->flush();

        $this->addFlash(
            'success',
            'L\'utilisateur ' . $user->getUsername() . ' a bien été débloqué.'
        );

        return $this->redirectToRoute('user_index');
    }

    /**
     * Activate a User entity.
     *
     * @Route("/{id}/activer", name="user_activate")
     * @Method({"GET", "POST"})
     */
    public function activateAction(Request $request, User $user)
    {
        if ($user->isEnabled()) {
            $this->addFlash(
                'error',
                'L\'utilisateur est déjà activé.'
            );

            return $this->redirectToRoute('user_index');
        }

        $this->denyAccessUnlessGranted('enable user', $user);

        $form = $this->createForm('UserBundle\Form\UserPasswordType');
        $form->handleRequest($request);

        if ($form->isValid()) {
            $form_data = $form->getData();

            $user->setPlainPassword($form_data['plainPassword']);

            $user->setConfirmationToken(null);
            $user->setEnabled(true);
            $this->get('fos_user.user_manager')->updateUser($user, false);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash(
                'success',
                'L\'utilisateur ' . $user->getUsername() . ' a bien été activé.'
            );

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/registrationPassword.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * Enables COCO role on a User entity.
     *
     * @Route("/{id}/activer-coco", name="user_enable_coco")
     * @Method("GET")
     */
    public function enableCocoAction(User $user)
    {
        $this->denyAccessUnlessGranted('enable coco user', $user);

        $user->addRole("ROLE_COCO");
        $this->get('fos_user.user_manager')->updateUser($user, false);
        $this->getDoctrine()->getManager()->flush();

        $this->addFlash(
            'success',
            'L\'utilisateur ' . $user->getUsername() . ' a reçu le rôle "COCO".'
        );

        return $this->redirectToRoute('user_index');
    }

    /**
     * Disables COCO role on a User entity.
     *
     * @Route("/{id}/desactiver-coco", name="user_disable_coco")
     * @Method("GET")
     */
    public function disableCocoAction(User $user)
    {
        $this->denyAccessUnlessGranted('disable coco user', $user);

        $user->removeRole("ROLE_COCO");
        $this->get('fos_user.user_manager')->updateUser($user, false);
        $this->getDoctrine()->getManager()->flush();

        $this->addFlash(
            'success',
            'L\'utilisateur ' . $user->getUsername() . ' a perdu le rôle "COCO".'
        );

        return $this->redirectToRoute('user_index');
    }

    /**
     * Finds and displays a User entity.
     *
     * @Route("/{id}", name="user_show")
     * @Method("GET")
     */
    public function showAction(User $user)
    {
        $this->denyAccessUnlessGranted('show user', $user);

        return $this->render('user/show.html.twig', array(
            'user' => $user
        ));

        return;
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     * @Route("/{id}/modifier", name="user_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, User $user)
    {
        $this->denyAccessUnlessGranted('edit user', $user);

        $editForm = $this->createForm('AppBundle\Form\UserProfileType', $user);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $um = $this->get('fos_user.user_manager');

            $user->upload();

            $this->addFlash(
                'success',
                'L\'utilisateur <strong>' . $user->getUsername() . '</strong> a bien été enregistré.'
            );

            $um->updateUser($user);

            return $this->redirectToRoute('user_show', array('id' => $user->getId()));
        }

        return $this->render('user/edit.html.twig', array(
            'user' => $user,
            'form' => $editForm->createView()
        ));
    }

    /**
     * @param $users
     * @param Request $request
     * @param int $limit
     * @return \Knp\Component\Pager\Pagination\PaginationInterface
     */
    public function getPagination($users, $request, $limit = 10) {
        $paginator  = $this->get('knp_paginator');

        return $paginator->paginate(
            $users,
            $request->query->getInt('page', 1),
            $limit
        );
    }
}
