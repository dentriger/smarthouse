<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use UserBundle\Entity\User;

class DefaultController extends Controller
{
    /**
     * @Route("/admin", name="adminPanel")
     */
    public function indexAction()
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $users = $repository->findAll();
        return $this->render('admin/adminPanel.html.twig',[
            'users'=>$users
        ]);
    }
}
