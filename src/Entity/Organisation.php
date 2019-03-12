<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Organisation\Entity;

use Doctrine\Common\Collections;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Zend\Form\Annotation;

/**
 * Organisation.
 *
 * @ORM\Table(name="organisation")
 * @ORM\Entity(repositoryClass="Organisation\Repository\Organisation")
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("organisation")
 * @Annotation\Instance("Organisation\Entity\Organisation")
 */
class Organisation extends AbstractEntity
{
    /**
     * @ORM\Column(name="organisation_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Exclude()
     *
     * @var integer
     */
    private $id;
    /**
     * @ORM\Column(name="organisation", type="string", nullable=false)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-organisation-name","help-block":"txt-organisation-name-help-block"})
     * @Annotation\Attributes({"placeholder":"txt-organisation-placeholder"})
     *
     * @var string
     */
    private $organisation;
    /**
     * @ORM\OneToMany(targetEntity="Contact\Entity\ContactOrganisation", cascade={"persist"}, mappedBy="organisation")
     * @Annotation\Exclude()
     *
     * @var \Contact\Entity\ContactOrganisation[]|Collections\ArrayCollection
     */
    private $contactOrganisation;
    /**
     * @ORM\Column(name="date_created", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="create")
     * @Annotation\Exclude()
     *
     * @var \DateTime
     */
    private $dateCreated;
    /**
     * @ORM\Column(name="docref", type="string", nullable=true, unique=true)
     * @Gedmo\Slug(fields={"id","organisation"})
     * @Annotation\Exclude()
     *
     * @var string
     */
    private $docRef;
    /**
     * @ORM\Column(name="date_updated", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     * @Annotation\Exclude()
     *
     * @var \DateTime
     */
    private $dateUpdated;
    /**
     * @ORM\OneToMany(targetEntity="Affiliation\Entity\Affiliation", cascade={"persist"}, mappedBy="organisation")
     * @Annotation\Exclude()
     *
     * @var \Affiliation\Entity\Affiliation[]|Collections\ArrayCollection
     */
    private $affiliation;
    /**
     * @ORM\OneToMany(targetEntity="Affiliation\Entity\Financial", cascade={"persist"}, mappedBy="organisation")
     * @Annotation\Exclude()
     *
     * @var \Affiliation\Entity\Financial
     */
    private $affiliationFinancial;
    /**
     * @ORM\OneToOne(targetEntity="Organisation\Entity\OParent", cascade={"persist"}, mappedBy="organisation", fetch="EXTRA_LAZY")
     * @Annotation\Exclude()
     *
     * @var \Organisation\Entity\OParent
     */
    private $parent;
    /**
     * @ORM\OneToMany(targetEntity="Organisation\Entity\Parent\Financial", cascade={"persist"}, mappedBy="organisation")
     * @Annotation\Exclude()
     *
     * @var \Organisation\Entity\Parent\Financial[]|Collections\ArrayCollection
     */
    private $parentFinancial;
    /**
     * @ORM\OneToOne(targetEntity="Organisation\Entity\Parent\Organisation", cascade={"persist"}, mappedBy="organisation")
     * @Annotation\Exclude()
     *
     * @var \Organisation\Entity\Parent\Organisation
     */
    private $parentOrganisation;
    /**
     * @ORM\ManyToOne(targetEntity="General\Entity\Country",inversedBy="organisation", cascade={"persist"})
     * @ORM\JoinColumn(name="country_id", referencedColumnName="country_id", nullable=true)
     * @Annotation\Type("DoctrineORMModule\Form\Element\EntitySelect")
     * @Annotation\Options({
     *      "target_class":"General\Entity\Country",
     *      "find_method":{
     *          "name":"findForForm",
     *          "params": {
     *              "criteria":{},
     *              "orderBy":{
     *                  "country":"ASC"}
     *              }
     *          }
     *      }
     * )
     * @Annotation\Attributes({"label":"txt-country","help-block":"txt-organisation-country-help-block"})
     *
     * @var \General\Entity\Country
     */
    private $country;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\Idea\Partner", cascade={"persist"}, mappedBy="organisation")
     * @Annotation\Exclude()
     *
     * @var \Project\Entity\Idea\Partner[]|Collections\ArrayCollection
     */
    private $ideaPartner;
    /**
     * @ORM\ManyToOne(targetEntity="Organisation\Entity\Type", inversedBy="organisation", cascade={"persist"})
     * @ORM\JoinColumn(name="type_id", referencedColumnName="type_id", nullable=true)
     * @Annotation\Type("DoctrineORMModule\Form\Element\EntitySelect")
     * @Annotation\Options({
     *      "target_class":"Organisation\Entity\Type",
     *      "find_method":{
     *          "name":"findBy",
     *          "params": {
     *              "criteria":{},
     *              "orderBy":{
     *                  "type":"ASC"}
     *              }
     *          }
     *      }
     * )
     * @Annotation\Attributes({"label":"txt-organisation-type"})
     * @var \Organisation\Entity\Type
     */
    private $type;
    /**
     * @ORM\OneToMany(targetEntity="Organisation\Entity\Web", cascade={"persist","remove"}, mappedBy="organisation")
     * @ORM\OrderBy({"main"="DESC"})
     *
     * @var \Organisation\Entity\Web[]|Collections\ArrayCollection
     */
    private $web;
    /**
     * @ORM\OneToMany(targetEntity="Organisation\Entity\Log", cascade={"persist","remove"}, mappedBy="organisation")
     * @Annotation\Exclude()
     *
     * @var \Organisation\Entity\Log[]|Collections\ArrayCollection
     */
    private $log;
    /**
     * @ORM\OneToOne(targetEntity="Organisation\Entity\Description", cascade={"persist","remove"}, mappedBy="organisation")
     * @Annotation\ComposedObject("Organisation\Entity\Description")
     *
     * @var \Organisation\Entity\Description
     */
    private $description;
    /**
     * @ORM\OneToMany(targetEntity="Organisation\Entity\Logo", cascade={"persist","remove"}, mappedBy="organisation")
     * @Annotation\Exclude()
     *
     * @var \Organisation\Entity\Logo[]|Collections\ArrayCollection
     */
    private $logo;
    /**
     * @ORM\OneToMany(targetEntity="Organisation\Entity\Note", cascade={"persist","remove"}, mappedBy="organisation")
     * @ORM\OrderBy({"dateCreated" = "DESC"})
     * @Annotation\Exclude()
     *
     * @var \Organisation\Entity\Note[]|Collections\ArrayCollection
     */
    private $note;
    /**
     * @ORM\OneToMany(targetEntity="Organisation\Entity\Name", cascade={"persist","remove"}, mappedBy="organisation")
     * @Annotation\Exclude()
     *
     * @var \Organisation\Entity\Name[]|Collections\ArrayCollection
     */
    private $names;
    /**
     * @ORM\OneToOne(targetEntity="Organisation\Entity\Financial", cascade={"persist","remove"}, mappedBy="organisation", fetch="EXTRA_LAZY")
     * @Annotation\Exclude()
     *
     * @var \Organisation\Entity\Financial
     */
    private $financial;
    /**
     * @ORM\OneToMany(targetEntity="\Program\Entity\Doa", cascade={"persist","remove"}, mappedBy="organisation")
     * @Annotation\Exclude()
     *
     * @var \Program\Entity\Doa[]|Collections\ArrayCollection
     */
    private $programDoa;
    /**
     * @ORM\OneToMany(targetEntity="Invoice\Entity\Invoice", cascade={"persist"}, mappedBy="organisation")
     * @Annotation\Exclude()
     *
     * @var \Invoice\Entity\Invoice[]|Collections\ArrayCollection
     */
    private $invoice;
    /**
     * @ORM\OneToMany(targetEntity="Event\Entity\Booth\Financial", cascade={"persist"}, mappedBy="organisation", fetch="EXTRA_LAZY")
     * @Annotation\Exclude()
     *
     * @var \Event\Entity\Booth\Financial[]|Collections\ArrayCollection
     */
    private $boothFinancial;
    /**
     * @ORM\OneToMany(targetEntity="Program\Entity\Call\Doa", cascade={"persist","remove"}, mappedBy="organisation")
     * @Annotation\Exclude()
     *
     * @var \Program\Entity\Call\Doa[]|Collections\ArrayCollection
     */
    private $doa;
    /**
     * @ORM\OneToMany(targetEntity="Organisation\Entity\Booth", cascade={"persist"}, mappedBy="organisation")
     * @Annotation\Exclude()
     *
     * @var \Organisation\Entity\Booth[]|Collections\ArrayCollection()
     */
    private $organisationBooth;
    /**
     * @ORM\OneToMany(targetEntity="Invoice\Entity\Journal", cascade={"persist"}, mappedBy="organisation")
     * @Annotation\Exclude()
     *
     * @var \Invoice\Entity\Journal[]|Collections\ArrayCollection()
     */
    private $journal;
    /**
     * @ORM\OneToMany(targetEntity="Invoice\Entity\Reminder", cascade={"persist"}, mappedBy="organisation")
     * @Annotation\Exclude()
     *
     * @var \Invoice\Entity\Reminder[]|Collections\ArrayCollection()
     */
    private $reminder;
    /**
     * @ORM\ManyToMany(targetEntity="Project\Entity\Result\Result", cascade={"persist"}, mappedBy="organisation")
     * @Annotation\Exclude()
     * Second param to be able to selecte more than 1 project per result
     *
     * @var \Project\Entity\Result\Result[]|Collections\ArrayCollection
     */
    private $result;

    public function __construct()
    {
        $this->affiliation = new Collections\ArrayCollection();
        $this->affiliationFinancial = new Collections\ArrayCollection();
        $this->contactOrganisation = new Collections\ArrayCollection();
        $this->parentFinancial = new Collections\ArrayCollection();
        $this->names = new Collections\ArrayCollection();
        $this->financialDebtor = new Collections\ArrayCollection();
        $this->log = new Collections\ArrayCollection();
        $this->logo = new Collections\ArrayCollection();
        $this->note = new Collections\ArrayCollection();
        $this->programDoa = new Collections\ArrayCollection();
        $this->ideaPartner = new Collections\ArrayCollection();
        $this->invoice = new Collections\ArrayCollection();
        $this->boothFinancial = new Collections\ArrayCollection();
        $this->doa = new Collections\ArrayCollection();
        $this->organisationBooth = new Collections\ArrayCollection();
        $this->journal = new Collections\ArrayCollection();
        $this->reminder = new Collections\ArrayCollection();
        $this->result = new Collections\ArrayCollection();
        $this->web = new Collections\ArrayCollection();
    }

    public function hasLogo(): bool
    {
        return null !== $this->logo && !$this->logo->isEmpty();
    }

    public function isParent(): bool
    {
        return null !== $this->parent;
    }

    public function hasParent(): bool
    {
        return null !== $this->parentOrganisation;
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
        return (string)$this->organisation;
    }

    public function parseFullName(): string
    {
        return (string)$this->organisation;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): Organisation
    {
        $this->id = $id;

        return $this;
    }

    public function getOrganisation(): ?string
    {
        return $this->organisation;
    }

    public function setOrganisation($organisation): Organisation
    {
        $this->organisation = $organisation;

        return $this;
    }

    public function getContactOrganisation()
    {
        return $this->contactOrganisation;
    }

    public function setContactOrganisation($contactOrganisation): Organisation
    {
        $this->contactOrganisation = $contactOrganisation;

        return $this;
    }

    public function getDateCreated(): ?\DateTime
    {
        return $this->dateCreated;
    }

    public function setDateCreated($dateCreated): Organisation
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    public function getDocRef(): ?string
    {
        return $this->docRef;
    }

    public function setDocRef($docRef): Organisation
    {
        $this->docRef = $docRef;

        return $this;
    }

    public function getDateUpdated(): ?\DateTime
    {
        return $this->dateUpdated;
    }

    public function setDateUpdated($dateUpdated): Organisation
    {
        $this->dateUpdated = $dateUpdated;

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

    public function getAffiliationFinancial()
    {
        return $this->affiliationFinancial;
    }

    public function setAffiliationFinancial($affiliationFinancial): Organisation
    {
        $this->affiliationFinancial = $affiliationFinancial;

        return $this;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function setParent(?\Organisation\Entity\OParent $parent): Organisation
    {
        $this->parent = $parent;

        return $this;
    }

    public function getParentFinancial()
    {
        return $this->parentFinancial;
    }

    public function setParentFinancial($parentFinancial): Organisation
    {
        $this->parentFinancial = $parentFinancial;

        return $this;
    }

    public function getParentOrganisation()
    {
        return $this->parentOrganisation;
    }

    public function setParentOrganisation(?\Organisation\Entity\Parent\Organisation $parentOrganisation): Organisation
    {
        $this->parentOrganisation = $parentOrganisation;

        return $this;
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function setCountry($country): Organisation
    {
        $this->country = $country;

        return $this;
    }

    public function getIdeaPartner()
    {
        return $this->ideaPartner;
    }

    public function setIdeaPartner($ideaPartner): Organisation
    {
        $this->ideaPartner = $ideaPartner;

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type): Organisation
    {
        $this->type = $type;

        return $this;
    }

    public function getWeb()
    {
        return $this->web;
    }

    public function setWeb($web): Organisation
    {
        $this->web = $web;

        return $this;
    }

    public function getLog()
    {
        return $this->log;
    }

    public function setLog($log): Organisation
    {
        $this->log = $log;

        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description): Organisation
    {
        $this->description = $description;

        return $this;
    }

    public function getLogo()
    {
        return $this->logo;
    }

    public function setLogo($logo): Organisation
    {
        $this->logo = $logo;

        return $this;
    }

    public function getNote()
    {
        return $this->note;
    }

    public function setNote($note): Organisation
    {
        $this->note = $note;

        return $this;
    }

    public function getFinancial()
    {
        return $this->financial;
    }

    public function setFinancial($financial): Organisation
    {
        $this->financial = $financial;

        return $this;
    }

    public function getProgramDoa()
    {
        return $this->programDoa;
    }

    public function setProgramDoa($programDoa): Organisation
    {
        $this->programDoa = $programDoa;

        return $this;
    }

    public function getInvoice()
    {
        return $this->invoice;
    }

    public function setInvoice($invoice): Organisation
    {
        $this->invoice = $invoice;

        return $this;
    }

    public function getBoothFinancial()
    {
        return $this->boothFinancial;
    }

    public function setBoothFinancial($boothFinancial): Organisation
    {
        $this->boothFinancial = $boothFinancial;

        return $this;
    }

    public function getDoa()
    {
        return $this->doa;
    }

    public function setDoa($doa): Organisation
    {
        $this->doa = $doa;

        return $this;
    }

    public function getOrganisationBooth()
    {
        return $this->organisationBooth;
    }

    public function setOrganisationBooth($organisationBooth): Organisation
    {
        $this->organisationBooth = $organisationBooth;

        return $this;
    }

    public function getJournal()
    {
        return $this->journal;
    }

    public function setJournal($journal): Organisation
    {
        $this->journal = $journal;

        return $this;
    }

    public function getReminder()
    {
        return $this->reminder;
    }

    public function setReminder($reminder): Organisation
    {
        $this->reminder = $reminder;

        return $this;
    }

    public function getResult()
    {
        return $this->result;
    }

    public function setResult($result): Organisation
    {
        $this->result = $result;

        return $this;
    }

    public function getNames()
    {
        return $this->names;
    }

    public function setNames($names): Organisation
    {
        $this->names = $names;

        return $this;
    }
}
