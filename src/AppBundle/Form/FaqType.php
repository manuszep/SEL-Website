<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FaqType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('question', TextType::class, array(
                'label' => 'label.question'
            ))
            ->add('answer', TextareaType::class, array(
                'label' => 'label.answer',
                'attr' => array(
                    'class' => "wysiwyg"
                )
            ))
            ->add('type', ChoiceType::class, array(
                'choices'  => array(
                    'Publique' => 0,
                    'Réservé aux membres' => 1,
                    'Réservé au COCO' => 2
                ),
                'label' => 'label.faqType'
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
            'data_class' => 'AppBundle\Entity\Faq'
        ));
    }
}