<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ExchangeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('creditUser', EntityType::class, array(
                'class' => 'AppBundle:User',
                'choice_label' => 'username',
                'placeholder' => 'Membre qui reçoit des noeuds'
            ))
            ->add('debitUser', EntityType::class, array(
                'class' => 'AppBundle:User',
                'choice_label' => 'username',
                'placeholder' => 'Membre qui donne des noeuds'
            ))
            ->add('message', TextareaType::class, array(
                'required' => false
            ))
            ->add('amount', IntegerType::class, array(
                'scale' => 2,
                'attr' => array(
                    'step' => 0.25
                )
            ))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Exchange'
        ));
    }
}
