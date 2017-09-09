<?php

namespace CatalogBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\Form;
use CatalogBundle\Entity\Product;

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
        $product->setName($form->get('name')->getData());
        $product->setStateFlag($form->get('state_flag')->getData());

        $product->setCategory(
            $this->em
                ->getRepository('CatalogBundle:Category')
                ->findOneBy(array('id' => $form->get('category')->getData()))
        );

        $product->setDescription($form->get('description')->getData());
        $product->setSku($form->get('sku')->getData());
        $product->setCreationTime($now);
        $product->setLastModification($now);
        $product->setImage($fileName);
        return $product;
    }

    public function updateProduct(Form $form, Product $product)
    {
        if (!is_null($form->get('image')->getData())) {
            $fileName = $form->get('image')->getData();
            $product->setImage($fileName);
        }
        $now = new\DateTime('now');
        $product
            ->setName($form->get('name')->getData());
        $product->setStateFlag($form->get('state_flag')->getData());

        $product->setCategory(
            $this->em
                ->getRepository('CatalogBundle:Category')
                ->findOneBy(array('id' => $form->get('category')->getData()))
        );

        $product->setDescription($form->get('description')->getData());
        $product->setSku($form->get('sku')->getData());
        $product->setLastModification($now);
        return $product;
    }
}