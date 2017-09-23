<?php
namespace CatalogBundle\Form\Product;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use CatalogBundle\Form\Type\CategoryType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SubmitProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('image', FileType::class, [
                'attr' => [
                    'label' => 'Image File',
                    'class' => 'file',
                    'style' => 'margin-bottom:15px;',
                    'data-allowed-file-extensions' => '["jpg", "png"]'
                ],
                'required' => false,
            ])
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'margin-bottom:15px;'
                ]
            ])
            ->add('category', CategoryType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'margin-bottom:15px'
                ]
            ])
            ->add('sku', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'margin-bottom:15px'
                ]
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'margin-bottom:15px'
                ]
            ])
//            ->add('similar_products', ProductType::class, [
//                'attr' => [
//                    'class' => 'form-control',
//                    'style' => 'margin-bottom:15px'
//                ]
//            ])
            ->add('state_flag', CheckboxType::class, [
                'attr' => [
                    'class' => 'checkbox-inline',
                    'style' => 'margin: 10px;'
                ],
                'required' => false,
                'empty_data' => false,
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Submit',
                'attr' => [
                    'class' => 'btn btn-primary',
                    'style' => 'margin-bottom:15px'
                ]
            ]);
    }
}