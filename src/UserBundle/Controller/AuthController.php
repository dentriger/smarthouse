<?php
/**
 * Created by PhpStorm.
 * User: Hp
 * Date: 22.09.2017
 * Time: 16:56
 */

namespace UserBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use UserBundle\Entity\Tokens;
use UserBundle\Entity\User;
use UserBundle\Form\ForgotPasswordForm;
use UserBundle\Form\RegistrationForm;
use UserBundle\Service\UserMail;
use UserBundle\Service\UserProfiler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class AuthController extends Controller
{

    private $userMail;
    private $userProfiler;

    public function __construct(UserMail $userMail,UserProfiler $userProfiler)
    {
        $this->userMail = $userMail;
        $this->userProfiler = $userProfiler;
    }
    /**
     * @Route("/sign", name="user_sign")
     */
    public function signAction(Request $request, AuthenticationUtils $authUtils)
    {
        $error = $authUtils->getLastAuthenticationError();
        $lastUsername = $authUtils->getLastUsername();
        $em = $this->getDoctrine()->getManager();
        $formRegister = $this->createForm(RegistrationForm::class);
        $formForgot = $this->createForm(ForgotPasswordForm::class);
        $formForgot->handleRequest($request);
        $formRegister->handleRequest($request);
        if ($formRegister->isSubmitted() && $formRegister->isValid()) {
            $user = $em->getRepository(User::class)->saveUser(
                $this->userProfiler->createUser($formRegister)
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
            'security/login.html.twig', [
                'formRegister' => $formRegister->createView(),
                'formForgot' => $formForgot->createView(),
                'last_username' => $lastUsername,
                'error'         => $error,
        ]);
    }
}