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
                    ->find($form->get('parent_category')->getData())
            );
        }
        $category->setTitle($form->get('title')->getData());
        if (!$form->get('state_flag')->getData()) $category->setStateFlag(0);
        else $category->setStateFlag(1);
        return $category;
    }

    public function updateCategory(Form $form, Category $category)
    {
        if (($form->get('parent_category')->getData())!==$category->getId()) {
            $category->setParent(
                $this->em
                    ->getRepository('CatalogBundle:Category')
                    ->find($form->get('parent_category')->getData())
            );
        }
        $category->setTitle($form->get('title')->getData());
        $category->setStateFlag($form->get('state_flag')->getData());
        return $category;
    }

    public function getCrud()
    {
        $repo = $this->em->getRepository('CatalogBundle:Category');
        $options = [
            'decorate' => true,
            'rootOpen' => '<ul>',
            'rootClose' => '</ul>',
            'childOpen' => '<li style="margin-bottom:15px;margin-top: 15px;">',
            'childClose' => '</li>',
            'nodeDecorator' => function ($node) {
                return '<a href="/category/' . $node['id'] . '">' . $node['title'] . '</a>
                <a href="/category/' . $node['id'] . '/edit"  class="btn btn-sm btn-primary">edit</a>
                <a href="/category/' . $node['id'] . '/remove" class="btn btn-sm btn-danger">delete</a>';
            }
        ];
        $htmlTree = $repo->childrenHierarchy(
            null,
            false,
            $options
        );
        return $htmlTree;
    }
}