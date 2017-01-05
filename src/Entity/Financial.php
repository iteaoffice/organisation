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
use Zend\Form\Annotation;

/**
 * OrganisationFinancial.
 *
 * @ORM\Table(name="organisation_financial")
 * @ORM\Entity(repositoryClass="Organisation\Repository\Financial")
 */
class Financial extends AbstractEntity
{
    const VAT_STATUS_UNDEFINED = 0;
    const VAT_STATUS_VALID = 1;
    const VAT_STATUS_INVALID = 2;
    const VAT_STATUS_UNCHECKED = 3;
    const NO_OMIT_CONTACT = 0;
    const OMIT_CONTACT = 1;
    const NO_REQUIRED_PURCHASE_ORDER = 0;
    const REQUIRED_PURCHASE_ORDER = 1;
    const NO_EMAIL_DELIVERY = 0;
    const EMAIL_DELIVERY = 1;
    /**
     * Textual versions of the vat status.
     *
     * @var array
     */
    protected static $vatStatusTemplates
        = [
            self::VAT_STATUS_UNDEFINED => 'txt-vat-status-undefined',
            self::VAT_STATUS_VALID     => 'txt-vat-status-valid',
            self::VAT_STATUS_INVALID   => 'txt-vat-status-invalid',
            self::VAT_STATUS_UNCHECKED => 'txt-vat-status-unchecked',
        ];
    /**
     * Textual versions of the vat shift.
     *
     * @var array
     */
    protected static $omitContactTemplates
        = [
            self::NO_OMIT_CONTACT => 'txt-no-omit-contact',
            self::OMIT_CONTACT    => 'txt-omit-contact',
        ];
    /**
     * Textual versions of the email templates.
     *
     * @var array
     */
    protected static $emailTemplates
        = [
            self::EMAIL_DELIVERY    => 'txt-delivery-by-email',
            self::NO_EMAIL_DELIVERY => 'txt-delivery-by-postal-mail',
        ];
    /**
     * Textual versions of the vat shift.
     *
     * @var array
     */
    protected static $requiredPurchaseOrderTemplates
        = [
            self::NO_REQUIRED_PURCHASE_ORDER => 'txt-no-purchase-order-required',
            self::REQUIRED_PURCHASE_ORDER    => 'txt-purchase-order-required',
        ];

    /**
     * @ORM\Column(name="financial_id", length=10, type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Type("Zend\Form\Element\Hidden")
     *
     * @var integer
     */
    private $id;
    /**
     * @ORM\Column(name="vat", type="string", length=40, nullable=true)
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Attributes({"label":"txt-vat-number", "help-block":"txt-vat-number-help-block"})
     * @var string
     */
    private $vat;
    /**
     * @ORM\Column(name="date_vat", type="datetime", nullable=true)
     * @Annotation\Exclude
     * @var \DateTime
     */
    private $dateVat;
    /**
     * @ORM\Column(name="vat_status", type="smallint", nullable=false)
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Attributes({"array":"vatStatusTemplates"})
     * @Annotation\Attributes({"label":"txt-vat-status"})
     *
     * @var int
     */
    private $vatStatus;
    /**
     * @ORM\ManyToOne(targetEntity="Organisation\Entity\Organisation", inversedBy="financialDebtor", cascade={"persist"})
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="debtor", referencedColumnName="organisation_id", nullable=true)
     * })
     *
     * @var \Organisation\Entity\Organisation
     */
    private $debtor;
    /**
     * @ORM\Column(name="shiftvat", type="smallint", nullable=false)
     * @Annotation\Exclude
     *
     * @deprecated
     *
     * @var int
     */
    private $shiftVat;
    /**
     * @ORM\Column(name="omitcontact", type="smallint", nullable=false)
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Attributes({"array":"omitContactTemplates"})
     * @Annotation\Attributes({"label":"txt-omit-contact"})
     *
     * @var int
     */
    private $omitContact;
    /**
     * @ORM\Column(name="iban", type="string", length=40, nullable=true)
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Attributes({"label":"txt-iban"})
     * @var string
     */
    private $iban;
    /**
     * @ORM\Column(name="supplier_number", type="string", length=40, nullable=true)
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Attributes({"label":"txt-supplier-number"})
     * @var string
     */
    private $supplierNumber;
    /**
     * @ORM\Column(name="bic", type="string", length=40, nullable=true)
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Attributes({"label":"txt-bic"})
     *
     * @var string
     */
    private $bic;
    /**
     * @ORM\Column(name="required_purchase_order", type="smallint", nullable=false)
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Attributes({"array":"requiredPurchaseOrderTemplates"})
     * @Annotation\Attributes({"label":"txt-required-purchase-order"})
     *
     * @var int
     */
    private $requiredPurchaseOrder;
    /**
     * @ORM\Column(name="email", type="smallint", nullable=false)
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Attributes({"array":"emailTemplates"})
     * @Annotation\Attributes({"label":"txt-delivery-by-email-order"})
     *
     * @var int
     */
    private $email;
    /**
     * @ORM\OneToOne(targetEntity="Organisation\Entity\Organisation", inversedBy="financial", cascade="persist")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="organisation_id", referencedColumnName="organisation_id", nullable=false)
     * })
     *
     * @var \Organisation\Entity\Organisation
     */
    private $organisation;
    /**
     * @ORM\OneToMany(targetEntity="\Invoice\Entity\Financial\Row", cascade={"persist"}, mappedBy="financial")
     * @Annotation\Exclude()
     *
     * @var \Invoice\Entity\Financial\Row[]
     */
    private $financialRow;
    /**
     * @ORM\ManyToMany(targetEntity="General\Entity\VatType", cascade="persist", inversedBy="organisationFinancial")
     * @ORM\JoinTable(name="vat_type_financial",
     *            joinColumns={@ORM\JoinColumn(name="financial_id", referencedColumnName="financial_id")},
     *            inverseJoinColumns={@ORM\JoinColumn(name="type_id", referencedColumnName="type_id")}
     * )
     * @Annotation\Exclude()
     *
     * @var \General\Entity\VatType[]|Collections\ArrayCollection
     */
    private $vatType;
    /**
     * @ORM\OneToMany(targetEntity="\Invoice\Entity\Reminder", cascade={"persist"}, mappedBy="financial")
     * @Annotation\Exclude()
     *
     * @var \Invoice\Entity\Reminder[]|Collections\ArrayCollection
     */
    private $reminder;

    /**
     *
     */
    public function __construct()
    {
        $this->vatStatus             = self::VAT_STATUS_UNCHECKED;
        $this->omitContact           = self::NO_OMIT_CONTACT;
        $this->requiredPurchaseOrder = self::NO_REQUIRED_PURCHASE_ORDER;
        $this->email                 = self::EMAIL_DELIVERY;
        $this->vatType               = new Collections\ArrayCollection();
        $this->reminder              = new Collections\ArrayCollection();
    }

    /**
     * @return array
     */
    public static function getVatStatusTemplates(): array
    {
        return self::$vatStatusTemplates;
    }

    /**
     * @return array
     */
    public static function getOmitContactTemplates(): array
    {
        return self::$omitContactTemplates;
    }

    /**
     * @return array
     */
    public static function getEmailTemplates(): array
    {
        return self::$emailTemplates;
    }

    /**
     * @return array
     */
    public static function getRequiredPurchaseOrderTemplates(): array
    {
        return self::$requiredPurchaseOrderTemplates;
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
     * @return string
     */
    public function getBic()
    {
        return $this->bic;
    }

    /**
     * @param string $bic
     */
    public function setBic($bic)
    {
        $this->bic = $bic;
    }

    /**
     * @return \DateTime
     */
    public function getDateVat()
    {
        return $this->dateVat;
    }

    /**
     * @param \DateTime $dateVat
     */
    public function setDateVat($dateVat)
    {
        $this->dateVat = $dateVat;
    }

    /**
     * @return \Organisation\Entity\Organisation
     */
    public function getDebtor()
    {
        return $this->debtor;
    }

    /**
     * @param \Organisation\Entity\Organisation $debtor
     */
    public function setDebtor($debtor)
    {
        $this->debtor = $debtor;
    }

    /**
     * @param bool $textual
     *
     * @return int|string
     */
    public function getEmail($textual = false)
    {
        if ($textual) {
            return self::$emailTemplates[$this->email];
        }

        return $this->email;
    }

    /**
     * @param int $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return \Invoice\Entity\Financial\Row[]
     */
    public function getFinancialRow()
    {
        return $this->financialRow;
    }

    /**
     * @param \Invoice\Entity\Financial\Row[] $financialRow
     */
    public function setFinancialRow($financialRow)
    {
        $this->financialRow = $financialRow;
    }

    /**
     * @return string
     */
    public function getIban()
    {
        return $this->iban;
    }

    /**
     * @param string $iban
     */
    public function setIban($iban)
    {
        $this->iban = $iban;
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
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param  bool $textual
     *
     * @return int|string
     */
    public function getOmitContact($textual = false)
    {
        if ($textual) {
            return self::$omitContactTemplates[$this->omitContact];
        }

        return $this->omitContact;
    }

    /**
     * @param int $omitContact
     */
    public function setOmitContact($omitContact)
    {
        $this->omitContact = $omitContact;
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
     */
    public function setOrganisation($organisation)
    {
        $this->organisation = $organisation;
    }

    /**
     * @param  bool $textual
     *
     * @return int|string
     */
    public function getRequiredPurchaseOrder($textual = false)
    {
        if ($textual) {
            return self::$requiredPurchaseOrderTemplates[$this->requiredPurchaseOrder];
        }

        return $this->requiredPurchaseOrder;
    }

    /**
     * @param int $requiredPurchaseOrder
     */
    public function setRequiredPurchaseOrder($requiredPurchaseOrder)
    {
        $this->requiredPurchaseOrder = $requiredPurchaseOrder;
    }

    /**
     * @return int
     */
    public function getShiftVat()
    {
        return $this->shiftVat;
    }

    /**
     * @param int $shiftVat
     */
    public function setShiftVat($shiftVat)
    {
        $this->shiftVat = $shiftVat;
    }

    /**
     * @return string
     */
    public function getVat()
    {
        return $this->vat;
    }

    /**
     * @param string $vat
     */
    public function setVat($vat)
    {
        $this->vat = $vat;
    }

    /**
     * @param  bool $textual
     *
     * @return int|string
     */
    public function getVatStatus($textual = false)
    {
        if ($textual) {
            return self::$vatStatusTemplates[$this->vatStatus];
        }

        return $this->vatStatus;
    }

    /**
     * @param int $vatStatus
     */
    public function setVatStatus($vatStatus)
    {
        $this->vatStatus = $vatStatus;
    }

    /**
     * @return Collections\ArrayCollection|\General\Entity\VatType[]
     */
    public function getVatType()
    {
        return $this->vatType;
    }

    /**
     * @param Collections\ArrayCollection|\General\Entity\VatType[] $vatType
     */
    public function setVatType($vatType)
    {
        $this->vatType = $vatType;
    }

    /**
     * @return \Invoice\Entity\Reminder[]|Collections\ArrayCollection
     */
    public function getReminder()
    {
        return $this->reminder;
    }

    /**
     * @param \Invoice\Entity\Reminder[]|Collections\ArrayCollection $reminder
     */
    public function setReminder($reminder)
    {
        $this->reminder = $reminder;
    }

    /**
     * @return string
     */
    public function getSupplierNumber()
    {
        return $this->supplierNumber;
    }

    /**
     * @param string $supplierNumber
     *
     * @return Financial
     */
    public function setSupplierNumber($supplierNumber)
    {
        $this->supplierNumber = $supplierNumber;

        return $this;
    }
}
