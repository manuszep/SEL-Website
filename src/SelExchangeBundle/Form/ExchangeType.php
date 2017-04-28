<?php

namespace SelExchangeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\DataTransformer\CommaToDotTransformer;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class ExchangeType extends AbstractType
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
        $builder->add('title', TextType::class, array(
            'label' => 'label.title'
        ));
        
        if ($this->authorizationChecker->isGranted('ROLE_EDITOR')) {
            $builder->add('debitUser', EntityType::class, array(
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
            ));

            $builder->add('creditUser', EntityType::class, array(
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
            ));
        } else {
            $builder->add('creditUser', EntityType::class, array(
                'class' => 'AppBundle:User',
                'choice_label' => 'username',
                'placeholder' => 'placeholder.pleaseChoose',
                'label' => 'label.creditUser',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.username', 'ASC')
                        ->where('u.locked = :locked')
                        ->andWhere('u.id != :id')
                        ->setParameter('locked', '0')
                        ->setParameter('id', $this->tokenStorage->getToken()->getUser()->getId());
                }
            ));
        }


            $builder->add('message', TextareaType::class, array(
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
            'data_class' => 'SelExchangeBundle\Entity\Exchange'
        ));
    }
}
