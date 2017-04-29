<?php

namespace ArticleBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use SelDocumentBundle\Form\DocumentType;
use SelDocumentBundle\DataTransformer\DocumentsCollectionTransformer;
use SelDocumentBundle\DataTransformer\DocumentTransformer;
use SelDocumentBundle\Entity\DocumentManager;

class ArticleType extends AbstractType
{
    private $manager;

    public function __construct(DocumentManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', TextType::class, array(
            'label' => 'label.title'
        ));

        $builder->add('body', TextareaType::class, array(
            'required' => true,
            'label' => 'label.content',
            'attr' => array(
                'class' => "wysiwyg"
            )
        ));

        $builder->add('published', CheckboxType::class, array(
            'label'    => 'label.published',
            'required' => false,
        ));

        $builder->add('picture', DocumentType::class, array(
            'label' => 'label.picture',
            'required' => false,
            'subfolder' => 'article'
        ));

        $picture_transformer = new DocumentTransformer($this->manager, 'article');
        $builder->get('picture')->addModelTransformer($picture_transformer);

        $builder->add('documents', CollectionType::class, array(
            'allow_add' => true,
            'allow_delete' => true,
            'prototype' => true,
            'entry_type'   => DocumentType::class,
            'by_reference' => false,
            'entry_options'  => array(
                'multiple' => true,
                'required' => false,
                'label' => 'label.documents',
                'subfolder' => 'documents'
            ),
        ));

        $transformer = new DocumentsCollectionTransformer($this->manager, 'documents');
        $builder->get('documents')->addModelTransformer($transformer);

        $builder->add('published_at', DateType::class, array(
            'required' => false,
            'widget' => 'single_text',
            'required' => false,
            'label' => 'label.publishedAt',
            'format' => 'dd/MM/yyyy',
            'attr' => array(
                'placeholder' => 'placeholder.dateFormat'
            )
        ));

        $builder->add('expires_at', DateType::class, array(
            'required' => false,
            'widget' => 'single_text',
            'required' => false,
            'label' => 'label.expiresAt',
            'format' => 'dd/MM/yyyy',
            'attr' => array(
                'placeholder' => 'placeholder.dateFormat'
            )
        ));

        /*$builder->add('documents', CollectionType::class, array(
            'entry_type' => IntegerType::class,
            'allow_add' => true
        ));*/

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
