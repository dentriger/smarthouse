<?php
namespace CatalogBundle\Repository;

use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use CatalogBundle\Entity\Category;

class CategoryRepository extends NestedTreeRepository
{
    public function save(Category $category)
    {
        $this->_em->persist($category);
        $this->_em->flush();
    }

    public function remove(Category $category)
    {
        if ($category != null) {
            $this->removeFromTree($category);
            $this->_em->remove($category);
            $this->_em->flush();
            $this->_em->clear();
        }
    }
    public function getAllByIdNameLvl()
    {
        return $this->_em->createQueryBuilder()
            ->select('n.id, n.title, n.lvl')
            ->from('CatalogBundle:Category', 'n')
            ->getQuery()
            ->getResult();
    }
}
