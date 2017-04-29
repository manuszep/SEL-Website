<?php

namespace SelDocumentBundle\Form;

use SelDocumentBundle\Entity\DocumentManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use SelDocumentBundle\DataTransformer\DocumentTransformer;
use Symfony\Component\Form\FormEvents;

class DocumentType extends AbstractType
{
    private $manager;

    public function __construct(DocumentManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $subfolder = "documents";

        if(isset($options['subfolder'])) {
            $subfolder = $options['subfolder'];
            unset($options['subfolder']);
        }

        $documentTransformer = new DocumentTransformer($this->manager, $subfolder);

        $builder->add('file',FileType::class, array(
            'multiple' => $options['multiple'],
            'required' => $options['required'],
            "image_path" => "path",
            "by_reference" => false
        ));

        $builder->add('subfolder', HiddenType::class, array(
            'data' => $subfolder,
        ));

        $builder->get('file')->addModelTransformer($documentTransformer);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SelDocumentBundle\Entity\Document',
            'subfolder' => 'documents',
            'multiple' => false
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'documentbundle_document';
    }
}