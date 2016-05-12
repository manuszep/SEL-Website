<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class UserProfileType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, array('label' => 'label.username'))
            ->add('email', EmailType::class, array('label' => 'label.email'))
            ->add('phone', TextType::class, array('label' => 'label.phone'))
            ->add('mobile', TextType::class, array('label' => 'label.mobile'))
            ->add('street', TextType::class, array('label' => 'label.street'))
            ->add('street_number', TextType::class, array('label' => 'label.streetNumber'))
            ->add('street_box', TextType::class, array('label' => 'label.streetBox'))
            ->add('zip', NumberType::class, array('label' => 'label.zip'))
            ->add('city', TextType::class, array('label' => 'label.city'))
            ->add('picture', FileType::class, array(
                'label' => 'label.picture',
                'required' => false
            ))
            ->add('current_password', PasswordType::class, array(
                'label' => 'label.passwordCurrent',
                'mapped' => false,
                'constraints' => new UserPassword(),
            ))
        ;
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