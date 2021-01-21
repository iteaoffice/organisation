<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Organisation\Entity\Parent;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Laminas\Form\Annotation;
use Organisation\Entity\AbstractEntity;

/**
 * @ORM\Table(name="organisation_parent_type")
 * @ORM\Entity(repositoryClass="Organisation\Repository\Parent\TypeRepository")
 * @Annotation\Hydrator("Laminas\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("organisation_parent_type")
 */
class Type extends AbstractEntity
{
    public const TYPE_A_CHAMBER = 1;
    public const TYPE_B_CHAMBER = 2;
    public const TYPE_C_CHAMBER = 3;
    public const TYPE_OTHER     = 4;
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
     * @ORM\OneToMany(targetEntity="Organisation\Entity\ParentEntity", cascade={"persist"}, mappedBy="type")
     * @Annotation\Exclude()
     *
     * @var \Organisation\Entity\ParentEntity[]|ArrayCollection
     */
    private $parent;

    public function __construct()
    {
        $this->parent = new ArrayCollection();
    }

    public function __toString(): string
    {
        return (string)$this->type;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Type
    {
        $this->id = $id;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): Type
    {
        $this->type = $type;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): Type
    {
        $this->description = $description;
        return $this;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function setParent($parent): Type
    {
        $this->parent = $parent;
        return $this;
    }
}
