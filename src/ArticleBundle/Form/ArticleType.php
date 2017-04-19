<?php

namespace ArticleBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class ArticleType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', TextType::class, array(
            'label' => 'label.title'
        ));

        $builder->add('body', TextareaType::class, array(
            'required' => false,
            'label' => 'label.content',
            'attr' => array(
                'class' => "wysiwyg"
            )
        ));

        $builder->add('published', CheckboxType::class, array(
            'label'    => 'label.published',
            'required' => false,
        ));

        $builder->add('picture', FileType::class, array(
            'label' => 'label.picture',
            'required' => false
        ));

        $builder->add('published_at', DateType::class, array(
            'required' => false,
            'widget' => 'single_text',
            'required' => false,
            'label' => 'label.publishedAt',
            'format' => 'dd/MM/yyyy',
            'attr' => array(
                'placeholder' => 'jj/mm/aaaa'
            )
        ));

        $builder->add('expires_at', DateType::class, array(
            'required' => false,
            'widget' => 'single_text',
            'required' => false,
            'label' => 'label.expiresAt',
            'format' => 'dd/MM/yyyy',
            'attr' => array(
                'placeholder' => 'jj/mm/aaaa'
            )
        ));

        $builder->add('save', SubmitType::class, array(
            'attr' => array('class' => 'main'),
            'label' => 'label.save'
        ));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ArticleBundle\Entity\Article'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'articlebundle_article';
    }


}
