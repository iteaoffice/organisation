<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Organisation\Entity\AdvisoryBoard;

use Contact\Entity\Contact;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Laminas\Form\Annotation;
use Organisation\Entity\AbstractEntity;

/**
 * @ORM\Table(name="organisation_advisory_board_solution")
 * @ORM\Entity
 * @Annotation\Hydrator("Laminas\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("organisation_advisory_board_solution")
 */
class Solution extends AbstractEntity
{
    /**
     * @ORM\Column(name="solution_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Type("\Laminas\Form\Element\Hidden")
     */
    private ?int $id = null;
    /**
     * @ORM\Column(name="title")
     * @Annotation\Type("\Laminas\Form\Element\Text")
     * @Annotation\Options({"label":"txt-advisory-board-solution-title-label","help-block":"txt-advisory-board-solution-title-help-block"})
     * @Annotation\Attributes({"placeholder":"txt-advisory-board-solution-title-placeholder"})
     */
    private string $title = '';
    /**
     * @ORM\Column(name="docref", type="string", unique=true)
     * @Gedmo\Slug(fields={"title"})
     * @Annotation\Exclude()
     */
    private ?string $docRef = null;
    /**
     * @ORM\Column(name="description")
     * @Annotation\Type("\Laminas\Form\Element\Textarea")
     * @Annotation\Options({"label":"txt-advisory-board-solution-description-label","help-block":"txt-advisory-board-solution-description-help-block"})
     * @Annotation\Attributes({"placeholder":"txt-advisory-board-solution-description-placeholder"})
     */
    private string $description = '';
    /**
     * @ORM\Column(name="targeted_customers")
     * @Annotation\Type("\Laminas\Form\Element\Textarea")
     * @Annotation\Options({"label":"txt-advisory-board-solution-description-label","help-block":"txt-advisory-board-solution-description-help-block"})
     * @Annotation\Attributes({"placeholder":"txt-advisory-board-solution-description-placeholder"})
     */
    private string $targetedCustomers = '';
    /**
     * @ORM\Column(name="website",nullable=true)
     * @Annotation\Type("\Laminas\Form\Element\Url")
     * @Annotation\Options({"label":"txt-advisory-board-solution-website-label","help-block":"txt-advisory-board-solution-website-help-block"})
     * @Annotation\Attributes({"placeholder":"txt-advisory-board-solution-website-placeholder"})
     */
    private ?string $website = null;
    /**
     * @ORM\Column(name="date_created", type="datetime")
     * @Gedmo\Timestampable(on="create")
     * @Annotation\Exclude()
     */
    private ?DateTime $dateCreated = null;
    /**
     * @ORM\Column(name="date_updated", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     * @Annotation\Exclude()
     */
    private ?DateTime $dateUpdated = null;
    /**
     * @ORM\OneToOne (targetEntity="Organisation\Entity\AdvisoryBoard\Solution\Image", cascade={"persist","remove"}, mappedBy="solution")
     * @Annotation\Exclude()
     */
    private ?Solution\Image $image = null;
    /**
     * @ORM\ManyToOne(targetEntity="Contact\Entity\Contact", cascade={"persist"}, inversedBy="advisoryBoardSolutions")
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id", nullable=true)
     * @Annotation\Type("Contact\Form\Element\Contact")
     * @Annotation\Options({"label":"txt-advisory-board-solution-contact-label","help-block":"txt-advisory-board-solution-contact-help-block"})
     */
    private ?Contact $contact = null;

    public function hasImage(): bool
    {
        return null !== $this->image;
    }

    public function __toString(): string
    {
        return (string)$this->title;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Solution
    {
        $this->id = $id;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): Solution
    {
        $this->title = $title;
        return $this;
    }

    public function getDocRef(): ?string
    {
        return $this->docRef;
    }

    public function setDocRef(?string $docRef): Solution
    {
        $this->docRef = $docRef;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): Solution
    {
        $this->description = $description;
        return $this;
    }

    public function getTargetedCustomers(): ?string
    {
        return $this->targetedCustomers;
    }

    public function setTargetedCustomers(?string $targetedCustomers): Solution
    {
        $this->targetedCustomers = $targetedCustomers;
        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): Solution
    {
        $this->website = $website;
        return $this;
    }

    public function getDateCreated(): ?DateTime
    {
        return $this->dateCreated;
    }

    public function setDateCreated(?DateTime $dateCreated): Solution
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    public function getDateUpdated(): ?DateTime
    {
        return $this->dateUpdated;
    }

    public function setDateUpdated(?DateTime $dateUpdated): Solution
    {
        $this->dateUpdated = $dateUpdated;
        return $this;
    }

    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    public function setContact(?Contact $contact): Solution
    {
        $this->contact = $contact;
        return $this;
    }

    public function getImage(): ?Solution\Image
    {
        return $this->image;
    }

    public function setImage(?Solution\Image $image): Solution
    {
        $this->image = $image;
        return $this;
    }
}
