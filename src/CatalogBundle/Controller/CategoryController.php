<?php
namespace CatalogBundle\Controller;

use CatalogBundle\Entity\Category;
use CatalogBundle\Form\Category\SubmitCategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CategoryController extends Controller
{
    public function createCategoryAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(SubmitCategoryType::class);
        $categoryRepo = $em->getRepository('CatalogBundle:Category');
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $category = new Category();
            if (!is_null($form->get('parent_category')->getData())) {
                $category->setParent(
                    $categoryRepo->findOneBy(array('id' => $form->get('parent_category')->getData()))
                );
            }
            $category->setTitle($form->get('title')->getData());
            $category->setStateFlag($form->get('state_flag')->getData());
            $em->persist($category);
            $em->flush();
            return $this->redirectToRoute('category_crud');
        }

        return $this->render('category_add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function editCategoryAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $categoryRepo = $em->getRepository('CatalogBundle:Category');
        $category =  $categoryRepo->findOneBy(array('id' => $id));

        $form = $this->createForm(SubmitCategoryType::class);
        $form->setData($category->getDataToForm());

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if (!is_null($form->get('parent_category')->getData())) {
                $category->setParent(
                    $categoryRepo->findOneBy(array('id' => $form->get('parent_category')->getData()))
                );
            }
            $category->setTitle($form->get('title')->getData());
            $category->setStateFlag($form->get('state_flag')->getData());
            $em->persist($category);
            $em->flush();
            return $this->redirectToRoute('category_crud');
        }

        return $this->render('category_add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function getAllProductsAction(Request $request)
    {
        $per_page = 5;
        $paginator = $this->get('knp_paginator');
        $pagination = $this
            ->get('app.category_paginator_generator')
            ->getPaginator($request, $paginator, 'all', $per_page);

        $htmlTree = $this->get('app.category_menu_generator')->getMenu();
        return $this->render('test.html.twig', compact('htmlTree', 'id', 'pagination'));
    }

    public function getProductsByCategoryAction(Request $request, $id)
    {
        $per_page = $request->get('per_page') ? $request->get('per_page') : 8;
        $paginator = $this->get('knp_paginator');
        $pagination = $this
            ->get('app.category_paginator_generator')
            ->getPaginator($request, $paginator, $id, $per_page);

        $htmlTree = $this->get('app.category_menu_generator')->getMenu();
        return $this->render('test.html.twig', compact('htmlTree', 'id', 'pagination'));
    }

    public function crudCategoryAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('CatalogBundle:Category');
        $options = array(
            'decorate' => true,
            'rootOpen' => '<ul>',
            'rootClose' => '</ul>',
            'childOpen' => '<li style="margin-bottom:15px;margin-top: 15px;">',
            'childClose' => '</li>',
            'nodeDecorator' => function ($node) {
                return '<a href="/category/' . $node['id'] . '">' . $node['title'] . '</a>
                <a href="/category/' . $node['id'] .
                '/edit"  class="btn btn-sm btn-primary">edit</a>
                <a href="/category/' . $node['id'] .
                '/remove" class="btn btn-sm btn-danger">delete</a>';
            }
        );
        $htmlTree = $repo->childrenHierarchy(
            null,
            false,
            $options
        );
        return $this->render('category_crud.html.twig', compact('htmlTree'));
    }

    public function removeCategoryAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $categoryRepo = $em->getRepository('CatalogBundle:Category');
        $em->remove($categoryRepo->findOneBy(array('id' => $id)));
        $em->flush();
        return $this->redirectToRoute('category_crud');
    }
}


