<?php
namespace UserBundle\Service;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Templating\EngineInterface;
use UserBundle\Entity\Tokens;
use UserBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use UserBundle\Form\RegistrationForm;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserProfiler
{
    private $passwordEncoder;
    private $token;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->hash = md5(uniqid(null, true));
        $this->assda = sha1('lol',null);
        $this->date = new \DateTime('now');
        $this->nextDate = new \DateTime('now +3 day');
        $this->token = new Tokens();
    }

    public function createUser(Form $form)
    {
        $user = $form->getData();
        $this->setEncodePassword($user);

        return $user;
    }

    public function setEncodePassword($user,Form $form=null,$isNew = true)
    {
        if($isNew){
            $password = $this->passwordEncoder->encodePassword($user, $user->getPlainPassword());
        }
        else{
            $password = $this->passwordEncoder->encodePassword($user, $form->get('plainPassword')->getData());
        }
        return $user->setPassword($password);
    }

    public function generateToken(User $user,Tokens $token = null,$new = true)
    {
        $hash = md5(uniqid(null, true));
        if ($new)
        {
            $id = $user->getId();
            $newToken = $this->createToken($id, $hash);
        }
        else
        {
            $newToken = $this->updateToken($hash,$token);
        }
        return $newToken;
    }

    public function createToken($id, $hash)
    {
        $this->token->setUserId($id);
        $this->token->setToken($hash);
        $this->token->setCreateDate($this->getDate());
        $this->token->setDestroyDate($this->nextDate->format('Y-m-d H:i:s'));

        return $this->token;
    }

    public function updateToken($hash,$token)
    {
        $token->setToken($hash);
        return $token;
    }

    public function activateUser(User $user)
    {
        $user->setIsActive(true);
        return $user;
    }

    public function getDate()
    {
        $curentDate = $this->date;
        $date = $curentDate->format('Y-m-d H:i:s');
        return $date;
    }

    public function tokenIsValid(Tokens $token)
    {
        $isValid = ($token->getDestroyDate()) > $this->getDate();
        return $isValid;
    }
}