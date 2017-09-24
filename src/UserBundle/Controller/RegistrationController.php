<?php

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use UserBundle\Entity\Tokens;
use UserBundle\Form\RegistrationForm;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use UserBundle\Service\UserMail;
use UserBundle\Service\UserProfiler;
use UserBundle\Repository\UserRepository;

class RegistrationController extends Controller
{
    private $userMail;
    private $userProfiler;

    public function __construct(UserMail $userMail,UserProfiler $userProfiler)
    {
        $this->userMail = $userMail;
        $this->userProfiler = $userProfiler;
    }

    /**
     * @Route("/register", name="user_registration")
     */
    public function registerAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(RegistrationForm::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $em->getRepository(User::class)->saveUser(
                $this->userProfiler->createUser($form)
            );
            $token = $em->getRepository(Tokens::class)->saveToken(
                $this->userProfiler->generateToken($user)
            );

            $this->userMail->sendMail($user,$token,$this->userMail::REGISTRATIONTWIG); //change mail to $this->mail::REGISTRATIONTWIG
            $this->addFlash(
                'notice',
                'You are register!Please check your email for activate your profile!'
            );
            return $this->redirectToRoute('home');
        }

        return $this->render(
            'default/registration.html.twig', [
                'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/activate{hash}", name="user_activate")
     */
    public function activateAction($hash)
    {
        $em = $this->getDoctrine()->getManager();
        $token = $em->getRepository(Tokens::class)->findOneBy(['token'=> $hash]);
        if ($token) {
            $valid = $this->userProfiler->tokenIsValid($token);

            if (!$valid) {
                $this->addFlash(
                    'notice',
                    'Your token invalid! For reset your password, go to forgotPassword page!'
                );
            }
            else {
                $user = $em->getRepository(User::class)->findOneBy(['id' => $token->getUserId()]);
                $em->getRepository(User::class)->saveUser(
                    $this->userProfiler->activateUser($user));
                $this->addFlash(
                    'notice',
                    'Your profile is activate! Go to login page for watch products!'
                );
                $em->getRepository(Tokens::class)->removeToken($token);
            }
        }
        else {
            $this->addFlash(
                'error',
                'Token not found!'
            );
        }

        return $this->redirectToRoute('home');

    }
}