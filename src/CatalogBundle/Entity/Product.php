<?php

namespace CatalogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="products",indexes={
 *     @ORM\Index(name="name", columns={"name"}),
 *     @ORM\Index(name="creation_time", columns={"creation_time"})})
 * @ORM\Entity(repositoryClass="CatalogBundle\Repository\ProductRepository")
 */
class Product
{

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="products")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    private $category;
    
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creation_time", type="string")
     */
    private $creationTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_modification", type="string")
     */
    private $lastModification;

    /**
     * @var bool
     * @ORM\Column(name="state_flag", type="boolean")
     */
    private $stateFlag;

    /**
     * @var string
     *
     * @ORM\Column(name="sku", type="string", length=50, unique=true)
     */
    private $sku;

    /**
     * @var string
     * @ORM\Column(name="image", type="string", length=50, nullable=true)
     * @Assert\Image
     */
    private $image;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Product
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Product
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set creationTime
     *
     * @param string $creationTime
     *
     * @return Product
     */
    public function setCreationTime($creationTime)
    {
        $this->creationTime = $creationTime;

        return $this;
    }

    /**
     * Get creationTime
     *
     * @return string
     */
    public function getCreationTime()
    {
        return $this->creationTime;
    }

    /**
     * Set lastModification
     *
     * @param string $lastModification
     *
     * @return Product
     */
    public function setLastModification($lastModification)
    {
        $this->lastModification = $lastModification;

        return $this;
    }

    /**
     * Get lastModification
     *
     * @return string
     */
    public function getLastModification()
    {
        return $this->lastModification;
    }

    /**
     * Set stateFlag
     *
     * @param boolean $stateFlag
     *
     * @return Product
     */
    public function setStateFlag($stateFlag)
    {
        $this->stateFlag = $stateFlag;

        return $this;
    }

    /**
     * Get stateFlag
     *
     * @return boolean
     */
    public function getStateFlag()
    {
        return $this->stateFlag;
    }

    /**
     * Set sku
     *
     * @param string $sku
     *
     * @return Product
     */
    public function setSku($sku)
    {
        $this->sku = $sku;

        return $this;
    }

    /**
     * Get sku
     *
     * @return string
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return Product
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set category
     *
     * @param \CatalogBundle\Entity\Category $category
     *
     * @return Product
     */
    public function setCategory(\CatalogBundle\Entity\Category $category = null)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * Get category
     *
     * @return \CatalogBundle\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
    }


    public function getProductDataToForm()
    {
        return [
            'name' => $this->getName(),
            'sku' => $this->getSku(),
            'description' => $this->getDescription(),
            'state_flag' => $this->getStateFlag(),
            'category' => $this->getCategory()->getId(),
        ];
    }
}
