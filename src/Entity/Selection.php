<?php

/**
 * ITEA Office all rights reserved
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Organisation\Entity;

use Contact\Entity\Contact;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Laminas\Form\Annotation;

/**
 * @ORM\Table(name="organisation_selection")
 * @ORM\Entity(repositoryClass="Organisation\Repository\Selection")
 * @Annotation\Hydrator("Laminas\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("organisation_selection")
 */
class Selection extends AbstractEntity
{
    /**
     * @ORM\Column(name="selection_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Type("\Laminas\Form\Element\Hidden")
     *
     * @var int
     */
    private $id;
    /**
     * @ORM\Column(name="selection", type="string", nullable=false, unique=true)
     * @Annotation\Type("\Laminas\Form\Element\Text")
     * @Annotation\Options({"label":"txt-organisation-selection-selection-label","help-block":"txt-organisation-selection-selection-help-block"})
     * @Annotation\Attributes({"placeholder":"txt-organisation-selection-selection-placeholder"})
     *
     * @var string
     */
    private $selection;
    /**
     * @ORM\Column(name="description", type="text", nullable=true)
     * @Annotation\Type("\Laminas\Form\Element\Textarea")
     * @Annotation\Options({"label":"txt-organisation-selection-description-label","help-block":"txt-organisation-selection-description-help-block"})
     * @Annotation\Attributes({"placeholder":"txt-organisation-selection-description-placeholder"})
     *
     * @var string
     */
    private $description;
    /**
     * @ORM\Column(name="tag", type="string", nullable=true)
     * @Annotation\Type("\Laminas\Form\Element\Text")
     * @Annotation\Options({"label":"txt-organisation-selection-tag-label","help-block":"txt-organisation-selection-tag-help-block"})
     * @Annotation\Attributes({"placeholder":"txt-organisation-selection-tag-placeholder"})
     *
     * @var string
     */
    private $tag;
    /**
     * @ORM\Column(name="date_created", type="datetime", nullable=false)
     * @Gedmo\Timestampable(on="create")
     * @Annotation\Exclude()
     *
     * @var DateTime
     */
    private $dateCreated;
    /**
     * @ORM\Column(name="date_updated", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     * @Annotation\Exclude()
     *
     * @var DateTime
     */
    private $dateUpdated;
    /**
     * @ORM\ManyToOne(targetEntity="Contact\Entity\Contact", cascade={"persist"}, inversedBy="organisationSelection")
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id", nullable=true)
     * @Annotation\Type("Contact\Form\Element\Contact")
     * @Annotation\Options({"label":"txt-organisation-selection-owner-label","help-block":"txt-organisation-selection-owner-help-block"})
     *
     * @var Contact
     */
    private $contact;
    /**
     * @ORM\Column(name="sql_query", type="text", nullable=true)
     * @Annotation\Type("\Laminas\Form\Element\Textarea")
     * @Annotation\Options({"label":"txt-organisation-selection-sql-label","help-block":"txt-organisation-selection-sql-help-block"})
     * @Annotation\Attributes({"id":"selection_sql"})
     *
     * @var string
     */
    private $sql;

    public function __clone()
    {
        $this->id        = null;
        $this->selection = sprintf('%s (copy)', $this->selection);
    }

    public function hasSql(): bool
    {
        return null !== $this->sql;
    }

    public function __toString(): string
    {
        return (string)$this->selection;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Selection
    {
        $this->id = $id;
        return $this;
    }

    public function getSelection(): ?string
    {
        return $this->selection;
    }

    public function setSelection(?string $selection): Selection
    {
        $this->selection = $selection;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): Selection
    {
        $this->description = $description;
        return $this;
    }

    public function getTag(): ?string
    {
        return $this->tag;
    }

    public function setTag(?string $tag): Selection
    {
        $this->tag = $tag;
        return $this;
    }

    public function getDateCreated(): ?DateTime
    {
        return $this->dateCreated;
    }

    public function setDateCreated(?DateTime $dateCreated): Selection
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    public function getDateUpdated(): ?DateTime
    {
        return $this->dateUpdated;
    }

    public function setDateUpdated(?DateTime $dateUpdated): Selection
    {
        $this->dateUpdated = $dateUpdated;
        return $this;
    }

    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    public function setContact(?Contact $contact): Selection
    {
        $this->contact = $contact;
        return $this;
    }

    public function getSql(): ?string
    {
        return $this->sql;
    }

    public function setSql(string $sql): Selection
    {
        $this->sql = $sql;
        return $this;
    }
}
