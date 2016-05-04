<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Service;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class ServiceType extends AbstractType
{
    private $authorizationChecker;

    public function __construct(AuthorizationChecker $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // TODO: Search for a better way
        $serivce = new Service();

        $types = array_flip($serivce->getTypes());
        $domains = array_flip($serivce->getDomains());

        if ($this->authorizationChecker->isGranted('ROLE_EDITOR')) {
            $builder->add('user', EntityType::class, array(
                'class' => 'AppBundle:User',
                'choice_label' => 'username',
                'placeholder' => 'Auteur',
                'label' => 'Auteur'
            ));
        }

        $builder
            ->add('title', TextType::class, array(
                'label' => 'Titre'
            ))
            ->add('body', TextareaType::class, array(
                'label' => 'Contenu'
            ))
            ->add('type', ChoiceType::class, array(
                'choices'  => $types,
                'label' => 'Type de service'
            ))
            ->add('domain', ChoiceType::class, array(
                'choices'  => $domains,
                'label' => 'Domaine'
            ))
            ->add('category', EntityType::class, array(
                'class' => 'AppBundle:Category',
                'choice_label' => 'select_label',
                'placeholder' => 'Catégorie',
                'label' => 'Catégorie'
            ))
            ->add('promote', CheckboxType::class, array(
                'label'    => 'Mettre en évidence ?',
                'required' => false,
            ))
            ->add('picture', FileType::class, array(
                'label' => 'Image',
                'required' => false
            ))
            ->add('expires_at', DateType::class, array(
                'widget' => 'single_text',
                'required' => false,
                'label' => 'Date d\'expiration'
            ))
            ->add('save', SubmitType::class, array(
                'attr' => array('class' => 'main'),
            ));
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Service'
        ));
    }
}
