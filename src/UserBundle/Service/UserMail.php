<?php
namespace UserBundle\Service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Templating\EngineInterface;
use UserBundle\Entity\Tokens;
use UserBundle\Entity\User;
use Doctrine\ORM\EntityManager;

class UserMail
{
    const REGISTRATION = 'registration';
    const FORGOTPASSWORD = 'forgotPassword';
    const REGISTRATIONTWIG = 'Emails/registration.html.twig';
    const FORGOTPASSWORDTWIG = 'Emails/forgotPassword.html.twig';

    public function __construct(\Swift_Mailer $mailer, EngineInterface $twigEngine)
    {
        $this->mailer = $mailer;
        $this->twigEngine = $twigEngine;
    }

    public function sendMail(User $user,Tokens $token, $mail)
    {
        $message = (new \Swift_Message('SmartHouse'))
            ->setFrom('admin@example.com')
            ->setTo($user->getEmail())
            ->setBody(
             $this->twigEngine->render($mail,[
                 'name' => $user->getUsername(),
                 'hash' => $token->getToken()
             ])
            );

        $this->mailer->send($message);
    }

    public function mail($mail)
    {
        $twig = '';
        switch ($mail) {
            case self::REGISTRATION:
                $twig = self::REGISTRATIONTWIG;
                break;
            case self::FORGOTPASSWORD:
                $twig = self::FORGOTPASSWORDTWIG;
                break;
        }
        return $twig;
    }
}
