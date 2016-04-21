<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
                'placeholder' => 'Membre qui reçoit des noeuds',
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
                'placeholder' => 'Membre qui donne des noeuds',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.username', 'ASC')
                        ->where('u.locked = :locked')
                        ->setParameter('locked', '0');
                }
            ))
            ->add('message', TextareaType::class, array(
                'required' => false
            ))
            ->add('amount', NumberType::class, array(
                'scale' => 2,
                'attr' => array(
                    'step' => 0.25
                )
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
