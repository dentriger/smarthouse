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

    public function removeByCategory($id)
    {
        $products = $this->getByCategoryId($id);
        foreach ($products as $product) {
            $this->_em->remove($product);
        }
        $this->_em->flush();
    }

    public function getByCategoryId($id)
    {
        $products = $this->_em
            ->createQueryBuilder()
            ->select('p')
            ->from('CatalogBundle:Product', 'p')
            ->where('p.category=' . $id)
            ->getQuery()
            ->getResult();
        return $products;
    }

    public function getByPage($page, $per_page, $ordered_by, $direction, $filtered_by, $column)
    {
        if ($filtered_by !== 'all') {
            $products = $this->_em
                ->createQueryBuilder()
                ->select('p')
                ->from('CatalogBundle:Product', 'p')
                ->orderBy('p.' . $ordered_by, $direction)
                ->where('p.' . $filtered_by . '= :column')->setParameter(":column", $column)
                ->setFirstResult(($page - 1) * $per_page)
                ->setMaxResults($per_page)
                ->getQuery()
                ->getResult();
        }else {
            $products = $this->_em
                ->createQueryBuilder()
                ->select('p')
                ->from('CatalogBundle:Product', 'p')
                ->orderBy('p.' . $ordered_by, $direction)
                ->setFirstResult(($page - 1) * $per_page)
                ->setMaxResults($per_page)
                ->getQuery()
                ->getResult();
        }

        return $products;
    }

    public function getCount($filtered_by, $column)
    {
        if ($filtered_by !== 'all') {
            $result = $this->_em
                ->createQueryBuilder()
                ->select('COUNT(p)')
                ->from('CatalogBundle:Product', 'p')
                ->where('p.' . $filtered_by . '= :column')->setParameter(":column", $column)
                ->getQuery()
                ->getSingleScalarResult();
        }else{
            $result = $this->_em
                ->createQueryBuilder()
                ->select('COUNT(p)')
                ->from('CatalogBundle:Product', 'p')
                ->getQuery()
                ->getSingleScalarResult();
        }
        return $result;
    }

    public function getAllIdNameProducts()
    {
        $products = $this->_em
            ->createQueryBuilder()
            ->select('p.id','p.name')
            ->from('CatalogBundle:Product', 'p')
            ->getQuery()
            ->getResult();
        return $products;
    }
}
