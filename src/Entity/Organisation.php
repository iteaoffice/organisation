<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

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
 */
class Organisation extends AbstractEntity
{
    /**
     * @ORM\Column(name="organisation_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Exclude()
     *
     * @var integer
     */
    private $id;
    /**
     * @ORM\Column(name="organisation", type="string", length=60, nullable=false)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-organisation-name","help-block":"txt-organisation-name-help-block"})
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
     * @ORM\Column(name="docref", type="string", length=255, nullable=false, unique=true)
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
     * @ORM\ManyToMany(targetEntity="Program\Entity\Domain", inversedBy="organisation")
     * @ORM\JoinTable(name="organisation_domain",
     *            joinColumns={@ORM\JoinColumn(name="organisation_id", referencedColumnName="organisation_id")},
     *            inverseJoinColumns={@ORM\JoinColumn(name="domain_id", referencedColumnName="domain_id")}
     * )
     * @Annotation\Type("DoctrineORMModule\Form\Element\EntityMultiCheckbox")
     * @Annotation\Options({"target_class":"Program\Entity\Domain"})
     * @Annotation\Attributes({"label":"txt-domain"})
     *
     * @var \Program\Entity\Domain[]|Collections\ArrayCollection
     */
    private $domain;
    /**
     * @ORM\ManyToMany(targetEntity="Program\Entity\Technology", inversedBy="organisation")
     * @ORM\JoinTable(name="organisation_technology",
     *            joinColumns={@ORM\JoinColumn(name="organisation_id", referencedColumnName="organisation_id")},
     *            inverseJoinColumns={@ORM\JoinColumn(name="technology_id", referencedColumnName="technology_id")}
     * )
     * @Annotation\Type("DoctrineORMModule\Form\Element\EntityMultiCheckbox")
     * @Annotation\Options({"target_class":"Program\Entity\Technology"})
     * @Annotation\Attributes({"label":"txt-technology"})
     *
     * @var \Program\Entity\Technology[]|Collections\ArrayCollection
     */
    private $technology;
    /**
     * @ORM\OneToMany(targetEntity="Organisation\Entity\Cluster", cascade={"persist","remove"}, mappedBy="organisation")
     * @Annotation\Exclude()
     *
     * @var \Organisation\Entity\Cluster[]|Collections\ArrayCollection
     */
    private $cluster;
    /**
     * @ORM\ManyToMany(targetEntity="Organisation\Entity\Cluster", inversedBy="member", cascade={"persist","remove"})
     * @ORM\JoinTable(name="cluster_organisation",
     *            joinColumns={@ORM\JoinColumn(name="organisation_id", referencedColumnName="organisation_id")},
     *            inverseJoinColumns={@ORM\JoinColumn(name="cluster_id", referencedColumnName="cluster_id")}
     * )
     * @Annotation\Type("DoctrineORMModule\Form\Element\EntityMultiCheckbox")
     * @Annotation\Options({"target_class":"Organisation\Entity\Cluster"})
     * @Annotation\Attributes({"label":"txt-cluster-membership"})
     *
     * @var \Organisation\Entity\Cluster[]|Collections\ArrayCollection
     */
    private $clusterMember;
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
     * @Annotation\Instance("Organisation\Entity\Description")
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
     * @ORM\OneToMany(targetEntity="Organisation\Entity\Financial", cascade={"persist"}, mappedBy="debtor")
     * @Annotation\Exclude()
     *
     * @var \Organisation\Entity\Financial[]|Collections\ArrayCollection
     */
    private $financialDebtor;
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
    /**
     * @ORM\OneToMany(targetEntity="Organisation\Entity\IctOrganisation", cascade={"persist"}, mappedBy="organisation")
     * @Annotation\Exclude()
     *
     * @var IctOrganisation[]|Collections\ArrayCollection
     */
    private $ictOrganisation;


    /**
     * Class constructor.
     */
    public function __construct()
    {
        $this->affiliation = new Collections\ArrayCollection();
        $this->affiliationFinancial = new Collections\ArrayCollection();
        $this->contactOrganisation = new Collections\ArrayCollection();
        $this->parentFinancial = new Collections\ArrayCollection();
        $this->domain = new Collections\ArrayCollection();
        $this->names = new Collections\ArrayCollection();
        $this->technology = new Collections\ArrayCollection();
        $this->cluster = new Collections\ArrayCollection();
        $this->clusterMember = new Collections\ArrayCollection();
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
        $this->ictOrganisation = new Collections\ArrayCollection();
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
     * @param string $property
     * @param mixed $value
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
     * ToString
     * Return the id here for form population.
     *
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->organisation;
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
     * @return Organisation
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getOrganisation()
    {
        return $this->organisation;
    }

    /**
     * @param string $organisation
     *
     * @return Organisation
     */
    public function setOrganisation($organisation)
    {
        $this->organisation = $organisation;

        return $this;
    }

    /**
     * @return ContactOrganisation[]|Collections\ArrayCollection
     */
    public function getContactOrganisation()
    {
        return $this->contactOrganisation;
    }

    /**
     * @param ContactOrganisation[]|Collections\ArrayCollection $contactOrganisation
     *
     * @return Organisation
     */
    public function setContactOrganisation($contactOrganisation)
    {
        $this->contactOrganisation = $contactOrganisation;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @param \DateTime $dateCreated
     *
     * @return Organisation
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * @return string
     */
    public function getDocRef()
    {
        return $this->docRef;
    }

    /**
     * @param string $docRef
     *
     * @return Organisation
     */
    public function setDocRef($docRef)
    {
        $this->docRef = $docRef;

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
     * @return Organisation
     */
    public function setDateUpdated($dateUpdated)
    {
        $this->dateUpdated = $dateUpdated;

        return $this;
    }

    /**
     * @return \Affiliation\Entity\Affiliation[]|Collections\ArrayCollection
     */
    public function getAffiliation()
    {
        return $this->affiliation;
    }

    /**
     * @param \Affiliation\Entity\Affiliation[]|Collections\ArrayCollection $affiliation
     *
     * @return Organisation
     */
    public function setAffiliation($affiliation)
    {
        $this->affiliation = $affiliation;

        return $this;
    }

    /**
     * @return \Affiliation\Entity\Financial[]|Collections\ArrayCollection
     */
    public function getAffiliationFinancial()
    {
        return $this->affiliationFinancial;
    }

    /**
     * @param \Affiliation\Entity\Financial[]|Collections\ArrayCollection $affiliationFinancial
     *
     * @return Organisation
     */
    public function setAffiliationFinancial($affiliationFinancial)
    {
        $this->affiliationFinancial = $affiliationFinancial;

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
     * @return Organisation
     */
    public function setParent(\Organisation\Entity\OParent $parent): Organisation
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Financial[]|Collections\ArrayCollection
     */
    public function getParentFinancial()
    {
        return $this->parentFinancial;
    }

    /**
     * @param Financial[]|Collections\ArrayCollection $parentFinancial
     *
     * @return Organisation
     */
    public function setParentFinancial($parentFinancial)
    {
        $this->parentFinancial = $parentFinancial;

        return $this;
    }

    /**
     * @return \Organisation\Entity\Parent\Organisation
     */
    public function getParentOrganisation()
    {
        return $this->parentOrganisation;
    }

    /**
     * @param \Organisation\Entity\Parent\Organisation
     *
     * @return Organisation
     */
    public function setParentOrganisation($parentOrganisation)
    {
        $this->parentOrganisation = $parentOrganisation;

        return $this;
    }

    /**
     * @return \General\Entity\Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param \General\Entity\Country $country
     *
     * @return Organisation
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Project\Entity\Idea\Partner[]
     */
    public function getIdeaPartner()
    {
        return $this->ideaPartner;
    }

    /**
     * @param Collections\ArrayCollection|\Project\Entity\Idea\Partner[] $ideaPartner
     *
     * @return Organisation
     */
    public function setIdeaPartner($ideaPartner)
    {
        $this->ideaPartner = $ideaPartner;

        return $this;
    }

    /**
     * @return Type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param Type $type
     *
     * @return Organisation
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|Web[]
     */
    public function getWeb()
    {
        return $this->web;
    }

    /**
     * @param Collections\ArrayCollection|Web[] $web
     *
     * @return Organisation
     */
    public function setWeb($web)
    {
        $this->web = $web;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Program\Entity\Domain[]
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param Collections\ArrayCollection|\Program\Entity\Domain[] $domain
     *
     * @return Organisation
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Program\Entity\Technology[]
     */
    public function getTechnology()
    {
        return $this->technology;
    }

    /**
     * @param Collections\ArrayCollection|\Program\Entity\Technology[] $technology
     *
     * @return Organisation
     */
    public function setTechnology($technology)
    {
        $this->technology = $technology;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|Cluster[]
     */
    public function getCluster()
    {
        return $this->cluster;
    }

    /**
     * @param Collections\ArrayCollection|Cluster[] $cluster
     *
     * @return Organisation
     */
    public function setCluster($cluster)
    {
        $this->cluster = $cluster;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|Cluster[]
     */
    public function getClusterMember()
    {
        return $this->clusterMember;
    }

    /**
     * @param Collections\ArrayCollection|Cluster[] $clusterMember
     *
     * @return Organisation
     */
    public function setClusterMember($clusterMember)
    {
        $this->clusterMember = $clusterMember;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|Log[]
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * @param Collections\ArrayCollection|Log[] $log
     *
     * @return Organisation
     */
    public function setLog($log)
    {
        $this->log = $log;

        return $this;
    }

    /**
     * @return Description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param Description $description
     *
     * @return Organisation
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|Logo[]
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * @param Collections\ArrayCollection|Logo[] $logo
     *
     * @return Organisation
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|Note[]
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param Collections\ArrayCollection|Note[] $note
     *
     * @return Organisation
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * @return Financial
     */
    public function getFinancial()
    {
        return $this->financial;
    }

    /**
     * @param Financial $financial
     *
     * @return Organisation
     */
    public function setFinancial($financial)
    {
        $this->financial = $financial;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|Financial[]
     */
    public function getFinancialDebtor()
    {
        return $this->financialDebtor;
    }

    /**
     * @param Collections\ArrayCollection|Financial[] $financialDebtor
     *
     * @return Organisation
     */
    public function setFinancialDebtor($financialDebtor)
    {
        $this->financialDebtor = $financialDebtor;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Program\Entity\Doa[]
     */
    public function getProgramDoa()
    {
        return $this->programDoa;
    }

    /**
     * @param Collections\ArrayCollection|\Program\Entity\Doa[] $programDoa
     *
     * @return Organisation
     */
    public function setProgramDoa($programDoa)
    {
        $this->programDoa = $programDoa;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Invoice\Entity\Invoice[]
     */
    public function getInvoice()
    {
        return $this->invoice;
    }

    /**
     * @param Collections\ArrayCollection|\Invoice\Entity\Invoice[] $invoice
     *
     * @return Organisation
     */
    public function setInvoice($invoice)
    {
        $this->invoice = $invoice;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Event\Entity\Booth\Financial[]
     */
    public function getBoothFinancial()
    {
        return $this->boothFinancial;
    }

    /**
     * @param Collections\ArrayCollection|\Event\Entity\Booth\Financial[] $boothFinancial
     *
     * @return Organisation
     */
    public function setBoothFinancial($boothFinancial)
    {
        $this->boothFinancial = $boothFinancial;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Program\Entity\Call\Doa[]
     */
    public function getDoa()
    {
        return $this->doa;
    }

    /**
     * @param Collections\ArrayCollection|\Program\Entity\Call\Doa[] $doa
     *
     * @return Organisation
     */
    public function setDoa($doa)
    {
        $this->doa = $doa;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|Booth[]
     */
    public function getOrganisationBooth()
    {
        return $this->organisationBooth;
    }

    /**
     * @param Collections\ArrayCollection|Booth[] $organisationBooth
     *
     * @return Organisation
     */
    public function setOrganisationBooth($organisationBooth)
    {
        $this->organisationBooth = $organisationBooth;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Invoice\Entity\Journal[]
     */
    public function getJournal()
    {
        return $this->journal;
    }

    /**
     * @param Collections\ArrayCollection|\Invoice\Entity\Journal[] $journal
     *
     * @return Organisation
     */
    public function setJournal($journal)
    {
        $this->journal = $journal;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Invoice\Entity\Reminder[]
     */
    public function getReminder()
    {
        return $this->reminder;
    }

    /**
     * @param Collections\ArrayCollection|\Invoice\Entity\Reminder[] $reminder
     *
     * @return Organisation
     */
    public function setReminder($reminder)
    {
        $this->reminder = $reminder;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Project\Entity\Result\Result[]
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param Collections\ArrayCollection|\Project\Entity\Result\Result[] $result
     *
     * @return Organisation
     */
    public function setResult($result)
    {
        $this->result = $result;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|Name[]
     */
    public function getNames()
    {
        return $this->names;
    }

    /**
     * @param Collections\ArrayCollection|Name[] $names
     *
     * @return Organisation
     */
    public function setNames($names)
    {
        $this->names = $names;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|IctOrganisation[]
     */
    public function getIctOrganisation()
    {
        return $this->ictOrganisation;
    }

    /**
     * @param Collections\ArrayCollection|IctOrganisation[] $ictOrganisation
     * @return Organisation
     */
    public function setIctOrganisation($ictOrganisation)
    {
        $this->ictOrganisation = $ictOrganisation;
        return $this;
    }
}
