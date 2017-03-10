<?php

namespace UserBundle\Controller;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Controller\ResettingController as BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Controller managing the resetting of the password
 */
class ResettingController extends BaseController
{
    /**
     * Reset user password
     */
    public function resetAction(Request $request, $token)
    {
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $user = $userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with "confirmation token" does not exist for value "%s"', $token));
        }

        $form = $this->createForm('UserBundle\Form\UserPasswordType');
        $form->handleRequest($request);

        if ($form->isValid()) {
            $form_data = $form->getData();

            $user->setPlainPassword($form_data['plainPassword']);
            $user->setConfirmationToken(null);
            $user->setPasswordRequestedAt(null);

            /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
            $dispatcher = $this->get('event_dispatcher');

            $event = new GetResponseUserEvent($user, $request);
            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_CONFIRM, $event);

            $userManager->updateUser($user);

            $this->addFlash(
                'success',
                $user->getUsername() . ',<br /> Votre mot de passe vient d\'être mis à jour.'
            );

            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('user_show', array('id' => $user->getId())) . '#section2';
                $response = new RedirectResponse($url);
            }

            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_CONFIRMED, new FilterUserResponseEvent($user, $request, $response));

            return $response;
        }

        return $this->render('user/resetPassword.html.twig', array(
            'form' => $form->createView(),
            'username' => $user->getUsername()
        ));
    }
}
