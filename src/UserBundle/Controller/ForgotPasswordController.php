<?php
/**
 * Created by PhpStorm.
 * User: Hp
 * Date: 18.09.2017
 * Time: 13:25
 */

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use UserBundle\Entity\Tokens;
use UserBundle\Form\ForgotPasswordForm;
use UserBundle\Form\NewPasswordForm;
use UserBundle\Form\RegistrationForm;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use UserBundle\Service\UserMail;
use UserBundle\Service\UserProfiler;

class ForgotPasswordController extends Controller
{
    private $userMail;
    private $userProfiler;

    public function __construct(UserMail $userMail,UserProfiler $userProfiler)
    {
        $this->userMail = $userMail;
        $this->userProfiler = $userProfiler;
    }

    /**
     * @Route("/forgotpassword", name="forgotpassword")
     */
    public function forgotPasswordAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(ForgotPasswordForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $email = $form->get('email')->getData();
            $user = $em->getRepository(User::class)->findOneBy(['email'=>$email]);
            if($user->getIsActive() == true){
                $token = $em->getRepository(Tokens::class)->findOneBy(['userId'=>$user->getId()]);
                if(!$token) {
                    $new = $this->userProfiler->generateToken($user);
                }
                else {
                    $new = $this->userProfiler->generateToken($user, $token, false);
                }

                $newToken = $em->getRepository(Tokens::class)->saveToken($new);

                $this->userMail->sendMail($user,$newToken,$this->userMail::FORGOTPASSWORDTWIG);//change mail to $this->mail::FORGOTPASSWORDTWIG

                return $this->redirectToRoute('home');
            }
            else{
                $this->addFlash(
                    'notice',
                    'Your profile is not activate! Check your email to activate your profile'
                );
                return $this->redirectToRoute('home');
            }
        }

        return $this->render(
            'default/forgotPassword.html.twig', [
                'form' => $form->createView()
            ]);
    }

    /**
     * @Route("/newpassword{hash}", name="newpassword")
     */
    public function newPasswordAction(Request $request, $hash)
    {
        $em = $this->getDoctrine()->getManager();
        $token = $em->getRepository(Tokens::class)->findOneBy(['token' => $hash]);



        if ($token) {
            $form = $this->createForm(NewPasswordForm::class);

            $form->handleRequest($request);
            if ($form->isSubmitted()) {
                $user = $em->getRepository(User::class)->findOneBy(['id'=>$token->getUserId()]);
                $em->getRepository(User::class)->saveUser(
                    $this->userProfiler->setEncodePassword($user,$form,false)
                );

                return $this->redirectToRoute('home');
            }
            return $this->render(
                'default/newPassword.html.twig', [
                    'form' => $form->createView()
                ]);
        }
        else {
            return $this->redirectToRoute('home');
        }
    }
}