<?php
namespace CatalogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use CatalogBundle\Form\Product\SubmitProductType;

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
        return $this->redirectToRoute('product_crud');
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
        $direction = $request->get('direction') ? $request->get('direction') : 0;

        $result = $this
            ->get('app.product_serializer')
            ->serializeProducts(
                $page,
                $per_page,
                $ordered_by,
                $direction
            );

        return $result;
    }
}
