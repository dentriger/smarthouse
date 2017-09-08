<?php
namespace CatalogBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Form;
use CatalogBundle\Entity\Product;

class ProductRepository extends EntityRepository
{
    public function insertDataFromForm(Form $form)
    {
        $fileName = $form->get('image')->getData();
        $now = new\DateTime('now');
        $created_product = new Product();
        $created_product->setName($form->get('name')->getData());
        $created_product->setStateFlag($form->get('state_flag')->getData());

        $created_product->setCategory(
            $this->_em
                ->getRepository('CatalogBundle:Category')
                ->findOneBy(array('id' => $form->get('category')->getData()))
        );

        $created_product->setDescription($form->get('description')->getData());
        $created_product->setSku($form->get('sku')->getData());
        $created_product->setCreationTime($now);
        $created_product->setLastModification($now);
        $created_product->setImage($fileName);
        $this->_em->persist($created_product);
        $this->_em->flush();
    }

    public function updateDataFromForm(Form $form, Product $product)
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
            $this->_em
                ->getRepository('CatalogBundle:Category')
                ->findOneBy(array('id' => $form->get('category')->getData()))
        );

        $product->setDescription($form->get('description')->getData());
        $product->setSku($form->get('sku')->getData());
        $product->setLastModification($now);
        $this->_em->persist($product);
        $this->_em->flush();
    }

    public function getByPage($page, $per_page, $ordered_by, $direction)
    {
        if ($direction) {
            $directionDQL = 'ASC';
        } else {
            $directionDQL = 'DESC';
        }

        $products = $this->_em
            ->createQueryBuilder()
            ->select('p')
            ->from('CatalogBundle:Product', 'p')
            ->orderBy('p.' . $ordered_by, $directionDQL)
            ->setFirstResult(($page-1)*$per_page)
            ->setMaxResults($per_page)
            ->getQuery()
            ->getResult();

        return $products;
    }

    public function getByCategory($category_id)
    {

        $products = $this->_em
            ->createQueryBuilder()
            ->select('p')
            ->from('CatalogBundle:Product', 'p')
            ->where('p.category=' . $category_id)
            ->getQuery();

        return $products;
    }
}
