<?php

/**
*
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        https://github.com/iteaoffice/organisation for the canonical source repository
 */

declare(strict_types=1);

namespace Organisation\Entity\Parent;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Organisation\Entity\AbstractEntity;
use Laminas\Form\Annotation;

/**
 * Entity for the Partner.
 *
 * @ORM\Table(name="organisation_parent_type")
 * @ORM\Entity(repositoryClass="Organisation\Repository\Parent\Type")
 * @Annotation\Hydrator("Laminas\Hydrator\ObjectProperty")
 * @Annotation\Name("organisation_parent_type")
 */
class Type extends AbstractEntity
{
    public const TYPE_A_CHAMBER = 1;
    public const TYPE_B_CHAMBER = 2;
    public const TYPE_C_CHAMBER = 3;
    public const TYPE_OTHER = 4;
    /**
     * @ORM\Column(name="type_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Type("\Laminas\Form\Element\Hidden")
     *
     * @var int
     */
    private $id;
    /**
     * @ORM\Column(name="type", type="string", nullable=false)
     * @Annotation\Type("\Laminas\Form\Element\Text")
     * @Annotation\Options({"label":"txt-organisation-parent-type-label","help-block":"txt-organisation-parent-type-help-block"})
     * @var string
     */
    private $type;
    /**
     * @ORM\Column(name="description", type="string", nullable=false)
     * @Annotation\Type("\Laminas\Form\Element\Text")
     * @Annotation\Options({"label":"txt-organisation-parent-type-description-label","help-block":"txt-organisation-parent-type-description-help-block"})
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
