<?php

/**
 * ITEA Office all rights reserved
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Organisation\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Laminas\Form\Annotation;

/**
 * OrganisationLog.
 *
 * @ORM\Table(name="organisation_note")
 * @ORM\Entity
 * @Annotation\Hydrator("Laminas\Hydrator\ObjectProperty")
 * @Annotation\Name("organisation_note")
 */
class Note extends AbstractEntity
{
    /**
     * @ORM\Column(name="note_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Exclude()
     *
     * @var int
     */
    private $id;
    /**
     * @ORM\Column(name="note", length=65535, type="text", nullable=false)
     * @Annotation\Type("\Laminas\Form\Element\Textarea")
     * @Annotation\Options({"label":"txt-note","help-block":"txt-note-help-block"})
     *  @Annotation\Attributes({"placeholder":"txt-note-note-placeholder","rows":10})
     *
     * @var string
     */
    private $note;
    /**
     * @ORM\Column(name="source", type="string", nullable=false)
     * @Annotation\Type("\Laminas\Form\Element\Text")
     * @Annotation\Options({"label":"txt-note-source","help-block":"txt-note-source-help-block"})
     * @Annotation\Attributes({"placeholder":"txt-note-source-placeholder"})
     *
     * @var string
     */
    private $source;
    /**
     * @ORM\Column(name="date_created", type="datetime", nullable=false)
     * @Gedmo\Timestampable(on="create")
     * @Annotation\Exclude()
     *
     * @var \DateTime
     */
    private $dateCreated;
    /**
     * @ORM\ManyToOne(targetEntity="Contact\Entity\Contact", inversedBy="organisationLog", cascade={"persist"})
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id", nullable=false)
     * @Annotation\Exclude()
     *
     * @var \Contact\Entity\Contact
     */
    private $contact;
    /**
     * @ORM\ManyToOne(targetEntity="Organisation\Entity\Organisation", inversedBy="note", cascade="persist")
     * @ORM\JoinColumn(name="organisation_id", referencedColumnName="organisation_id", nullable=false)
     * @Annotation\Exclude()
     *
     * @var \Organisation\Entity\Organisation
     */
    private $organisation;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote($note): Note
    {
        $this->note = $note;

        return $this;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource($source): Note
    {
        $this->source = $source;

        return $this;
    }

    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    public function setDateCreated($dateCreated): Note
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    public function getContact()
    {
        return $this->contact;
    }

    public function setContact($contact): Note
    {
        $this->contact = $contact;

        return $this;
    }

    public function getOrganisation(): ?Organisation
    {
        return $this->organisation;
    }

    public function setOrganisation($organisation): Note
    {
        $this->organisation = $organisation;

        return $this;
    }
}
