<?php
namespace CatalogBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use CatalogBundle\Form\Product\SubmitProductType;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProductController extends Controller
{
    /**
     * @param Request $request
     * @Security("has_role('ROLE_MODERATOR')")
     * @Route("/product/create", name="product_create")
     * @Method({"GET","POST"})
     * @return Response
     */
    public function createProductAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(SubmitProductType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em->getRepository('CatalogBundle:Product')->save(
                $this
                ->get('app.product_generator')
                ->createProduct($form)
            );
            return $this->redirectToRoute('product_crud');
        }
        return $this->render('moderator/product/add_product.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param $request
     * @param $id
     * @Security("has_role('ROLE_MODERATOR')")
     * @Route(
     *     "/product/{id}/edit",
     *     requirements={"id" = "\d+"},
     *     name="product_edit"
     * )
     * @Method({"GET","POST"})
     * @return Response
     */
    public function editProductAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $editable_product = $em
            ->getRepository('CatalogBundle:Product')
            ->find($id);
        $form = $this->createForm(SubmitProductType::class);
        $form->setData($editable_product->getProductDataToForm());
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em->getRepository('CatalogBundle:Product')->save(
                $this
                    ->get('app.product_generator')
                    ->updateProduct($form, $editable_product)
            );
            return $this->redirectToRoute('product_crud');
        }

        return $this->render('moderator/product/add_product.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param $id
     * @Security("has_role('ROLE_USER')")
     * @Route(
     *     "/product/{id}",
     *     requirements={"id" = "\d+"},
     *     name="product_by_id"
     * )
     * @Method({"GET"})
     * @return Response
     */
    public function getProductByIdAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository('CatalogBundle:Product')->find($id);
        $htmlTree = $this->get('app.category_menu_generator')->getMenu();
        return $this->render('user/single_product.html.twig', compact('htmlTree', 'product'));
    }

    /**
     * @param Post
     * @Security("has_role('ROLE_MODERATOR')")
     * @Route(
     *     "/product/{id}/remove",
     *     requirements={"id" = "\d+"},
     *     name="product_remove"
     * )
     * @Method({"GET","POST"})
     * @return Response
     */
    public function removeProductAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository('CatalogBundle:Product')->find($id);
        $image = $product->getImage();
        $em->getRepository('CatalogBundle:Product')->remove($product);
        $this
            ->get('app.product_generator')
            ->removeImage($image);
        return new Response();
    }

    /**
     * @Security("has_role('ROLE_MODERATOR')")
     * @Route("/product/crud", name="product_crud")
     * @Method({"GET"})
     */
    public function gridProductsAction()
    {
        return $this->render('moderator/product/product_crud.html.twig');
    }

    /**
     * @param $request
     * @Security("has_role('ROLE_MODERATOR')")
     * @Route("/product/ajax", name="product_ajax")
     * @Method({"GET"})
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
            ->get('app.product_serializer')
            ->serializeProducts(
                $page,
                $per_page,
                $ordered_by,
                $direction,
                $filtered_by,
                $column
            );

        return $result;
    }

    /**
     * @param $request
     * @Security("has_role('ROLE_MODERATOR')")
     * @Route("/product/ajax/count", name="product_count")
     * @Method({"GET"})
     * @return Response
     */
    public function getCountAction(Request $request)
    {
        $filtered_by = $request->get('filtered_by') ? $request->get('filtered_by') : 'all';
        $column = $request->get('column') ? $request->get('column') : 1;
        $result = $this->getDoctrine()->getManager()
            ->getRepository('CatalogBundle:Product')
            ->getCount($filtered_by, $column);

        return new JsonResponse(array('count' => $result));
    }
}
