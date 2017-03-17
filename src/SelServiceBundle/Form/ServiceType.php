<?php

namespace SelServiceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class ServiceType extends AbstractType
{
    private $authorizationChecker;
    private $serviceTypes;
    private $serviceDomains;

    public function __construct(AuthorizationChecker $authorizationChecker, $serviceTypes, $serviceDomains)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->serviceTypes = $serviceTypes;
        $this->serviceDomains = $serviceDomains;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $types = array_flip($this->serviceTypes);
        $domains = array_flip($this->serviceDomains);

        if ($this->authorizationChecker->isGranted('ROLE_EDITOR')) {
            $builder->add('user', EntityType::class, array(
                'class' => 'AppBundle:User',
                'choice_label' => 'username',
                'placeholder' => 'placeholder.pleaseChoose',
                'label' => 'label.author'
            ));
        }

        $builder
            ->add('title', TextType::class, array(
                'label' => 'label.title'
            ))
            ->add('body', TextareaType::class, array(
                'label' => 'label.content',
                'attr' => array(
                    'class' => "wysiwyg"
                )
            ))
            ->add('type', ChoiceType::class, array(
                'choices'  => $types,
                'label' => 'label.serviceType'
            ))
            ->add('domain', ChoiceType::class, array(
                'choices'  => $domains,
                'label' => 'label.domain'
            ))
            ->add('category', EntityType::class, array(
                'class' => 'SelCategoryBundle:Category',
                'choice_label' => 'select_label',
                'placeholder' => 'placeholder.pleaseChoose',
                'label' => 'label.category'
            ))
            ->add('picture', FileType::class, array(
                'label' => 'label.picture',
                'required' => false
            ))
            ->add('expires_at', DateType::class, array(
                'widget' => 'single_text',
                'required' => false,
                'label' => 'label.expiresAt',
                'format' => 'dd/MM/yyyy',
                'attr' => array(
                    'placeholder' => 'jj/mm/aaaa'
                )
            ))
            ->add('save', SubmitType::class, array(
                'attr' => array('class' => 'main'),
                'label' => 'label.save'
            ));
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SelServiceBundle\Entity\Service'
        ));
    }
}
