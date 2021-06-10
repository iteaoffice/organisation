<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Organisation\Entity\AdvisoryBoard\Tender;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Laminas\Form\Annotation;
use Organisation\Entity\AbstractEntity;

/**
 * @ORM\Table(name="organisation_advisory_board_tender_type")
 * @ORM\Entity(repositoryClass="Organisation\Repository\AdvisoryBoard\Tender\TypeRepository")
 * @Annotation\Hydrator("Laminas\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("organisation_advisory_board_tender_type")
 */
class Type extends AbstractEntity
{
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
     * @Annotation\Options({"label":"txt-advisory-board-tender-type-label","help-block":"txt-advisory-board-tender-type-help-block"})
     * @var string
     */
    private $type;
    /**
     * @ORM\Column(name="description", type="string", nullable=false)
     * @Annotation\Type("\Laminas\Form\Element\Text")
     * @Annotation\Options({"label":"txt-advisory-board-tender-type-description-label","help-block":"txt-advisory-board-tender-type-description-help-block"})
     * @var string
     */
    private $description;
    /**
     * @ORM\OneToMany(targetEntity="Organisation\Entity\AdvisoryBoard\Tender", cascade={"persist"}, mappedBy="type")
     * @Annotation\Exclude()
     *
     * @var \Organisation\Entity\AdvisoryBoard\Tender[]|ArrayCollection
     */
    private $tenders;

    public function __construct()
    {
        $this->tenders = new ArrayCollection();
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

    public function getTenders()
    {
        return $this->tenders;
    }

    public function setTenders($tenders): Type
    {
        $this->tenders = $tenders;
        return $this;
    }
}
