<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Form\ContactType;

/**
 * Contact controller.
 *
 * @Route("/contact")
 */
class ContactController extends Controller
{
    /**
     * @Route("/", name="contact_index")
     * @Method({"GET", "POST"})
     */
    public function indexAction(Request $request)
    {
        $form = $this->createForm('AppBundle\Form\ContactType');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $message = \Swift_Message::newInstance()
                ->setSubject($data['subject'])
                ->setFrom($data['email'])
                ->setTo('info@boutsdefisel.be')
                ->setBody(
                    $this->renderView(
                        'Emails/contact.html.twig',
                        array('name' => $data['name'],
                              'message' => $data['message']
                        )
                    ),
                    'text/html'
                )
                ->addPart(
                    $this->renderView(
                        'Emails/contact.txt.twig',
                        array('name' => $data['name'],
                              'message' => $data['message']
                        )
                    ),
                    'text/plain'
                )
            ;
            $this->get('mailer')->send($message);

            $this->addFlash(
                'success',
                'Votre message a bien été envoyé.'
            );

            return $this->redirectToRoute('homepage');
        }

        return $this->render('contact/index.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}