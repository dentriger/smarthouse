<?php
namespace CatalogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use CatalogBundle\Form\Product\SubmitProductType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProductController extends Controller
{
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
        return $this->render('add_product.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function editProductAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $editable_product = $em
            ->getRepository('CatalogBundle:Product')
            ->findOneBy(array('id' => $id));
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

        return $this->render('add_product.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function getProductByIdAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository('CatalogBundle:Product')->findOneBy(array('id' => $id));
        $htmlTree = $this->get('app.category_menu_generator')->getMenu();
        return $this->render('single_product.html.twig', compact('htmlTree', 'product'));
    }

    public function removeProductAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $em->getRepository('CatalogBundle:Product')->remove(
            $em->getRepository('CatalogBundle:Product')->findOneBy(array('id' => $id))
        );
        return new Response();
    }

    public function gridProductsAction()
    {
        return $this->render('product_crud.html.twig');
    }

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

    public function getCountAction()
    {
        $result = $this->getDoctrine()->getManager()
            ->getRepository('CatalogBundle:Product')
            ->getCount();

        return new JsonResponse(array('count' => $result));
    }
}
