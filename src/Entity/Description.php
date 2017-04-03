<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

namespace Organisation\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;

/**
 * Description.
 *
 * @ORM\Table(name="organisation_description")
 * @ORM\Entity
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("organisation_description")
 */
class Description extends AbstractEntity
{
    /**
     * @ORM\Column(name="description_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Exclude()
     *
     * @var integer
     */
    private $id;
    /**
     * @ORM\Column(name="description", length=65535, type="text", nullable=false)
     * @Annotation\Type("\Zend\Form\Element\Textarea")
     * @Annotation\Options({"label":"txt-description","help-block":"txt-organisation-description-help-block"})
     * @Annotation\Attributes({"rows":12})
     *
     * @var string
     */
    private $description;
    /**
     * @ORM\OneToOne(targetEntity="Organisation\Entity\Organisation", inversedBy="description")
     * @ORM\JoinColumn(name="organisation_id", referencedColumnName="organisation_id", nullable=false, unique=true)
     * @Annotation\Exclude()
     *
     * @var \Organisation\Entity\Organisation
     */
    private $organisation;

    /**
     * @param $property
     *
     * @return mixed
     */
    public function __get($property)
    {
        return $this->$property;
    }

    /**
     * @param string $property
     * @param mixed $value
     * @return void
     */
    public function __set($property, $value)
    {
        $this->$property = $value;
    }

    /**
     * @param $property
     *
     * @return bool
     */
    public function __isset($property)
    {
        return isset($this->$property);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->description;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return Description
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return Description
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Organisation
     */
    public function getOrganisation()
    {
        return $this->organisation;
    }

    /**
     * @param Organisation $organisation
     *
     * @return Description
     */
    public function setOrganisation($organisation)
    {
        $this->organisation = $organisation;

        return $this;
    }
}
