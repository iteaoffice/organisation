<?php
/**
 * ITEA Office all rights reserved
 *
 * @category   Project
 *
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license    https://itea3.org/license.txt proprietary
 *
 * @link       https://itea3.org
 */

declare(strict_types=1);

namespace Organisation\Entity\Parent;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Organisation\Entity\AbstractEntity;
use Zend\Form\Annotation;

/**
 * @ORM\Table(name="organisation_parent_doa")
 * @ORM\Entity
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
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
     * @var \DateTime
     */
    private $dateApproved;
    /**
     * @ORM\Column(name="date_signed", type="date", nullable=true)
     *
     * @var \DateTime
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
     * @Annotation\Type("\Zend\Form\Element\File")
     * @Annotation\Options({"label":"txt-nda-file"})
     *
     * @var \General\Entity\ContentType
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
     * @var \DateTime
     */
    private $dateUpdated;
    /**
     * @ORM\Column(name="date_created", type="datetime", nullable=false)
     * @Gedmo\Timestampable(on="create")
     *
     * @var \DateTime
     */
    private $dateCreated;
    /**
     * @ORM\OneToMany(targetEntity="Organisation\Entity\Parent\DoaObject", cascade={"persist","remove"}, mappedBy="doa")
     * @Annotation\Exclude()
     *
     * @var \Organisation\Entity\Parent\DoaObject[]|ArrayCollection
     */
    private $object;
    /**
     * @ORM\ManyToOne(targetEntity="Contact\Entity\Contact", inversedBy="parentDoa")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id")
     * })
     *
     * @var \Contact\Entity\Contact
     */
    private $contact;
    /**
     * @ORM\ManyToOne(targetEntity="Organisation\Entity\OParent", inversedBy="doa")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="parent_id")
     * })
     *
     * @var \Organisation\Entity\OParent
     */
    private $parent;
    /**
     * @ORM\ManyToOne(targetEntity="Program\Entity\Program", inversedBy="parentDoa", cascade={"persist"})
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="program_id", referencedColumnName="program_id", nullable=false)
     * })
     *
     * @var \Program\Entity\Program
     */
    private $program;

    /**
     * Class constructor.
     */
    public function __construct()
    {
        $this->object = new ArrayCollection();
    }

    /**
     * Magic Getter.
     *
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
     * @return string
     */
    public function __toString(): string
    {
        return sprintf('%s DOA', $this->program);
    }


    /**
     * Parse a filename.
     *
     * @return string
     */
    public function parseFileName(): string
    {
        return str_replace(' ', '_', sprintf("DOA_%s_%s", $this->getParent(), $this->getProgram()));
    }

    /**
     * @return \Organisation\Entity\OParent
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param \Organisation\Entity\OParent $parent
     *
     * @return Doa
     */
    public function setParent(\Organisation\Entity\OParent $parent): Doa
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return \Program\Entity\Program
     */
    public function getProgram()
    {
        return $this->program;
    }

    /**
     * @param \Program\Entity\Program $program
     *
     * @return Doa
     */
    public function setProgram(\Program\Entity\Program $program): Doa
    {
        $this->program = $program;

        return $this;
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
     * @return Doa
     */
    public function setId(int $id): Doa
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateApproved()
    {
        return $this->dateApproved;
    }

    /**
     * @param \DateTime $dateApproved
     *
     * @return Doa
     */
    public function setDateApproved(\DateTime $dateApproved): Doa
    {
        $this->dateApproved = $dateApproved;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateSigned()
    {
        return $this->dateSigned;
    }

    /**
     * @param \DateTime $dateSigned
     *
     * @return Doa
     */
    public function setDateSigned(\DateTime $dateSigned): Doa
    {
        $this->dateSigned = $dateSigned;

        return $this;
    }

    /**
     * @return string
     */
    public function getBranch()
    {
        return $this->branch;
    }

    /**
     * @param string $branch
     *
     * @return Doa
     */
    public function setBranch(string $branch): Doa
    {
        $this->branch = $branch;

        return $this;
    }

    /**
     * @return \General\Entity\ContentType
     */
    public function getContentType(): \General\Entity\ContentType
    {
        return $this->contentType;
    }

    /**
     * @param \General\Entity\ContentType $contentType
     *
     * @return Doa
     */
    public function setContentType(\General\Entity\ContentType $contentType): Doa
    {
        $this->contentType = $contentType;

        return $this;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param int $size
     *
     * @return Doa
     */
    public function setSize(int $size): Doa
    {
        $this->size = $size;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateUpdated()
    {
        return $this->dateUpdated;
    }

    /**
     * @param \DateTime $dateUpdated
     *
     * @return Doa
     */
    public function setDateUpdated(\DateTime $dateUpdated): Doa
    {
        $this->dateUpdated = $dateUpdated;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateCreated(): \DateTime
    {
        return $this->dateCreated;
    }

    /**
     * @param \DateTime $dateCreated
     *
     * @return Doa
     */
    public function setDateCreated(\DateTime $dateCreated): Doa
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * @return ArrayCollection|\Organisation\Entity\Parent\DoaObject[]
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @param ArrayCollection|\Organisation\Entity\Parent\DoaObject[] $object
     *
     * @return Doa
     */
    public function setObject($object)
    {
        $this->object = $object;

        return $this;
    }

    /**
     * @return \Contact\Entity\Contact
     */
    public function getContact(): \Contact\Entity\Contact
    {
        return $this->contact;
    }

    /**
     * @param \Contact\Entity\Contact $contact
     *
     * @return Doa
     */
    public function setContact(\Contact\Entity\Contact $contact): Doa
    {
        $this->contact = $contact;

        return $this;
    }
}
