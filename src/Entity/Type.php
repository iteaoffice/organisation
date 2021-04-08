<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Organisation\Entity;

use Affiliation\Entity\Questionnaire\Questionnaire;
use Doctrine\Common\Collections;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Event\Entity\Meeting\Cost;
use Laminas\Form\Annotation;

/**
 * @ORM\Table(name="organisation_type")
 * @ORM\Entity(repositoryClass="Organisation\Repository\TypeRepository")
 * @Annotation\Hydrator("Laminas\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("organisation_type")
 */
class Type extends AbstractEntity
{
    public const TYPE_IFC            = 1;
    public const TYPE_LARGE_INDUSTRY = 2;
    public const TYPE_SME            = 3;
    public const TYPE_RESEARCH       = 4;
    public const TYPE_UNIVERSITY     = 5;
    public const TYPE_GOVERNMENT     = 6;
    public const TYPE_OTHER          = 7;
    public const TYPE_UNKNOWN        = 8;

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
     * @ORM\Column(name="type", type="string", nullable=false, unique=true)
     * @Annotation\Type("\Laminas\Form\Element\Text")
     * @Annotation\Options({"label":"txt-type"})
     *
     * @var string
     */
    private $type;
    /**
     * @ORM\Column(name="description", type="string", nullable=false, unique=true)
     * @Annotation\Type("\Laminas\Form\Element\Text")
     * @Annotation\Options({"label":"txt-organistion-type-description-label","help-block":"txt-organistion-type-description-help-block"})
     *
     * @var string
     */
    private $description;
    /**
     * @ORM\Column(name="standard_type", type="string", nullable=false, unique=false)
     * @Annotation\Type("\Laminas\Form\Element\Text")
     * @Annotation\Options({"label":"txt-organistion-type-standard-type-label","help-block":"txt-organistion-type-standard-type-help-block"})
     * @Annotation\Attributes({"placeholder":"txt-organistion-type-standard-type-placeholder"})
     *
     * @var string
     */
    private $standardType;
    /**
     * @ORM\OneToMany(targetEntity="Event\Entity\Meeting\Cost", cascade={"persist"}, mappedBy="type")
     * @Annotation\Exclude()
     *
     * @var Cost[]|Collections\ArrayCollection()
     */
    private $meetingCost;
    /**
     * @ORM\OneToMany(targetEntity="Organisation\Entity\Organisation", cascade={"persist"}, mappedBy="type")
     * @Annotation\Exclude()
     *
     * @var Organisation[]|Collections\ArrayCollection
     */
    private $organisation;
    /**
     * @ORM\OneToMany(targetEntity="Organisation\Entity\Update", cascade={"persist"}, mappedBy="type")
     * @Annotation\Exclude()
     *
     * @var Update[]|Collections\Collection
     */
    private $organisationUpdates;
    /**
     * @ORM\OneToMany(targetEntity="Affiliation\Entity\Questionnaire\Questionnaire", cascade={"persist"}, mappedBy="organisationType")
     * @Annotation\Exclude()
     *
     * @var Questionnaire[]|Collection
     */
    private $questionnaires;

    public function __construct()
    {
        $this->organisation        = new Collections\ArrayCollection();
        $this->meetingCost         = new Collections\ArrayCollection();
        $this->organisationUpdates = new Collections\ArrayCollection();
        $this->questionnaires      = new Collections\ArrayCollection();
    }

    public function __toString(): string
    {
        return (string)$this->description;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): Type
    {
        $this->id = $id;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType($type): Type
    {
        $this->type = $type;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription($description): Type
    {
        $this->description = $description;

        return $this;
    }

    public function getStandardType(): ?string
    {
        return $this->standardType;
    }

    public function setStandardType(string $standardType): Type
    {
        $this->standardType = $standardType;
        return $this;
    }

    public function getMeetingCost()
    {
        return $this->meetingCost;
    }

    public function setMeetingCost($meetingCost): Type
    {
        $this->meetingCost = $meetingCost;

        return $this;
    }

    public function getOrganisation()
    {
        return $this->organisation;
    }

    public function setOrganisation($organisation): Type
    {
        $this->organisation = $organisation;

        return $this;
    }

    public function getOrganisationUpdates(): Collections\Collection
    {
        return $this->organisationUpdates;
    }

    public function setOrganisationUpdates(Collections\Collection $organisationUpdates): Type
    {
        $this->organisationUpdates = $organisationUpdates;
        return $this;
    }

    public function getQuestionnaires(): Collection
    {
        return $this->questionnaires;
    }

    public function setQuestionnaires(Collection $questionnaires): Type
    {
        $this->questionnaires = $questionnaires;
        return $this;
    }
}
