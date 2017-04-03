<?php

/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Project
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

namespace Organisation\Entity\Parent;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Organisation\Entity\AbstractEntity;
use Zend\Form\Annotation;

/**
 * Entity for the Parent.
 *
 * @ORM\Table(name="organisation_parent_financial")
 * @ORM\Entity
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("organisation_parent_financial")
 *
 * @category Parent
 */
class Financial extends AbstractEntity
{
    /**
     * @ORM\Column(name="parent_financial_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @var int
     */
    private $id;
    /**
     * @ORM\OneToOne(targetEntity="Organisation\Entity\OParent", inversedBy="financial", cascade="persist")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="parent_id", nullable=false)
     * })
     *
     * @var \Organisation\Entity\OParent
     */
    private $parent;
    /**
     * @ORM\ManyToOne(targetEntity="Contact\Entity\Contact", cascade="persist", inversedBy="parentFinancial")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id", nullable=false)
     * })
     *
     * @var \Contact\Entity\Contact
     */
    private $contact;
    /**
     * @ORM\ManyToOne(targetEntity="Organisation\Entity\Organisation", cascade={"persist"}, inversedBy="parentFinancial")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="organisation_id", referencedColumnName="organisation_id", nullable=false)
     * })
     *
     * @var \Organisation\Entity\Organisation
     */
    private $organisation;
    /**
     * @ORM\Column(name="branch", type="string", nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-branch"})
     *
     * @var string
     */
    private $branch;
    /**
     * @ORM\Column(name="note", type="text", nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Text")
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
     * @var \DateTime
     */
    private $dateCreated;
    /**
     * @ORM\Column(name="date_updated", type="datetime", nullable=false)
     * @Gedmo\Timestampable(on="create")
     * @Annotation\Exclude()
     *
     * @var \DateTime
     */
    private $dateUpdated;

    /**
     * Class constructor.
     */
    public function __construct()
    {
    }

    /**
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
     * @return void;
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
        return (string)$this->getOrganisation()->getOrganisation();
    }

    /**
     * @return \Organisation\Entity\Organisation
     */
    public function getOrganisation()
    {
        return $this->organisation;
    }

    /**
     * @param \Organisation\Entity\Organisation $organisation
     *
     * @return Financial
     */
    public function setOrganisation(\Organisation\Entity\Organisation $organisation): Financial
    {
        $this->organisation = $organisation;

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
     * @return Financial
     */
    public function setId($id): Financial
    {
        $this->id = $id;

        return $this;
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
     * @return Financial
     */
    public function setParent($parent): Financial
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return \Contact\Entity\Contact
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @param \Contact\Entity\Contact $contact
     *
     * @return Financial
     */
    public function setContact(\Contact\Entity\Contact $contact): Financial
    {
        $this->contact = $contact;

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
     * @return Financial
     */
    public function setBranch(string $branch): Financial
    {
        $this->branch = $branch;

        return $this;
    }

    /**
     * @return string
     */
    public function getNote(): string
    {
        return $this->note;
    }

    /**
     * @param string $note
     *
     * @return Financial
     */
    public function setNote(string $note): Financial
    {
        $this->note = $note;

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
     * @return Financial
     */
    public function setDateCreated(\DateTime $dateCreated): Financial
    {
        $this->dateCreated = $dateCreated;

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
     * @return Financial
     */
    public function setDateUpdated(\DateTime $dateUpdated): Financial
    {
        $this->dateUpdated = $dateUpdated;

        return $this;
    }
}
