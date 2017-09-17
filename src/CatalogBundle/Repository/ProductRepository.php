<?php
namespace CatalogBundle\Repository;

use Doctrine\ORM\EntityRepository;
use CatalogBundle\Entity\Product;
use Symfony\Component\HttpFoundation\Response;

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

    public function getByPage($page, $per_page, $ordered_by, $direction, $filtered_by, $column)
    {
        if ($filtered_by !== 'all') {
            $products = $this->_em
                ->createQueryBuilder()
                ->select('p')
                ->from('CatalogBundle:Product', 'p')
                ->orderBy('p.' . $ordered_by, $direction)
                ->where('p.' . $filtered_by . '=' . $column)
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

    public function getCount()
    {
        return $this->_em
            ->createQueryBuilder()
            ->select('COUNT(p)')
            ->from('CatalogBundle:Product', 'p')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
