<?php

namespace CatalogBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\Form;
use CatalogBundle\Entity\Product;
use Symfony\Component\Filesystem\Filesystem;

class ProductGenerator
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function createProduct(Form $form)
    {
        $fileName = $form->get('image')->getData();
        $now = new\DateTime('now');
        $product = new Product();

        if (!is_null($fileName)) {
            $file = md5(uniqid()) . '.' . $fileName->guessExtension();
            $fileName->move(
                'uploads/images',
                $file
            );
            $product->setImage($file);
        }

//        $similar_product_id = $form->get('similar_products')->getData();
//        if($similar_product_id!==NULL) {
//            $similar_product = $this->em
//                ->getRepository('CatalogBundle:Product')
//                ->find($similar_product_id));
//            $product->addSimilarProduct($similar_product);
//            $this->em->getRepository('CatalogBundle:Product')->save($similar_product);
//        }

        $product->setName($form->get('name')->getData());
        if (!$form->get('state_flag')->getData()) $product->setStateFlag(0);
        else $product->setStateFlag(1);

        $product->setCategory(
            $this->em
                ->getRepository('CatalogBundle:Category')
                ->findOneBy(array('id' => $form->get('category')->getData()))
        );

        $product->setDescription($form->get('description')->getData());
        $product->setSku($form->get('sku')->getData());
        $product->setCreationTime($now->format('Y-m-d H:i:s'));
        $product->setLastModification($now->format('Y-m-d H:i:s'));
        return $product;
    }

    public function updateProduct(Form $form, Product $product)
    {
        if (!is_null($form->get('image')->getData())) {
            $fileName = $form->get('image')->getData();
            $file = md5(uniqid()).'.'.$fileName->guessExtension();
            $fileName->move(
                'uploads/images',
                $file
            );
            $product->setImage($file);
        }
        $now = new\DateTime('now');
        $product->setName($form->get('name')->getData());
        $product->setStateFlag($form->get('state_flag')->getData());

        $product->setCategory(
            $this->em
                ->getRepository('CatalogBundle:Category')
                ->findOneBy(array('id' => $form->get('category')->getData()))
        );

        $product->setDescription($form->get('description')->getData());
        $product->setSku($form->get('sku')->getData());
        $product->setLastModification($now->format('Y-m-d H:i:s'));
        return $product;
    }

    public function removeImage($image)
    {
        $fs = new Filesystem();
        $fs->remove(array('file', 'uploads/images/'.$image));
    }
}