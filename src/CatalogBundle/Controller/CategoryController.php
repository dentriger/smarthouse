<?php
namespace CatalogBundle\Controller;

use CatalogBundle\Form\Category\SubmitCategoryType;
use CatalogBundle\Form\Category\UpdateCategoryType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class CategoryController extends Controller
{
    /**
     * @param $request
     * @Security("has_role('ROLE_MODERATOR')")
     * @Route(
     *     "/category/create",
     *     name="category_create"
     * )
     * @Method({"GET","POST"})
     * @return Response
     */
    public function createCategoryAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(SubmitCategoryType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->getRepository('CatalogBundle:Category')->save(
                $this
                    ->get('app.category_generator')
                    ->createCategory($form)
            );
            return $this->redirectToRoute('category_crud');
        }

        return $this->render('moderator/category/category_add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param $request
     * @param $id
     * @Security("has_role('ROLE_MODERATOR')")
     * @Route(
     *     "/category/{id}/edit",
     *     requirements={"id" = "\d+"},
     *     name="category_edit"
     * )
     * @Method({"GET","POST"})
     * @return Response
     */
    public function editCategoryAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $editableCategory = $em
            ->getRepository('CatalogBundle:Category')
            ->find($id);
        $form = $this->createForm(UpdateCategoryType::class);
        $form->setData($editableCategory->getCategoryDataToForm());
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em->getRepository('CatalogBundle:Category')->save(
                $this
                    ->get('app.category_generator')
                    ->updateCategory($form, $editableCategory)
            );
            return $this->redirectToRoute('category_crud');
        }

        return $this->render('moderator/category/category_add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param $request
     * @Security("has_role('ROLE_USER')")
     * @Route(
     *     "/category/all",
     *     name="products_all"
     * )
     * @Method({"GET"})
     * @return Response
     */
    public function getAllProductsAction(Request $request)
    {
        $perPage = $request->get('per_page') ? $request->get('per_page') : 8;
        $paginator = $this->get('knp_paginator');
        $pagination = $this
            ->get('app.category_paginator_generator')
            ->getPaginator($request, $paginator, 'all', $perPage);

        $htmlTree = $this->get('app.category_menu_generator')->getMenu();
        return $this->render('user/test.html.twig', compact('htmlTree', 'pagination'));
    }

    /**
     * @param $request
     * @param $id
     * @Security("has_role('ROLE_USER')")
     * @Route(
     *     "/category/{id}",
     *     requirements={"id" = "\d+"},
     *     name="products_by_category"
     * )
     * @Method({"GET"})
     * @return Response
     */
    public function getProductsByCategoryAction(Request $request, $id)
    {
        $perPage = $request->get('per_page') ? $request->get('per_page') : 8;
        $paginator = $this->get('knp_paginator');
        $pagination = $this
            ->get('app.category_paginator_generator')
            ->getPaginator($request, $paginator, $id, $perPage);

        $htmlTree = $this->get('app.category_menu_generator')->getMenu();
        return $this->render('user/test.html.twig', compact('htmlTree', 'pagination'));
    }

    /**
     * @Security("has_role('ROLE_MODERATOR')")
     * @Route(
     *     "/category/crud",
     *     name="category_crud"
     *     )
     * @Method({"GET","POST"})
     * @return Response
     */
    public function crudCategoryAction()
    {
        $htmlTree = $this
            ->get('app.category_generator')
            ->getCrud();

        return $this->render('moderator/category/category_crud.html.twig', compact('htmlTree'));
    }

    /**
     * @param Post
     * @Security("has_role('ROLE_MODERATOR')")
     * @Route(
     *     "/category/{id}/remove",
     *     requirements={"id" = "\d+"},
     *     name="category_remove"
     * )
     * @Method({"GET","POST"})
     * @return Response
     */
    public function removeCategoryAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $em->getRepository('CatalogBundle:Product')->removeByCategory($id);
        $em->getRepository('CatalogBundle:Category')->remove(
            $em->getRepository('CatalogBundle:Category')->find($id)
        );
        return $this->redirectToRoute('category_crud');
    }
}


