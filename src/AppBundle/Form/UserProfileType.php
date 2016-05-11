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
            ->add('username', TextType::class, array('label' => 'Nom d\'utilisateur'))
            ->add('email', EmailType::class, array('label' => 'Adresse email'))
            ->add('phone', TextType::class, array('label' => 'Téléphone'))
            ->add('mobile', TextType::class, array('label' => 'GSM'))
            ->add('street', TextType::class, array('label' => 'Rue'))
            ->add('street_number', TextType::class, array('label' => 'N°'))
            ->add('street_box', TextType::class, array('label' => 'Boite'))
            ->add('zip', NumberType::class, array('label' => 'Code postal'))
            ->add('city', TextType::class, array('label' => 'Ville'))
            ->add('picture', FileType::class, array(
                'label' => 'Image',
                'required' => false
            ))
            ->add('current_password', PasswordType::class, array(
                'label' => 'Mot de passe actuel',
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