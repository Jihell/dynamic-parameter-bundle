<?php
/**
 * @package dynamic-parameter-bundle
 */
namespace Jihel\Plugin\DynamicParameterBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Parameter
 *
 * @author Joseph LEMOINE <lemoine.joseph@gmail.com>
 * @link http://www.joseph-lemoine.fr
 *
 * @ORM\Table(name="parameter")
 * @ORM\Entity(repositoryClass="Jihel\Plugin\DynamicParameterBundle\Repository\ParameterRepository")
 * @ORM\EntityListeners({"Jihel\Plugin\DynamicParameterBundle\Listener\ParameterListener"})
 */
class Parameter
{
    public function __construct()
    {
        $this->setIsEditable(true);
    }

    public function __toString()
    {
        return $this->getName();
    }

    /**
     * =========================================================================
     *                              PROPERTIES
     * =========================================================================
     */

    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @Assert\NotBlank
     * @ORM\Column(name="name", type="string")
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="namespace", type="string", nullable=true)
     */
    protected $namespace;

    /**
     * @var string
     *
     * @Assert\NotBlank
     * @ORM\Column(name="value", type="string")
     */
    protected $value;

    /**
     * @var string
     *
     * @Assert\NotBlank
     * @Assert\Choice(choices={"int","string","float","bool","json"})
     * @ORM\Column(name="type", type="string")
     */
    protected $type;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isEditable", type="boolean")
     */
    protected $isEditable;


    /**
     * =========================================================================
     *                              ACCESSORS
     * =========================================================================
     */

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = strtolower($name);
        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param boolean $isEditable
     * @return $this
     */
    public function setIsEditable($isEditable)
    {
        $this->isEditable = $isEditable;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getIsEditable()
    {
        return $this->isEditable;
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @param string $namespace
     * @return $this
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
        return $this;
    }
}
