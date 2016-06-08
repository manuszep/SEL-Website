<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\DataTransformer\PhoneNumberTransformer;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class UserProfileType extends AbstractType
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
        $builder
            ->add('username', TextType::class, array('label' => 'label.username'))
            ->add('email', EmailType::class, array('label' => 'label.email'))
            ->add('phone', TextType::class, array('label' => 'label.phone', 'required' => false))
            ->add('mobile', TextType::class, array('label' => 'label.mobile', 'required' => false))
            ->add('street', TextType::class, array('label' => 'label.street', 'required' => false))
            ->add('street_number', TextType::class, array('label' => 'label.streetNumber', 'required' => false))
            ->add('street_box', TextType::class, array('label' => 'label.streetBox', 'required' => false))
            ->add('zip', IntegerType::class, array('label' => 'label.zip', 'required' => false))
            ->add('city', TextType::class, array('label' => 'label.city', 'required' => false))
            ->add('picture', FileType::class, array(
                'label' => 'label.picture',
                'required' => false
            ));

        if (!$this->authorizationChecker->isGranted('ROLE_EDITOR')) {
            $builder->add('current_password', PasswordType::class, array(
                'label' => 'label.passwordCurrent',
                'mapped' => false,
                'constraints' => new UserPassword(),
            ));
        }

        $builder->add('save', SubmitType::class, array(
            'attr' => array('class' => 'main'),
            'label' => 'label.save'
        ));

        $builder->get('phone')
            ->addModelTransformer(new PhoneNumberTransformer());

        $builder->get('mobile')
            ->addModelTransformer(new PhoneNumberTransformer());
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User'
        ));
    }
}