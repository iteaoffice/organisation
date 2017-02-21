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

namespace Organisation\Entity;

use Doctrine\Common\Collections;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Organisation\Entity\Parent\Financial;
use Zend\Form\Annotation;

/**
 * Entity for the Organisation.
 *
 * @ORM\Table(name="organisation_parent")
 * @ORM\Entity(repositoryClass="Organisation\Repository\OParent")
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("organisation_parent")
 */
class OParent extends AbstractEntity
{
    const EPOSS_MEMBER_TYPE_NO_MEMBER = 1;
    const EPOSS_MEMBER_TYPE_MEMBER = 2;
    const EPOSS_MEMBER_TYPE_DOA_SIGNER = 3;

    const ARTEMISIA_MEMBER_TYPE_NO_MEMBER = 1;
    const ARTEMISIA_MEMBER_TYPE_MEMBER = 2;
    const ARTEMISIA_MEMBER_TYPE_DOA_SIGNER = 3;

    //Create a set of criteria as dedicated constants as they don't fit in the normal type/status tables
    const CRITERION_C_CHAMBER = 1;
    const CRITERION_FREE_RIDER = 2;

    /**
     * @var array
     */
    protected static $epossMemberTypeTemplates
        = [
            self::EPOSS_MEMBER_TYPE_NO_MEMBER  => 'txt-not-eposs-member',
            self::EPOSS_MEMBER_TYPE_MEMBER     => 'txt-eposs-member',
            self::EPOSS_MEMBER_TYPE_DOA_SIGNER => 'txt-eposs-doa-signer',
        ];

    /**
     * @var array
     */
    protected static $artemisiaMemberTypeTemplates
        = [
            self::ARTEMISIA_MEMBER_TYPE_NO_MEMBER  => 'txt-not-artemisia-member',
            self::ARTEMISIA_MEMBER_TYPE_MEMBER     => 'txt-artemisia-member',
            self::ARTEMISIA_MEMBER_TYPE_DOA_SIGNER => 'txt-artemisia-doa-signer',
        ];


    /**
     * @ORM\Column(name="parent_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Type("Zend\Form\Element\Hidden")
     *
     * @var int
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="Contact\Entity\Contact", inversedBy="parent", cascade={"persist"})
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id", nullable=false)
     * })
     * @Annotation\Type("Contact\Form\Element\Contact")
     * @Annotation\Attributes({"label":"txt-organisation-representative"})
     *
     * @var \Contact\Entity\Contact
     */
    private $contact;
    /**
     * @ORM\ManyToOne(targetEntity="Organisation\Entity\Parent\Type", inversedBy="parent", cascade={"persist"})
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="type_id", referencedColumnName="type_id", nullable=false)
     * })
     * @Annotation\Type("DoctrineORMModule\Form\Element\EntitySelect")
     * @Annotation\Options({
     *      "target_class":"Organisation\Entity\Parent\Type",
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
     * @Annotation\Attributes({"label":"txt-parent-type"})
     *
     * @var \Organisation\Entity\Parent\Type
     */
    private $type;
    /**
     * @ORM\ManyToOne(targetEntity="Organisation\Entity\Parent\Status", inversedBy="parent", cascade={"persist"})
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="status_id", referencedColumnName="status_id", nullable=false)
     * })
     * @Annotation\Type("DoctrineORMModule\Form\Element\EntitySelect")
     * @Annotation\Options({
     *      "target_class":"Organisation\Entity\Parent\Status",
     *      "find_method":{
     *          "name":"findBy",
     *          "params": {
     *              "criteria":{},
     *              "orderBy":{
     *                  "status":"ASC"}
     *              }
     *          }
     *      }
     * )
     * @Annotation\Attributes({"label":"txt-organisation-parent-status"})
     *
     * @var \Organisation\Entity\Parent\Status
     */
    private $status;
    /**
     * @ORM\Column(name="eposs_member_type", type="smallint", nullable=false)
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Attributes({"array":"epossMemberTypeTemplates"})
     * @Annotation\Attributes({"label":"txt-eposs-member-type"})
     * @Annotation\Options({"help-block":"txt-is-eposs-member-type-explanation"})
     *
     * @var int
     */
    private $epossMemberType;
    /**
     * @ORM\Column(name="artemisia_member_type", type="smallint", nullable=false)
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Attributes({"array":"artemisiaMemberTypeTemplates"})
     * @Annotation\Attributes({"label":"txt-artemisia-member-type"})
     * @Annotation\Options({"help-block":"txt-is-artemisia-member-type-explanation"})
     *
     * @var int
     */
    private $artemisiaMemberType;
    /**
     * @ORM\OneToOne(targetEntity="Organisation\Entity\Parent\Financial", cascade={"persist","remove"}, mappedBy="parent")
     * @Annotation\Exclude()
     *
     * @var \Organisation\Entity\Parent\Financial
     */
    private $financial;
    /**
     * @ORM\OneToOne(targetEntity="Organisation\Entity\Organisation", inversedBy="parent", cascade={"persist"})
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="organisation_id", referencedColumnName="organisation_id", nullable=false)
     * })
     * @Annotation\Type("Organisation\Form\Element\Organisation")
     * @Annotation\Attributes({"label":"txt-organisation"})
     *
     * @var \Organisation\Entity\Organisation
     */
    private $organisation;
    /**
     * @ORM\OneToMany(targetEntity="Organisation\Entity\Parent\Organisation", mappedBy="parent", cascade={"persist"})
     * @Annotation\Exclude()
     *
     * @var \Organisation\Entity\Parent\Organisation[]|Collections\ArrayCollection
     */
    private $parentOrganisation;
    /**
     * @ORM\Column(name="date_created", type="datetime", nullable=false)
     * @Gedmo\Timestampable(on="create")
     * @Annotation\Exclude()
     *
     * @var \DateTime
     */
    private $dateCreated;
    /**
     * @ORM\Column(name="date_parent_type_update", type="datetime", nullable=true)
     * @Annotation\Type("Zend\Form\Element\Date")
     * @Annotation\Attributes({"label":"txt-date-parent-type-update"})
     *
     * @var \DateTime
     */
    private $dateParentTypeUpdate;
    /**
     * @ORM\Column(name="date_updated", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     * @Annotation\Exclude()
     *
     * @var \DateTime
     */
    private $dateUpdated;
    /**
     * @ORM\Column(name="date_end", type="datetime", nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Date")
     * @Annotation\Options({"label":"txt-organisation-date-end"})
     *
     * @var \DateTime
     */
    private $dateEnd;
    /**
     * @ORM\OneToMany(targetEntity="Organisation\Entity\Parent\Invoice", cascade={"persist"}, mappedBy="parent")
     * @Annotation\Exclude()
     *
     * @var \Organisation\Entity\Parent\Invoice[]|Collections\ArrayCollection()
     */
    private $invoice;
    /**
     * @ORM\OneToMany(targetEntity="Organisation\Entity\Parent\Doa", cascade={"persist"}, mappedBy="parent")
     * @Annotation\Exclude()
     *
     * @var \Organisation\Entity\Parent\Doa[]|Collections\ArrayCollection()
     */
    private $doa;


    /**
     * Class constructor.
     */
    public function __construct()
    {
        $this->invoice = new Collections\ArrayCollection();
        $this->parentOrganisation = new Collections\ArrayCollection();
        $this->doa = new Collections\ArrayCollection();
        $this->epossMemberType = self::EPOSS_MEMBER_TYPE_NO_MEMBER;
        $this->artemisiaMemberType = self::ARTEMISIA_MEMBER_TYPE_NO_MEMBER;
    }

    /**
     * @return array
     */
    public static function getEpossMemberTypeTemplates(): array
    {
        return self::$epossMemberTypeTemplates;
    }

    /**
     * @return array
     */
    public static function getArtemisiaMemberTypeTemplates(): array
    {
        return self::$artemisiaMemberTypeTemplates;
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
     * @return OParent
     */
    public function setId($id): OParent
    {
        $this->id = $id;

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
     * @return OParent
     */
    public function setContact(\Contact\Entity\Contact $contact): OParent
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * @return Parent\Type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param Parent\Type $type
     *
     * @return OParent
     */
    public function setType(Parent\Type $type): OParent
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Parent\Status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param Parent\Status $status
     *
     * @return OParent
     */
    public function setStatus(Parent\Status $status): OParent
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @param bool $textual
     *
     * @return int|string
     */
    public function getEpossMemberType($textual = false)
    {
        if ($textual) {
            return self::$epossMemberTypeTemplates[$this->epossMemberType];
        }

        return $this->epossMemberType;
    }

    /**
     * @param int $epossMemberType
     *
     * @return OParent
     */
    public function setEpossMemberType(int $epossMemberType): OParent
    {
        $this->epossMemberType = $epossMemberType;

        return $this;
    }

    /**
     * @param bool $textual
     *
     * @return int|string
     */
    public function getArtemisiaMemberType($textual = false)
    {
        if ($textual) {
            return self::$artemisiaMemberTypeTemplates[$this->artemisiaMemberType];
        }

        return $this->artemisiaMemberType;
    }

    /**
     * @param int $artemisiaMemberType
     *
     * @return OParent
     */
    public function setArtemisiaMemberType(int $artemisiaMemberType): OParent
    {
        $this->artemisiaMemberType = $artemisiaMemberType;

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
     * @return OParent
     */
    public function setFinancial(Financial $financial): OParent
    {
        $this->financial = $financial;

        return $this;
    }

    /**
     * @return Organisation
     */
    public function getOrganisation()
    {
        return $this->organisation;
    }

    /**
     * @param Organisation $organisation
     *
     * @return OParent
     */
    public function setOrganisation(Organisation $organisation): OParent
    {
        $this->organisation = $organisation;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|Parent\Organisation[]
     */
    public function getParentOrganisation()
    {
        return $this->parentOrganisation;
    }

    /**
     * @param Collections\ArrayCollection|Parent\Organisation[] $parentOrganisation
     *
     * @return OParent
     */
    public function setParentOrganisation($parentOrganisation)
    {
        $this->parentOrganisation = $parentOrganisation;

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
     * @return OParent
     */
    public function setDateCreated(\DateTime $dateCreated): OParent
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * @return string
     */
    public function getDateParentTypeUpdate()
    {
        return $this->dateParentTypeUpdate;
    }

    /**
     * @param \DateTime $dateParentTypeUpdate
     *
     * @return OParent
     */
    public function setDateParentTypeUpdate($dateParentTypeUpdate): OParent
    {
        $this->dateParentTypeUpdate = $dateParentTypeUpdate;

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
     * @return OParent
     */
    public function setDateUpdated($dateUpdated): OParent
    {
        $this->dateUpdated = $dateUpdated;

        return $this;
    }

    /**
     * @return string
     */
    public function getDateEnd()
    {
        return $this->dateEnd;
    }

    /**
     * @param \DateTime $dateEnd
     *
     * @return OParent
     */
    public function setDateEnd($dateEnd): OParent
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|Parent\Invoice[]
     */
    public function getInvoice()
    {
        return $this->invoice;
    }

    /**
     * @param Collections\ArrayCollection|Parent\Invoice[] $invoice
     *
     * @return OParent
     */
    public function setInvoice($invoice)
    {
        $this->invoice = $invoice;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|Parent\Doa[]
     */
    public function getDoa()
    {
        return $this->doa;
    }

    /**
     * @param Collections\ArrayCollection|Parent\Doa[] $doa
     *
     * @return OParent
     */
    public function setDoa($doa)
    {
        $this->doa = $doa;

        return $this;
    }
}
