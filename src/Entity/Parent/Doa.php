<?php

/**
 * ITEA Office all rights reserved
 *
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license    https://itea3.org/license.txt proprietary
 *
 * @link       https://itea3.org
 */

declare(strict_types=1);

namespace Organisation\Entity\Parent;

use Contact\Entity\Contact;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use General\Entity\ContentType;
use Organisation\Entity\AbstractEntity;
use Organisation\Entity\OParent;
use Program\Entity\Program;
use Laminas\Form\Annotation;

/**
 * @ORM\Table(name="organisation_parent_doa")
 * @ORM\Entity
 * @Annotation\Hydrator("Laminas\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("organisation_parent_doa")
 *
 * @category    Program
 */
class Doa extends AbstractEntity
{
    /**
     * @ORM\Column(name="doa_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @var int
     */
    private $id;
    /**
     * @ORM\Column(name="date_approved", type="datetime", nullable=true)
     *
     * @var DateTime
     */
    private $dateApproved;
    /**
     * @ORM\Column(name="date_signed", type="date", nullable=true)
     *
     * @var DateTime
     */
    private $dateSigned;
    /**
     * @ORM\Column(name="branch", type="string", nullable=true)
     *
     * @var string
     */
    private $branch;
    /**
     * @ORM\ManyToOne(targetEntity="General\Entity\ContentType", cascade={"persist"}, inversedBy="parentDoa")
     * @ORM\JoinColumn(name="contenttype_id", referencedColumnName="contenttype_id", nullable=true)
     * @Annotation\Type("\Laminas\Form\Element\File")
     * @Annotation\Options({"label":"txt-nda-file"})
     *
     * @var ContentType
     */
    private $contentType;
    /**
     * @ORM\Column(name="size", type="integer", nullable=true)
     *
     * @var int
     */
    private $size;
    /**
     * @ORM\Column(name="date_updated", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     *
     * @var DateTime
     */
    private $dateUpdated;
    /**
     * @ORM\Column(name="date_created", type="datetime", nullable=false)
     * @Gedmo\Timestampable(on="create")
     *
     * @var DateTime
     */
    private $dateCreated;
    /**
     * @ORM\OneToMany(targetEntity="Organisation\Entity\Parent\DoaObject", cascade={"persist","remove"}, mappedBy="doa")
     * @Annotation\Exclude()
     *
     * @var DoaObject[]|ArrayCollection
     */
    private $object;
    /**
     * @ORM\ManyToOne(targetEntity="Contact\Entity\Contact", inversedBy="parentDoa")
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id")
     *
     * @var Contact
     */
    private $contact;
    /**
     * @ORM\ManyToOne(targetEntity="Organisation\Entity\OParent", inversedBy="doa")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="parent_id")
     *
     * @var OParent
     */
    private $parent;
    /**
     * @ORM\ManyToOne(targetEntity="Program\Entity\Program", inversedBy="parentDoa", cascade={"persist"})
     * @ORM\JoinColumn(name="program_id", referencedColumnName="program_id", nullable=false)
     *
     * @var Program
     */
    private $program;

    public function __construct()
    {
        $this->object = new ArrayCollection();
    }

    public function __toString(): string
    {
        return sprintf('%s DOA', $this->program);
    }

    public function parseFileName(): string
    {
        return str_replace(' ', '_', sprintf('DOA_%s_%s', $this->getParent(), $this->getProgram()));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Doa
    {
        $this->id = $id;
        return $this;
    }

    public function getDateApproved(): ?DateTime
    {
        return $this->dateApproved;
    }

    public function setDateApproved(?DateTime $dateApproved): Doa
    {
        $this->dateApproved = $dateApproved;
        return $this;
    }

    public function getDateSigned(): ?DateTime
    {
        return $this->dateSigned;
    }

    public function setDateSigned(?DateTime $dateSigned): Doa
    {
        $this->dateSigned = $dateSigned;
        return $this;
    }

    public function getBranch(): ?string
    {
        return $this->branch;
    }

    public function setBranch(?string $branch): Doa
    {
        $this->branch = $branch;
        return $this;
    }

    public function getContentType(): ?ContentType
    {
        return $this->contentType;
    }

    public function setContentType(?ContentType $contentType): Doa
    {
        $this->contentType = $contentType;
        return $this;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(?int $size): Doa
    {
        $this->size = $size;
        return $this;
    }

    public function getDateUpdated(): ?DateTime
    {
        return $this->dateUpdated;
    }

    public function setDateUpdated(?DateTime $dateUpdated): Doa
    {
        $this->dateUpdated = $dateUpdated;
        return $this;
    }

    public function getDateCreated(): ?DateTime
    {
        return $this->dateCreated;
    }

    public function setDateCreated(?DateTime $dateCreated): Doa
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    public function getObject()
    {
        return $this->object;
    }

    public function setObject($object): Doa
    {
        $this->object = $object;
        return $this;
    }

    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    public function setContact(?Contact $contact): Doa
    {
        $this->contact = $contact;
        return $this;
    }

    public function getParent(): ?OParent
    {
        return $this->parent;
    }

    public function setParent(?OParent $parent): Doa
    {
        $this->parent = $parent;
        return $this;
    }

    public function getProgram(): ?Program
    {
        return $this->program;
    }

    public function setProgram(?Program $program): Doa
    {
        $this->program = $program;
        return $this;
    }
}
