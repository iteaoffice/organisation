<?php
/**
 * Debranova copyright message placeholder
 *
 * @category    Organisation
 * @package     Entity
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Debranova
 */
namespace Organisation\Entity;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\Factory as InputFactory;
use Zend\Form\Annotation;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Organisation
 *
 * @ORM\Table(name="organisation")
 * @ORM\Entity
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("organisation")
 */
class Organisation extends EntityAbstract
{
    /**
     * @ORM\Column(name="organisation_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Exclude()
     * @var integer
     */
    private $id;
    /**
     * @ORM\Column(name="organisation", type="string", length=60, nullable=false)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-organisation"})
     * @var string
     */
    private $organisation;
    /**
     * @ORM\OneToOne(targetEntity="\Contact\Entity\ContactOrganisation", cascade={"persist"}, mappedBy="organisation")
     * @Annotation\Exclude()
     * @var \Contact\Entity\ContactOrganisation
     */
    private $contactOrganisation;
    /**
     * @ORM\Column(name="date_created", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="create")
     * @Annotation\Exclude()
     * @var \DateTime
     */
    private $dateCreated;
    /**
     * @ORM\Column(name="docref", type="string", length=255, nullable=false, unique=true)
     * @Gedmo\Slug(fields={"id","organisation"})
     * @Annotation\Exclude()
     * @var string
     */
    private $docRef;
    /**
     * @ORM\Column(name="date_updated", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     * @Annotation\Exclude()
     * @var \DateTime
     */
    private $dateUpdated;
    /**
     * @ORM\OneToMany(targetEntity="Affiliation\Entity\Affiliation", cascade={"persist"}, mappedBy="organisation")
     * @Annotation\Exclude()
     * @var \Affiliation\Entity\Affiliation[]
     */
    private $affiliation;
    /**
     * @ORM\ManyToOne(targetEntity="General\Entity\Country",inversedBy="organisation", cascade={"persist"})
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="country_id", referencedColumnName="country_id", nullable=true)
     * })
     * @Annotation\Type("DoctrineORMModule\Form\Element\EntitySelect")
     * @Annotation\Options({
     *      "target_class":"General\Entity\Country",
     *      "find_method":{
     *          "name":"findBy",
     *          "params": {
     *              "criteria":{},
     *              "orderBy":{
     *                  "country":"ASC"}
     *              }
     *          }
     *      }
     * )
     * @Annotation\Attributes({"label":"txt-country"})
     * @var \General\Entity\Country
     */
    private $country;
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
     * @var \Organisation\Entity\Web[]
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
     * @var \Program\Entity\Domain[]
     */
    private $domain;
    /**
     * @ORM\ManyToMany(targetEntity="Program\Entity\Technology", inversedBy="organisation")
     * @ORM\JoinTable(name="cluster_organisation",
     *            joinColumns={@ORM\JoinColumn(name="organisation_id", referencedColumnName="organisation_id")},
     *            inverseJoinColumns={@ORM\JoinColumn(name="technology_id", referencedColumnName="technology_id")}
     * )
     * @Annotation\Type("DoctrineORMModule\Form\Element\EntityMultiCheckbox")
     * @Annotation\Options({"target_class":"Program\Entity\Technology"})
     * @Annotation\Attributes({"label":"txt-technology"})
     * @var \Program\Entity\Technology[]
     */
    private $technology;
    /**
     * @ORM\OneToMany(targetEntity="Organisation\Entity\Cluster", cascade={"persist"}, mappedBy="organisation")
     * @Annotation\Exclude()
     * @var \Organisation\Entity\Cluster[]
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
     * @var \Organisation\Entity\Cluster[]
     */
    private $clusterMember;
    /**
     * @ORM\OneToMany(targetEntity="Organisation\Entity\Log", cascade={"persist"}, mappedBy="organisation")
     * @Annotation\Exclude()
     * @var \Organisation\Entity\Log[]
     */
    private $log;
    /**
     * @ORM\OneToOne(targetEntity="Organisation\Entity\Description", cascade={"persist"}, mappedBy="organisation")
     * @Annotation\Exclude()
     * @var \Organisation\Entity\Description[]
     */
    private $description;
    /**
     * @ORM\OneToMany(targetEntity="Organisation\Entity\Logo", cascade={"persist"}, mappedBy="organisation")
     * @Annotation\Exclude()
     * @var \Organisation\Entity\Logo[]
     */
    private $logo;
    /**
     * @ORM\OneToMany(targetEntity="Organisation\Entity\Note", cascade={"persist"}, mappedBy="organisation")
     * @Annotation\Exclude()
     * @var \Organisation\Entity\Note[]
     */
    private $note;
    /**
     * @ORM\OneToMany(targetEntity="Organisation\Entity\Financial", cascade={"persist"}, mappedBy="organisation")
     * @Annotation\Exclude()
     * @var \Organisation\Entity\Financial[]
     */
    private $financial;
    /**
     * @ORM\OneToMany(targetEntity="Organisation\Entity\Financial", cascade={"persist"}, mappedBy="debtor")
     * @Annotation\Exclude()
     * @var \Organisation\Entity\Financial[]
     */
    private $financialDebtor;
    /**
     * @ORM\OneToMany(targetEntity="\Program\Entity\ProgramDoa", cascade={"persist"}, mappedBy="organisation")
     * @Annotation\Exclude()
     * @var \Program\Entity\ProgramDoa[]
     */
    private $programDoa;
    /**
     * @ORM\OneToMany(targetEntity="Invoice\Entity\Invoice", cascade={"persist"}, mappedBy="organisation")
     * @Annotation\Exclude()
     * @var \Invoice\Entity\Invoice[]
     */
    private $invoice;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->organisation    = new Collections\ArrayCollection();
        $this->affiliation     = new Collections\ArrayCollection();
        $this->domain          = new Collections\ArrayCollection();
        $this->technology      = new Collections\ArrayCollection();
        $this->cluster         = new Collections\ArrayCollection();
        $this->clusterMember   = new Collections\ArrayCollection();
        $this->log             = new Collections\ArrayCollection();
        $this->description     = new Collections\ArrayCollection();
        $this->logo            = new Collections\ArrayCollection();
        $this->note            = new Collections\ArrayCollection();
        $this->financial       = new Collections\ArrayCollection();
        $this->financialDebtor = new Collections\ArrayCollection();
        $this->programDoa      = new Collections\ArrayCollection();
        $this->invoice         = new Collections\ArrayCollection();
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
     * @return void
     */
    public function __set($property, $value)
    {
        $this->$property = $value;
    }

    /**
     * ToString
     * Return the id here for form population
     * @return string
     */
    public function __toString()
    {
        return (string)$this->organisation . ', ' . $this->getCountry()->getIso3();
    }

    /**
     * @param InputFilterInterface $inputFilter
     *
     * @return void
     * @throws \Exception
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception(sprintf("This class %s is unused", __CLASS__));
    }

    /**
     * @return \Zend\InputFilter\InputFilter|\Zend\InputFilter\InputFilterInterface
     */
    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory     = new InputFactory();

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'       => 'organisation',
                        'required'   => true,
                        'filters'    => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name'    => 'StringLength',
                                'options' => array(
                                    'encoding' => 'UTF-8',
                                    'min'      => 1,
                                    'max'      => 255,
                                ),
                            ),
                        ),
                    )
                )
            );

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'country',
                        'required' => true,
                    )
                )
            );

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'type',
                        'required' => true,
                    )
                )
            );
        }


        return $this->inputFilter;
    }

    /**
     * Needed for the hydration of form elements
     * @return array
     */
    public function getArrayCopy()
    {
        return array(
            'organisation' => $this->organisation,
            'country'      => $this->country,
            'type'         => $this->type,
        );
    }

    /**
     * @param \Affiliation\Entity\Affiliation[] $affiliation
     */
    public function setAffiliation($affiliation)
    {
        $this->affiliation = $affiliation;
    }

    /**
     * @return \Affiliation\Entity\Affiliation[]
     */
    public function getAffiliation()
    {
        return $this->affiliation;
    }

    /**
     * @param \Organisation\Entity\Cluster[] $cluster
     */
    public function setCluster($cluster)
    {
        $this->cluster = $cluster;
    }

    /**
     * @return \Organisation\Entity\Cluster[]
     */
    public function getCluster()
    {
        return $this->cluster;
    }

    /**
     * @param \Organisation\Entity\Cluster[] $clusterMember
     */
    public function setClusterMember($clusterMember)
    {
        $this->clusterMember = $clusterMember;
    }

    /**
     * @return \Organisation\Entity\Cluster[]
     */
    public function getClusterMember()
    {
        return $this->clusterMember;
    }

    /**
     * @param \Contact\Entity\ContactOrganisation $contactOrganisation
     */
    public function setContactOrganisation($contactOrganisation)
    {
        $this->contactOrganisation = $contactOrganisation;
    }

    /**
     * @return \Contact\Entity\ContactOrganisation
     */
    public function getContactOrganisation()
    {
        return $this->contactOrganisation;
    }

    /**
     * @param \General\Entity\Country $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @return \General\Entity\Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param \DateTime $dateCreated
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
    }

    /**
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @param \DateTime $dateUpdated
     */
    public function setDateUpdated($dateUpdated)
    {
        $this->dateUpdated = $dateUpdated;
    }

    /**
     * @return \DateTime
     */
    public function getDateUpdated()
    {
        return $this->dateUpdated;
    }

    /**
     * @param \Organisation\Entity\Description[] $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return \Organisation\Entity\Description[]
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $docRef
     */
    public function setDocRef($docRef)
    {
        $this->docRef = $docRef;
    }

    /**
     * @return string
     */
    public function getDocRef()
    {
        return $this->docRef;
    }

    /**
     * @param \Program\Entity\Domain[] $domain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * @return \Program\Entity\Domain[]
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param \Organisation\Entity\Financial[] $financial
     */
    public function setFinancial($financial)
    {
        $this->financial = $financial;
    }

    /**
     * @return \Organisation\Entity\Financial[]
     */
    public function getFinancial()
    {
        return $this->financial;
    }

    /**
     * @param \Organisation\Entity\Financial[] $financialDebtor
     */
    public function setFinancialDebtor($financialDebtor)
    {
        $this->financialDebtor = $financialDebtor;
    }

    /**
     * @return \Organisation\Entity\Financial[]
     */
    public function getFinancialDebtor()
    {
        return $this->financialDebtor;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param \Organisation\Entity\Log[] $log
     */
    public function setLog($log)
    {
        $this->log = $log;
    }

    /**
     * @return \Organisation\Entity\Log[]
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * @param \Organisation\Entity\Logo[] $logo
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;
    }

    /**
     * @return \Organisation\Entity\Logo[]
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * @param \Organisation\Entity\Note[] $note
     */
    public function setNote($note)
    {
        $this->note = $note;
    }

    /**
     * @return \Organisation\Entity\Note[]
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param string $organisation
     */
    public function setOrganisation($organisation)
    {
        $this->organisation = $organisation;
    }

    /**
     * @return string
     */
    public function getOrganisation()
    {
        return $this->organisation;
    }

    /**
     * @param \Program\Entity\ProgramDoa[] $programDoa
     */
    public function setProgramDoa($programDoa)
    {
        $this->programDoa = $programDoa;
    }

    /**
     * @return \Program\Entity\ProgramDoa[]
     */
    public function getProgramDoa()
    {
        return $this->programDoa;
    }

    /**
     * @param \Program\Entity\Technology[] $technology
     */
    public function setTechnology($technology)
    {
        $this->technology = $technology;
    }

    /**
     * @return \Program\Entity\Technology[]
     */
    public function getTechnology()
    {
        return $this->technology;
    }

    /**
     * @param \Organisation\Entity\Type $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return \Organisation\Entity\Type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param \Invoice\Entity\Invoice[] $invoice
     */
    public function setInvoice($invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * @return \Invoice\Entity\Invoice[]
     */
    public function getInvoice()
    {
        return $this->invoice;
    }

    /**
     * @param \Organisation\Entity\Web[] $web
     */
    public function setWeb($web)
    {
        $this->web = $web;
    }

    /**
     * @return \Organisation\Entity\Web[]
     */
    public function getWeb()
    {
        return $this->web;
    }
}

