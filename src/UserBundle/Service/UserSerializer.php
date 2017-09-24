<?php


namespace UserBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Response;

class UserSerializer
{

    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param $page
     * @param $per_page
     * @param $ordered_by
     * @param $direction
     * @param $filtered_by
     * @param $column
     * @return Response
     */
    public function serializeProducts(
        $page,
        $per_page,
        $ordered_by,
        $direction,
        $filtered_by,
        $column
    ) {

        $encoder = new JsonEncoder();
        $normalizer = new ObjectNormalizer();

        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });

        $normalizer->setCircularReferenceLimit(0);
        $normalizer->setIgnoredAttributes([
            // 'creationTime',
            // 'lastModification',
            'description',
            'image',
            'parent',
            'children',
            'products',
            'productDataToForm',
            'categoryDataToForm',
            'password',
            'isActive',
            'roles',
            'plainPassword',
            'enabled',
            'salt'
        ]);

        $serializer = new Serializer([$normalizer], [$encoder]);

        $products = $this->em
            ->getRepository('UserBundle:User')
            ->getByPage($page, $per_page, $ordered_by, $direction, $filtered_by, $column);
        $response = new Response($serializer->serialize($products, 'json'));
        $response->headers->set('Content-Type', 'application/vnd.api+json');

        return $response;
    }

}