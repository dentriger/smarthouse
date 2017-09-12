<?php
namespace CatalogBundle\Repository;

use Doctrine\ORM\EntityRepository;
use CatalogBundle\Entity\Product;

class ProductRepository extends EntityRepository
{
    public function save(Product $product)
    {
        $this->_em->persist($product);
        $this->_em->flush();
    }

    public function remove(Product $product)
    {
        if ($product != null) {
            $this->_em->remove($product);
            $this->_em->flush();
        }
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

//    public function getByCategory($category_id)
//    {
//
//        $products = $this->_em
//            ->createQueryBuilder()
//            ->select('p')
//            ->from('CatalogBundle:Product', 'p')
//            ->where('p.category=' . $category_id)
//            ->getQuery();
//
//        return $products;
//    }
}
