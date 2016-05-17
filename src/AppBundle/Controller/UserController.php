<?php

namespace AppBundle\Controller;

use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Util\TokenGenerator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\User;

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
    public function indexAction()
    {
        $um = $this->get('fos_user.user_manager');
        
        $users = $um->findUsers();

        return $this->render('user/index.html.twig', array(
            'users' => $users,
        ));
    }

    /**
     * Creates a new User entity.
     *
     * @Route("/ajouter", name="user_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $tokenGenerator = new TokenGenerator();

        $um = $this->get('fos_user.user_manager');
        $user = $um->createUser();
        $user->setEnabled(false);

        $form = $this->createForm('AppBundle\Form\UserType', $user);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $tokenGenerator = $this->container->get('fos_user.util.token_generator');
            $password = substr($tokenGenerator->generateToken(), 0, 12);

            $user->setPassword($password);

            if (null === $user->getConfirmationToken()) {
                $user->setConfirmationToken($tokenGenerator->generateToken());
            }

            $um->updateUser($user);

            $this->get('fos_user.mailer')->sendConfirmationEmailMessage($user);
            $this->addFlash(
                'notice',
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
            'form' => $form->createView(),
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

            return $this->redirectToRoute('user_edit', array('id' => $user->getId()));
        }

        return $this->render('user/edit.html.twig', array(
            'user' => $user,
            'edit_form' => $editForm->createView()
        ));
    }
}