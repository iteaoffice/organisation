<?php

/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Project
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

declare(strict_types=1);

namespace Organisation\Entity\Parent;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Organisation\Entity\AbstractEntity;
use Zend\Form\Annotation;

/**
 * Entity for the Partner.
 *
 * @ORM\Table(name="organisation_parent_type")
 * @ORM\Entity(repositoryClass="Organisation\Repository\Parent\Type")
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("organisation_parent_type")
 */
class Type extends AbstractEntity
{
    const TYPE_A_CHAMBER = 1;
    const TYPE_B_CHAMBER = 2;
    const TYPE_C_CHAMBER = 3;
    const TYPE_OTHER = 4;
    /**
     * @ORM\Column(name="type_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Type("\Zend\Form\Element\Hidden")
     *
     * @var int
     */
    private $id;
    /**
     * @ORM\Column(name="type", type="string", nullable=false)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-organisation-parent-type-label","help-block":"txt-organisation-parent-type-help-block"})
     * @var string
     */
    private $type;
    /**
     * @ORM\Column(name="description", type="string", nullable=false)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-organisation-parent-description-label","help-block":"txt-organisation-parent-description-help-block"})
     * @var string
     */
    private $description;
    /**
     * @ORM\OneToMany(targetEntity="Organisation\Entity\OParent", cascade={"persist"}, mappedBy="type")
     * @Annotation\Exclude()
     *
     * @var \Organisation\Entity\OParent[]|ArrayCollection
     */
    private $parent;

    /**
     * Type constructor.
     */
    public function __construct()
    {
        $this->parent = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->type;
    }

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
     * @param $property
     * @param $value
     *
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
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return Type
     */
    public function setId($id): Type
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return Type
     */
    public function setType(string $type): Type
    {
        $this->type = $type;

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
     * @return Type
     */
    public function setDescription(string $description): Type
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return ArrayCollection|\Organisation\Entity\OParent[]
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param ArrayCollection|\Organisation\Entity\OParent[] $parent
     *
     * @return Type
     */
    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }
}
