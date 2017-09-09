<?php

namespace CatalogBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\Form;
use CatalogBundle\Entity\Category;

class CategoryGenerator
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function createCategory(Form $form)
    {
        $category = new Category();
        if (!is_null($form->get('parent_category')->getData())) {
            $category->setParent(
                $this->em
                    ->getRepository('CatalogBundle:Category')
                    ->findOneBy(array('id' => $form->get('parent_category')->getData()))
            );
        }
        $category->setTitle($form->get('title')->getData());
        $category->setStateFlag($form->get('state_flag')->getData());
        return $category;
    }

    public function updateCategory(Form $form, Category $category)
    {
        if (!is_null($form->get('parent_category')->getData())) {
            $category->setParent(
                $this->em
                    ->getRepository('CatalogBundle:Category')
                    ->findOneBy(array('id' => $form->get('parent_category')->getData()))
            );
        }
        $category->setTitle($form->get('title')->getData());
        $category->setStateFlag($form->get('state_flag')->getData());
        return $category;
    }
}