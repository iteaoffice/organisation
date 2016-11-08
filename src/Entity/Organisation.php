<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 */

namespace Organisation\Entity;

use Doctrine\Common\Collections;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Zend\Form\Annotation;
use Zend\Permissions\Acl\Resource\ResourceInterface;

/**
 * Organisation.
 *
 * @ORM\Table(name="organisation")
 * @ORM\Entity(repositoryClass="Organisation\Repository\Organisation")
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("organisation")
 */
class Organisation extends EntityAbstract implements ResourceInterface
{
    /**
     * @ORM\Column(name="organisation_id", length=10, type="integer", nullable=false)
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
     * @var \Contact\Entity\ContactOrganisation[]
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
     * @ORM\OneToOne(targetEntity="Partner\Entity\Partner", cascade={"persist"}, mappedBy="organisation", fetch="EXTRA_LAZY")
     * @Annotation\Exclude()
     *
     * @var \Partner\Entity\Partner
     */
    private $partner;
    /**
     * @ORM\OneToMany(targetEntity="Partner\Entity\Financial", cascade={"persist"}, mappedBy="organisation")
     * @Annotation\Exclude()
     *
     * @var \Partner\Entity\Financial[]|Collections\ArrayCollection
     */
    private $partnerFinancial;
    /**
     * @ORM\OneToMany(targetEntity="Partner\Entity\Organisation", cascade={"persist"}, mappedBy="organisation")
     * @Annotation\Exclude()
     *
     * @var \Partner\Entity\Organisation[]|Collections\ArrayCollection
     */
    private $partnerOrganisation;
    /**
     * @ORM\ManyToOne(targetEntity="General\Entity\Country",inversedBy="organisation", cascade={"persist"})
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="country_id", referencedColumnName="country_id", nullable=true)
     * })
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
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="type_id", referencedColumnName="type_id", nullable=true)
     * })
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
     * @ORM\OneToMany(targetEntity="Organisation\Entity\Web", cascade={"persist"}, mappedBy="organisation")
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
     * @ORM\OneToMany(targetEntity="Organisation\Entity\Cluster", cascade={"persist"}, mappedBy="organisation")
     * @Annotation\Exclude()
     *
     * @var \Organisation\Entity\Cluster[]|Collections\ArrayCollection
     */
    private $cluster;
    /**
     * @ORM\ManyToMany(targetEntity="Organisation\Entity\Cluster", inversedBy="member", cascade={"persist"})
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
     * @ORM\OneToMany(targetEntity="Organisation\Entity\Log", cascade={"persist"}, mappedBy="organisation")
     * @Annotation\Exclude()
     *
     * @var \Organisation\Entity\Log[]|Collections\ArrayCollection
     */
    private $log;
    /**
     * @ORM\OneToOne(targetEntity="Organisation\Entity\Description", cascade={"persist"}, mappedBy="organisation", fetch="EXTRA_LAZY")
     * @Annotation\Exclude()
     *
     * @var \Organisation\Entity\Description
     */
    private $description;
    /**
     * @ORM\OneToMany(targetEntity="Organisation\Entity\Logo", cascade={"persist"}, mappedBy="organisation")
     * @Annotation\Exclude()
     *
     * @var \Organisation\Entity\Logo[]|Collections\ArrayCollection
     */
    private $logo;
    /**
     * @ORM\OneToMany(targetEntity="Organisation\Entity\Note", cascade={"persist"}, mappedBy="organisation")
     * @Annotation\Exclude()
     *
     * @var \Organisation\Entity\Note[]|Collections\ArrayCollection
     */
    private $note;
    /**
     * @ORM\OneToOne(targetEntity="Organisation\Entity\Financial", cascade={"persist"}, mappedBy="organisation", fetch="EXTRA_LAZY")
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
     * @ORM\OneToMany(targetEntity="\Program\Entity\Doa", cascade={"persist"}, mappedBy="organisation")
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
     * @ORM\OneToMany(targetEntity="Program\Entity\Call\Doa", cascade={"persist"}, mappedBy="organisation")
     * @Annotation\Exclude()
     *
     * @var \Program\Entity\Call\Doa[]|Collections\ArrayCollection
     */
    private $doa;

    /**
     * @ORM\OneToOne(targetEntity="Partner\Entity\Applicant", cascade={"persist"}, mappedBy="organisation", fetch="EXTRA_LAZY")
     * @Annotation\Exclude()
     *
     * @var \Partner\Entity\Applicant
     */
    private $applicant;
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
     * Class constructor.
     */
    public function __construct()
    {
        $this->affiliation          = new Collections\ArrayCollection();
        $this->affiliationFinancial = new Collections\ArrayCollection();
        $this->partnerOrganisation  = new Collections\ArrayCollection();
        $this->partnerFinancial     = new Collections\ArrayCollection();
        $this->domain               = new Collections\ArrayCollection();
        $this->technology           = new Collections\ArrayCollection();
        $this->cluster              = new Collections\ArrayCollection();
        $this->clusterMember        = new Collections\ArrayCollection();
        $this->financialDebtor      = new Collections\ArrayCollection();
        $this->log                  = new Collections\ArrayCollection();
        $this->logo                 = new Collections\ArrayCollection();
        $this->note                 = new Collections\ArrayCollection();
        $this->programDoa           = new Collections\ArrayCollection();
        $this->ideaPartner          = new Collections\ArrayCollection();
        $this->invoice              = new Collections\ArrayCollection();
        $this->boothFinancial       = new Collections\ArrayCollection();
        $this->doa                  = new Collections\ArrayCollection();
        $this->organisationBooth    = new Collections\ArrayCollection();
        $this->journal              = new Collections\ArrayCollection();
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
     */
    public function __set($property, $value)
    {
        $this->$property = $value;
    }

    /**
     * ToString
     * Return the id here for form population.
     *
     * @return string
     */
    public function __toString()
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
     * @return \Contact\Entity\ContactOrganisation[]|Collections\ArrayCollection
     */
    public function getContactOrganisation()
    {
        return $this->contactOrganisation;
    }

    /**
     * @param \Contact\Entity\ContactOrganisation[] $contactOrganisation
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
     * @return \Affiliation\Entity\Financial
     */
    public function getAffiliationFinancial()
    {
        return $this->affiliationFinancial;
    }

    /**
     * @param \Affiliation\Entity\Financial $affiliationFinancial
     *
     * @return Organisation
     */
    public function setAffiliationFinancial($affiliationFinancial)
    {
        $this->affiliationFinancial = $affiliationFinancial;

        return $this;
    }

    /**
     * @return \Partner\Entity\Partner
     */
    public function getPartner()
    {
        return $this->partner;
    }

    /**
     * @param \Partner\Entity\Partner $partner
     *
     * @return Organisation
     */
    public function setPartner($partner)
    {
        $this->partner = $partner;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Partner\Entity\Financial[]
     */
    public function getPartnerFinancial()
    {
        return $this->partnerFinancial;
    }

    /**
     * @param Collections\ArrayCollection|\Partner\Entity\Financial[] $partnerFinancial
     *
     * @return Organisation
     */
    public function setPartnerFinancial($partnerFinancial)
    {
        $this->partnerFinancial = $partnerFinancial;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Partner\Entity\Organisation[]
     */
    public function getPartnerOrganisation()
    {
        return $this->partnerOrganisation;
    }

    /**
     * @param Collections\ArrayCollection|\Partner\Entity\Organisation[] $partnerOrganisation
     *
     * @return Organisation
     */
    public function setPartnerOrganisation($partnerOrganisation)
    {
        $this->partnerOrganisation = $partnerOrganisation;

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
     * @return \Partner\Entity\Applicant
     */
    public function getApplicant()
    {
        return $this->applicant;
    }

    /**
     * @param \Partner\Entity\Applicant $applicant
     *
     * @return Organisation
     */
    public function setApplicant($applicant)
    {
        $this->applicant = $applicant;

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
}
