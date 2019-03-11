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
 * @link        https://github.com/iteaoffice/organisation for the canonical source repository
 */

declare(strict_types=1);

namespace Organisation\Entity\Parent;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Organisation\Entity\AbstractEntity;
use Organisation\Entity\OParent;
use Zend\Form\Annotation;

/**
 * Entity for the Parent Organisation.
 *
 * @ORM\Table(name="organisation_parent_organisation")
 * @ORM\Entity(repositoryClass="Organisation\Repository\Parent\Organisation")
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("organisation_parent_organisation")
 */
class Organisation extends AbstractEntity
{
    /**
     * @ORM\Column(name="parent_organisation_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Type("Zend\Form\Element\Hidden")
     * @var int
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="Organisation\Entity\OParent", inversedBy="parentOrganisation", cascade="persist")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="parent_id", nullable=false)
     *
     * @Annotation\Type("Organisation\Form\Element\OParent")
     * @Annotation\Attributes({"label":"txt-parent-organisation-parent-label"})
     * @Annotation\Options({"help-block":"txt-parent-organisation-parent-help-block"})
     *
     * @var \Organisation\Entity\OParent
     */
    private $parent;
    /**
     * @ORM\ManyToOne(targetEntity="Contact\Entity\Contact", cascade="persist", inversedBy="parentOrganisation")
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id", nullable=false)
     * @Annotation\Type("Contact\Form\Element\Contact")
     * @Annotation\Attributes({"label":"txt-parent-organisation-representative-label"})
     * @Annotation\Options({"help-block":"txt-parent-organisation-representative-help-block"})
     *
     * @var \Contact\Entity\Contact
     */
    private $contact;
    /**
     * @ORM\OneToOne(targetEntity="Organisation\Entity\Organisation", cascade={"persist"}, inversedBy="parentOrganisation")
     * @ORM\JoinColumn(name="organisation_id", referencedColumnName="organisation_id", nullable=false)
     * @Annotation\Type("Organisation\Form\Element\Organisation")
     * @Annotation\Attributes({"label":"txt-parent-organisation-organisation-label"})
     * @Annotation\Options({"help-block":"txt-parent-organisation-organisation-help-block"})
     *
     * @var \Organisation\Entity\Organisation
     */
    private $organisation;
    /**
     * @ORM\OneToMany(targetEntity="Affiliation\Entity\Affiliation", cascade={"persist"}, mappedBy="parentOrganisation")
     * @Annotation\Exclude()
     *
     * @var \Affiliation\Entity\Affiliation[]|ArrayCollection
     */
    private $affiliation;

    public function __construct()
    {
        $this->affiliation = new ArrayCollection();
    }

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }

    public function __isset($property)
    {
        return isset($this->$property);
    }

    public function __toString(): string
    {
        return (string)$this->getOrganisation()->getOrganisation();
    }

    public function getOrganisation()
    {
        return $this->organisation;
    }

    public function setOrganisation($organisation)
    {
        $this->organisation = $organisation;

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function setParent(OParent $parent): Organisation
    {
        $this->parent = $parent;

        return $this;
    }

    public function getContact()
    {
        return $this->contact;
    }

    public function setContact($contact)
    {
        $this->contact = $contact;

        return $this;
    }

    public function getAffiliation()
    {
        return $this->affiliation;
    }

    public function setAffiliation($affiliation)
    {
        $this->affiliation = $affiliation;

        return $this;
    }
}
