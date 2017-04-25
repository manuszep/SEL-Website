<?php

namespace ArticleBundle\Form;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use SelDocumentBundle\DataTransformer\DocumentTransformer;

class ArticleType extends AbstractType
{
    private $manager;

    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $documentTransformer = new DocumentTransformer($this->manager, 'documents');

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

        $builder->add('documents', FileType::class, array(
            'multiple' => true,
            'data_class' => null,
            'required' => false,
            'label' => 'label.documents',
        ));

        /*$builder->add('documents', CollectionType::class, array(
            'entry_type' => IntegerType::class,
            'allow_add' => true
        ));*/

        $builder->add('save', SubmitType::class, array(
            'attr' => array('class' => 'main'),
            'label' => 'label.save'
        ));

        $builder->get('documents')->addModelTransformer($documentTransformer);
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
