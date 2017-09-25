<?php

namespace CatalogBundle\Form\Type;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ProductType extends AbstractType
{
    private $em;
    private $products;
    private $validProducts;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $this->products = $this->em
            ->getRepository('CatalogBundle:Product')
            ->getAllIdNameProducts();

        foreach ($this->products as $product) {
            $this->validProducts[$product['name']] = $product['name'];
        }

        $resolver->setDefaults([
            'choices' => $this->validProducts
        ]);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}