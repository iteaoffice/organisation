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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Laminas\Form\Annotation;
use Organisation\Entity\AbstractEntity;
use Organisation\Entity\ParentEntity;

/**
 * @ORM\Table(name="organisation_parent_organisation")
 * @ORM\Entity(repositoryClass="Organisation\Repository\Parent\OrganisationRepository")
 * @Annotation\Hydrator("Laminas\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("organisation_parent_organisation")
 */
class Organisation extends AbstractEntity
{
    /**
     * @ORM\Column(name="parent_organisation_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Type("Laminas\Form\Element\Hidden")
     * @var int
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="Organisation\Entity\ParentEntity", inversedBy="parentOrganisation", cascade={"persist"})
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="parent_id", nullable=false)
     *
     * @Annotation\Type("Organisation\Form\Element\ParentElement")
     * @Annotation\Attributes({"label":"txt-parent-organisation-parent-label"})
     * @Annotation\Options({"help-block":"txt-parent-organisation-parent-help-block"})
     *
     * @var \Organisation\Entity\ParentEntity
     */
    private $parent;
    /**
     * @ORM\ManyToOne(targetEntity="Contact\Entity\Contact", cascade={"persist"}, inversedBy="parentOrganisation")
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
     * @Annotation\Type("Organisation\Form\Element\OrganisationElement")
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

    public function __toString(): string
    {
        if (null === $this->organisation) {
            return '';
        }
        return (string)$this->organisation->getOrganisation();
    }

    public function getOrganisation(): \Organisation\Entity\Organisation
    {
        return $this->organisation;
    }

    public function setOrganisation($organisation): Organisation
    {
        $this->organisation = $organisation;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Organisation
    {
        $this->id = $id;

        return $this;
    }

    public function getParent(): ParentEntity
    {
        return $this->parent;
    }

    public function setParent(ParentEntity $parent): Organisation
    {
        $this->parent = $parent;

        return $this;
    }

    public function getContact(): \Contact\Entity\Contact
    {
        return $this->contact;
    }

    public function setContact($contact): Organisation
    {
        $this->contact = $contact;

        return $this;
    }

    public function getAffiliation()
    {
        return $this->affiliation;
    }

    public function setAffiliation($affiliation): Organisation
    {
        $this->affiliation = $affiliation;

        return $this;
    }
}
