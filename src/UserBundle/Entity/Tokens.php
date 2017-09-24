<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tokens
 *
 * @ORM\Table(name="tokens")
 * @ORM\Entity(repositoryClass="UserBundle\Repository\TokensRepository")
 */
class Tokens
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=64, unique=true)
     */
    private $token;

    /**
     * @var string
     *
     * @ORM\Column(name="createDate", type="string", length=20)
     */
    private $createDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="destroyDate", type="string", length=20)
     */
    private $destroyDate;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     *
     * @return Tokens
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set token
     *
     * @param string $token
     *
     * @return Tokens
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set createDate
     *
     * @param string $createDate
     *
     * @return Tokens
     */
    public function setCreateDate($createDate)
    {
        $this->createDate = $createDate;

        return $this;
    }

    /**
     * Get createDate
     *
     * @return string
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }

    /**
     * Set destroyDate
     *
     * @param string $destroyDate
     *
     * @return Tokens
     */
    public function setDestroyDate($destroyDate)
    {
        $this->destroyDate = $destroyDate;

        return $this;
    }

    /**
     * Get destroyDate
     *
     * @return string
     */
    public function getDestroyDate()
    {
        return $this->destroyDate;
    }
}

