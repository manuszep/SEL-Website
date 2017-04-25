<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Collection;
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrue as RecaptchaTrue;

class ContactType extends AbstractType
{
    private $authorizationChecker;
    private $tokenStorage;

    public function __construct(AuthorizationChecker $authorizationChecker, TokenStorage $tokenStorage)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $name_attrs = array(
            'placeholder' => 'placeholder.yourName',
            'pattern'     => '.{2,}' //minlength
        );

        $email_attrs = array(
            'placeholder' => 'placeholder.yourEmail'
        );

        // If user is logged-in
        if ($this->authorizationChecker->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            // Set default value on name_attrs and lock field
            $usr = $this->tokenStorage->getToken()->getUser();
            $name_attrs['value'] = $usr->getUsername();
            $name_attrs['readonly'] = true;
            $email_attrs['value'] = $usr->getEmail();
            $email_attrs['readonly'] = true;
        }

        $builder
            ->add('name', TextType::class, array(
                'label' => 'label.name',
                'attr' => $name_attrs,
                'constraints' => array(
                    new NotBlank(array('message' => 'Le nom est obligatoire')),
                    new Length(array('min' => 2))
                )
            ))
            ->add('email', EmailType::class, array(
                'label' => 'label.email',
                'attr' => $email_attrs,
                'constraints' => array(
                    new NotBlank(array('message' => 'L\'email est obligatoire')),
                    new Email(array('message' => 'Cette adresse email ne semble pas valide.'))
                )
            ))
            ->add('subject', TextType::class, array(
                'label' => 'label.subject',
                'attr' => array(
                    'placeholder' => 'placeholder.messageSubject',
                    'pattern'     => '.{3,}' //minlength
                ),
                'constraints' => array(
                    new NotBlank(array('message' => 'Le sujet est obligatoire.')),
                    new Length(array('min' => 3))
                )
            ))
            ->add('message', TextareaType::class, array(
                'label' => 'label.message',
                'attr' => array(
                    'cols' => 90,
                    'rows' => 10,
                    'placeholder' => 'placeholder.yourMessage',
                    'class' => "wysiwyg"
                ),
                'constraints' => array(
                    new NotBlank(array('message' => 'Le message est obligatoire.')),
                    new Length(array('min' => 5))
                )
            ))
            ->add('recaptcha', EWZRecaptchaType::class, array(
                'label' => 'label.recaptcha',
                'attr' => array(
                    'options' => array(
                        'theme' => 'light',
                        'type'  => 'image',
                        'size'  => 'normal',
                        'defer' => true,
                        'async' => true
                    )
                ),
                'mapped'      => false,
                'constraints' => array(
                    new RecaptchaTrue()
                )
            ))
            ->add('send', SubmitType::class, array(
                'label' => 'label.send',
                'attr' => array('class' => 'main')
                )
            );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
    }
}