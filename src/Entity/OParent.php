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

namespace Organisation\Entity;

use Contact\Entity\Contact;
use Doctrine\Common\Collections;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
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
    public const MEMBER_TYPE_NO_MEMBER = 1;
    public const MEMBER_TYPE_MEMBER = 2;
    public const MEMBER_TYPE_APPLICANT = 3;

    public const EPOSS_MEMBER_TYPE_NO_MEMBER = 1;
    public const EPOSS_MEMBER_TYPE_MEMBER = 2;
    public const EPOSS_MEMBER_TYPE_DOA_SIGNER = 3;

    public const ARTEMISIA_MEMBER_TYPE_NO_MEMBER = 1;
    public const ARTEMISIA_MEMBER_TYPE_MEMBER = 2;
    public const ARTEMISIA_MEMBER_TYPE_DOA_SIGNER = 3;

    //Create a set of criteria as dedicated constants as they don't fit in the normal type/status tables
    public const CRITERION_C_CHAMBER = 1;
    public const CRITERION_FREE_RIDER = 2;

    protected static $memberTypeTemplates
        = [
            self::MEMBER_TYPE_NO_MEMBER => 'txt-no-member',
            self::MEMBER_TYPE_MEMBER    => 'txt-member',
            self::MEMBER_TYPE_APPLICANT => 'txt-applicant-member',
        ];

    protected static $epossMemberTypeTemplates
        = [
            self::EPOSS_MEMBER_TYPE_NO_MEMBER  => 'txt-not-eposs-member',
            self::EPOSS_MEMBER_TYPE_MEMBER     => 'txt-eposs-member',
            self::EPOSS_MEMBER_TYPE_DOA_SIGNER => 'txt-eposs-doa-signer',
        ];

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
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id", nullable=false)
     * @Annotation\Type("Contact\Form\Element\Contact")
     * @Annotation\Attributes({"label":"txt-parent-contact-label"})
     * @Annotation\Options({"help-block":"txt-parent-contact-help-block"})
     *
     * @var Contact
     */
    private $contact;
    /**
     * @ORM\ManyToOne(targetEntity="Organisation\Entity\Parent\Type", inversedBy="parent", cascade={"persist"})
     * @ORM\JoinColumn(name="type_id", referencedColumnName="type_id", nullable=false)
     * @Annotation\Type("DoctrineORMModule\Form\Element\EntitySelect")
     * @Annotation\Options({
     *      "help-block":"txt-parent-type-help-block",
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
     * @Annotation\Attributes({"label":"txt-parent-type-label"})
     *
     * @var \Organisation\Entity\Parent\Type
     */
    private $type;
    /**
     * @ORM\Column(name="member_type", type="smallint", nullable=false)
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Attributes({"array":"memberTypeTemplates"})
     * @Annotation\Attributes({"label":"txt-member-type"})
     * @Annotation\Options({"help-block":"txt-member-type-help-block"})
     *
     * @var int
     */
    private $memberType;
    /**
     * @ORM\Column(name="eposs_member_type", type="smallint", nullable=false)
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Attributes({"array":"epossMemberTypeTemplates"})
     * @Annotation\Attributes({"label":"txt-eposs-member-type"})
     * @Annotation\Options({"help-block":"txt-is-eposs-member-type-help-block"})
     *
     * @var int
     */
    private $epossMemberType;
    /**
     * @ORM\Column(name="artemisia_member_type", type="smallint", nullable=false)
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Attributes({"array":"artemisiaMemberTypeTemplates"})
     * @Annotation\Attributes({"label":"txt-artemisia-member-type"})
     * @Annotation\Options({"help-block":"txt-is-artemisia-member-type-help-block"})
     *
     * @var int
     */
    private $artemisiaMemberType;
    /**
     * @ORM\OneToMany(targetEntity="Organisation\Entity\Parent\Financial", cascade={"persist","remove"}, mappedBy="parent")
     * @Annotation\Exclude()
     *
     * @var \Organisation\Entity\Parent\Financial[]|Collections\ArrayCollection
     */
    private $financial;
    /**
     * @ORM\OneToOne(targetEntity="Organisation\Entity\Organisation", inversedBy="parent", cascade={"persist"})
     * @ORM\JoinColumn(name="organisation_id", referencedColumnName="organisation_id", nullable=false)
     * @Annotation\Type("Organisation\Form\Element\Organisation")
     * @Annotation\Attributes({"label":"txt-parent-organisation-label"})
     * @Annotation\Options({"help-block":"txt-parent-organisation-help-block"})
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
     * @Annotation\Attributes({"label":"txt-date-parent-type-update-label"})
     * @Annotation\Options({"help-block":"txt-date-parent-type-update-help-block"})
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
     * @Annotation\Options({"label":"txt-parent-date-end-label"})
     * @Annotation\Options({"help-block":"txt-parent-date-end-help-block"})
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
     * @ORM\OneToMany(targetEntity="Organisation\Entity\Parent\InvoiceExtra", cascade={"persist"}, mappedBy="parent")
     * @Annotation\Exclude()
     *
     * @var \Organisation\Entity\Parent\InvoiceExtra[]|Collections\ArrayCollection()
     */
    private $invoiceExtra;
    /**
     * @ORM\OneToMany(targetEntity="Organisation\Entity\Parent\Doa", cascade={"persist"}, mappedBy="parent")
     * @Annotation\Exclude()
     *
     * @var \Organisation\Entity\Parent\Doa[]|Collections\ArrayCollection()
     */
    private $doa;

    public function __construct()
    {
        $this->invoice = new Collections\ArrayCollection();
        $this->financial = new Collections\ArrayCollection();
        $this->invoiceExtra = new Collections\ArrayCollection();
        $this->parentOrganisation = new Collections\ArrayCollection();
        $this->doa = new Collections\ArrayCollection();
        $this->memberType = self::MEMBER_TYPE_NO_MEMBER;
        $this->epossMemberType = self::EPOSS_MEMBER_TYPE_NO_MEMBER;
        $this->artemisiaMemberType = self::ARTEMISIA_MEMBER_TYPE_NO_MEMBER;
    }

    public static function getMemberTypeTemplates(): array
    {
        return self::$memberTypeTemplates;
    }

    public static function getEpossMemberTypeTemplates(): array
    {
        return self::$epossMemberTypeTemplates;
    }

    public static function getArtemisiaMemberTypeTemplates(): array
    {
        return self::$artemisiaMemberTypeTemplates;
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

    public function isMember(): bool
    {
        return $this->memberType === self::MEMBER_TYPE_MEMBER;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): OParent
    {
        $this->id = $id;

        return $this;
    }

    public function getParentOrganisation()
    {
        return $this->parentOrganisation;
    }

    public function setParentOrganisation($parentOrganisation): OParent
    {
        $this->parentOrganisation = $parentOrganisation;

        return $this;
    }

    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    public function setContact($contact): OParent
    {
        $this->contact = $contact;

        return $this;
    }

    public function getType(): ?parent\Type
    {
        return $this->type;
    }

    public function setType($type): OParent
    {
        $this->type = $type;

        return $this;
    }

    public function getMemberType(bool $textual = false)
    {
        if ($textual) {
            return self::$memberTypeTemplates[$this->memberType];
        }

        return $this->memberType;
    }

    public function setMemberType($memberType): OParent
    {
        $this->memberType = $memberType;

        return $this;
    }

    public function getEpossMemberType(bool $textual = false)
    {
        if ($textual) {
            return self::$epossMemberTypeTemplates[$this->epossMemberType];
        }

        return $this->epossMemberType;
    }

    public function setEpossMemberType($epossMemberType): OParent
    {
        $this->epossMemberType = $epossMemberType;

        return $this;
    }

    public function getArtemisiaMemberType(bool $textual = false)
    {
        if ($textual) {
            return self::$artemisiaMemberTypeTemplates[$this->artemisiaMemberType];
        }

        return $this->artemisiaMemberType;
    }

    public function setArtemisiaMemberType($artemisiaMemberType): OParent
    {
        $this->artemisiaMemberType = $artemisiaMemberType;

        return $this;
    }

    public function getFinancial()
    {
        return $this->financial;
    }

    public function setFinancial($financial): OParent
    {
        $this->financial = $financial;

        return $this;
    }

    public function getOrganisation(): ?Organisation
    {
        return $this->organisation;
    }

    public function setOrganisation(Organisation $organisation): OParent
    {
        $this->organisation = $organisation;

        return $this;
    }

    public function getDateCreated(): ?\DateTime
    {
        return $this->dateCreated;
    }

    public function setDateCreated(\DateTime $dateCreated): OParent
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    public function getDateParentTypeUpdate(): ?\DateTime
    {
        return $this->dateParentTypeUpdate;
    }

    public function setDateParentTypeUpdate($dateParentTypeUpdate): OParent
    {
        $this->dateParentTypeUpdate = $dateParentTypeUpdate;

        return $this;
    }

    public function getDateUpdated(): ?\DateTime
    {
        return $this->dateUpdated;
    }

    public function setDateUpdated($dateUpdated): OParent
    {
        $this->dateUpdated = $dateUpdated;

        return $this;
    }

    public function getDateEnd(): ?\DateTime
    {
        return $this->dateEnd;
    }

    public function setDateEnd($dateEnd): OParent
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    public function getInvoice()
    {
        return $this->invoice;
    }

    public function setInvoice($invoice): OParent
    {
        $this->invoice = $invoice;

        return $this;
    }

    public function getInvoiceExtra()
    {
        return $this->invoiceExtra;
    }

    public function setInvoiceExtra($invoiceExtra): OParent
    {
        $this->invoiceExtra = $invoiceExtra;

        return $this;
    }

    public function getDoa()
    {
        return $this->doa;
    }

    public function setDoa($doa): OParent
    {
        $this->doa = $doa;

        return $this;
    }
}
