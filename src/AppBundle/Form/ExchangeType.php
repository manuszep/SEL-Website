<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\DataTransformer\CommaToDotTransformer;
use Doctrine\ORM\EntityRepository;

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
                'placeholder' => 'placeholder.pleaseChoose',
                'label' => 'label.creditUser',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.username', 'ASC')
                        ->where('u.locked = :locked')
                        ->setParameter('locked', '0');
                }
            ))
            ->add('debitUser', EntityType::class, array(
                'class' => 'AppBundle:User',
                'choice_label' => 'username',
                'placeholder' => 'placeholder.pleaseChoose',
                'label' => 'label.debitUser',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.username', 'ASC')
                        ->where('u.locked = :locked')
                        ->setParameter('locked', '0');
                }
            ))
            ->add('service', EntityType::class, array(
                'class' => 'AppBundle:Service',
                'choice_label' => 'select_label',
                'placeholder' => 'placeholder.pleaseChoose',
                'label' => 'label.targetService'
            ))
            ->add('message', TextareaType::class, array(
                'required' => false,
                'label' => 'label.message'
            ))
            ->add('amount', NumberType::class, array(
                'scale' => 2,
                'attr' => array(
                    'step' => 0.25
                ),
                'label' => 'label.amount'
            ))
            ->add('save', SubmitType::class, array(
                'attr' => array('class' => 'main'),
                'label' => 'label.save'
            ))
        ;

        $builder->get('amount')->addModelTransformer(new CommaToDotTransformer());
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
