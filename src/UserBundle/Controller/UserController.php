<?php
/**
 * Created by PhpStorm.
 * User: Hp
 * Date: 24.09.2017
 * Time: 11:27
 */

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends Controller
{
    /**
     * @param $request
     * @Route("/admin/user/ajax", name="user_ajax")
     * {"GET"}
     * @return Response
     */
    public function getProductsAjaxAction(Request $request)
    {
        $page = $request->get('page') ? $request->get('page') : 1;
        $per_page = $request->get('per_page') ? $request->get('per_page') : 5;
        $ordered_by = $request->get('ordered_by') ? $request->get('ordered_by') : 'id';
        $direction = $request->get('direction') ? $request->get('direction') : 'DESC';
        $filtered_by = $request->get('filtered_by') ? $request->get('filtered_by') : 'all';
        $column = $request->get('column') ? $request->get('column') : 1;

        $result = $this
            ->get('app.user_serializer')
            ->serializeProducts(
                $page,
                $per_page,
                $ordered_by,
                $direction,
                $filtered_by,
                $column
            );

        return $result;
        //return new Response(1);
    }

    /**
     * @param $request
     * @Route("/admin/user/ajax/count", name="user_count")
     * {"GET"}
     * @return Response
     */
    public function getCountAction(Request $request)
    {
        $filtered_by = $request->get('filtered_by') ? $request->get('filtered_by') : 'all';
        $column = $request->get('column') ? $request->get('column') : 1;
        $result = $this->getDoctrine()->getManager()
            ->getRepository('UserBundle:User')
            ->getCount($filtered_by, $column);

        return new JsonResponse(array('count' => $result));
    }

}