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

use Contact\Entity\Contact;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Organisation\Entity\AbstractEntity;
use Organisation\Entity\ParentEntity;
use Laminas\Form\Annotation;

/**
 * @ORM\Table(name="organisation_parent_financial")
 * @ORM\Entity
 * @Annotation\Hydrator("Laminas\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("organisation_parent_financial")
 *
 * @category Parent
 */
class Financial extends AbstractEntity
{
    /**
     * @ORM\Column(name="parent_financial_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @var int
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="Organisation\Entity\ParentEntity", inversedBy="financial", cascade="persist")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="parent_id", nullable=false)
     *
     * @var ParentEntity
     */
    private $parent;
    /**
     * @ORM\ManyToOne(targetEntity="Contact\Entity\Contact", cascade="persist", inversedBy="parentFinancial")
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id", nullable=false)
     *
     * @var Contact
     */
    private $contact;
    /**
     * @ORM\ManyToOne(targetEntity="Organisation\Entity\Organisation", cascade={"persist"}, inversedBy="parentFinancial")
     * @ORM\JoinColumn(name="organisation_id", referencedColumnName="organisation_id", nullable=false)
     *
     * @var \Organisation\Entity\Organisation
     */
    private $organisation;
    /**
     * @ORM\Column(name="branch", type="string", nullable=true)
     * @Annotation\Type("\Laminas\Form\Element\Text")
     * @Annotation\Options({"label":"txt-branch"})
     *
     * @var string
     */
    private $branch;
    /**
     * @ORM\Column(name="note", type="text", nullable=true)
     * @Annotation\Type("\Laminas\Form\Element\Text")
     * @Annotation\Options({"label":"txt-note"})
     *
     * @var string
     */
    private $note;
    /**
     * @ORM\Column(name="date_created", type="datetime", nullable=false)
     * @Gedmo\Timestampable(on="create")
     * @Annotation\Exclude()
     *
     * @var DateTime
     */
    private $dateCreated;
    /**
     * @ORM\Column(name="date_updated", type="datetime", nullable=false)
     * @Gedmo\Timestampable(on="create")
     * @Annotation\Exclude()
     *
     * @var DateTime
     */
    private $dateUpdated;

    public function __toString(): string
    {
        return (string)$this->getOrganisation()->getOrganisation();
    }

    public function getOrganisation(): ?\Organisation\Entity\Organisation
    {
        return $this->organisation;
    }

    public function setOrganisation(?\Organisation\Entity\Organisation $organisation): Financial
    {
        $this->organisation = $organisation;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Financial
    {
        $this->id = $id;
        return $this;
    }

    public function getParent(): ?ParentEntity
    {
        return $this->parent;
    }

    public function setParent(?ParentEntity $parent): Financial
    {
        $this->parent = $parent;
        return $this;
    }

    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    public function setContact(?Contact $contact): Financial
    {
        $this->contact = $contact;
        return $this;
    }

    public function getBranch(): ?string
    {
        return $this->branch;
    }

    public function setBranch(?string $branch): Financial
    {
        $this->branch = $branch;
        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): Financial
    {
        $this->note = $note;
        return $this;
    }

    public function getDateCreated(): ?DateTime
    {
        return $this->dateCreated;
    }

    public function setDateCreated(?DateTime $dateCreated): Financial
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    public function getDateUpdated(): ?DateTime
    {
        return $this->dateUpdated;
    }

    public function setDateUpdated(?DateTime $dateUpdated): Financial
    {
        $this->dateUpdated = $dateUpdated;
        return $this;
    }
}
