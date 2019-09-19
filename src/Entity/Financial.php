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

use DateTime;
use Doctrine\Common\Collections;
use Doctrine\ORM\Mapping as ORM;
use General\Entity\VatType;
use Invoice\Entity\Reminder;
use Zend\Form\Annotation;

/**
 * @ORM\Table(name="organisation_financial")
 * @ORM\Entity(repositoryClass="Organisation\Repository\Financial")
 */
class Financial extends AbstractEntity
{
    public const VAT_STATUS_UNDEFINED = 0;
    public const VAT_STATUS_VALID = 1;
    public const VAT_STATUS_INVALID = 2;
    public const VAT_STATUS_UNCHECKED = 3;
    public const NO_OMIT_CONTACT = 0;
    public const OMIT_CONTACT = 1;
    public const NO_REQUIRED_PURCHASE_ORDER = 0;
    public const REQUIRED_PURCHASE_ORDER = 1;
    public const NO_EMAIL_DELIVERY = 0;
    public const EMAIL_DELIVERY = 1;

    protected static $vatStatusTemplates
        = [
            self::VAT_STATUS_UNDEFINED => 'txt-vat-status-undefined',
            self::VAT_STATUS_VALID     => 'txt-vat-status-valid',
            self::VAT_STATUS_INVALID   => 'txt-vat-status-invalid',
            self::VAT_STATUS_UNCHECKED => 'txt-vat-status-unchecked',
        ];

    protected static $omitContactTemplates
        = [
            self::NO_OMIT_CONTACT => 'txt-no-omit-contact',
            self::OMIT_CONTACT    => 'txt-omit-contact',
        ];

    protected static $emailTemplates
        = [
            self::EMAIL_DELIVERY    => 'txt-delivery-by-email',
            self::NO_EMAIL_DELIVERY => 'txt-delivery-by-postal-mail',
        ];

    protected static $requiredPurchaseOrderTemplates
        = [
            self::NO_REQUIRED_PURCHASE_ORDER => 'txt-no-purchase-order-required',
            self::REQUIRED_PURCHASE_ORDER    => 'txt-purchase-order-required',
        ];

    /**
     * @ORM\Column(name="financial_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Type("Zend\Form\Element\Hidden")
     *
     * @var int
     */
    private $id;
    /**
     * @ORM\Column(name="vat", type="string", nullable=true)
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Attributes({"label":"txt-vat-number", "help-block":"txt-vat-number-help-block"})
     * @var string
     */
    private $vat;
    /**
     * @ORM\Column(name="date_vat", type="datetime", nullable=true)
     * @Annotation\Exclude
     * @var DateTime
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
     * @ORM\Column(name="omitcontact", type="smallint", nullable=false)
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Attributes({"array":"omitContactTemplates"})
     * @Annotation\Attributes({"label":"txt-omit-contact"})
     *
     * @var int
     */
    private $omitContact;
    /**
     * @ORM\Column(name="iban", type="string", nullable=true)
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Attributes({"label":"txt-iban"})
     * @var string
     */
    private $iban;
    /**
     * @ORM\Column(name="supplier_number", type="string", nullable=true)
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Attributes({"label":"txt-supplier-number"})
     * @var string
     */
    private $supplierNumber;
    /**
     * @ORM\Column(name="bic", type="string", nullable=true)
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
     * @ORM\JoinColumn(name="organisation_id", referencedColumnName="organisation_id", nullable=false)
     *
     * @var Organisation
     */
    private $organisation;
    /**
     * @ORM\ManyToMany(targetEntity="General\Entity\VatType", cascade="persist", inversedBy="organisationFinancial")
     * @ORM\JoinTable(name="vat_type_financial",
     *            joinColumns={@ORM\JoinColumn(name="financial_id", referencedColumnName="financial_id")},
     *            inverseJoinColumns={@ORM\JoinColumn(name="type_id", referencedColumnName="type_id")}
     * )
     * @Annotation\Exclude()
     *
     * @var VatType[]|Collections\ArrayCollection
     */
    private $vatType;
    /**
     * @ORM\OneToMany(targetEntity="\Invoice\Entity\Reminder", cascade={"persist"}, mappedBy="financial")
     * @Annotation\Exclude()
     *
     * @var Reminder[]|Collections\ArrayCollection
     */
    private $reminder;

    public function __construct()
    {
        $this->vatStatus = self::VAT_STATUS_UNCHECKED;
        $this->omitContact = self::NO_OMIT_CONTACT;
        $this->requiredPurchaseOrder = self::NO_REQUIRED_PURCHASE_ORDER;
        $this->email = self::EMAIL_DELIVERY;
        $this->vatType = new Collections\ArrayCollection();
        $this->reminder = new Collections\ArrayCollection();
    }

    public static function getVatStatusTemplates(): array
    {
        return self::$vatStatusTemplates;
    }

    public static function getOmitContactTemplates(): array
    {
        return self::$omitContactTemplates;
    }

    public static function getEmailTemplates(): array
    {
        return self::$emailTemplates;
    }

    public static function getRequiredPurchaseOrderTemplates(): array
    {
        return self::$requiredPurchaseOrderTemplates;
    }

    public function hasOmitContact(): bool
    {
        return $this->omitContact === self::OMIT_CONTACT;
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

    public function getBic(): ?string
    {
        return $this->bic;
    }

    public function setBic($bic): Financial
    {
        $this->bic = $bic;

        return $this;
    }

    public function getDateVat(): ?DateTime
    {
        return $this->dateVat;
    }

    public function setDateVat($dateVat): Financial
    {
        $this->dateVat = $dateVat;

        return $this;
    }


    public function getEmail(bool $textual = false)
    {
        if ($textual) {
            return self::$emailTemplates[$this->email];
        }

        return $this->email;
    }

    public function setEmail($email): Financial
    {
        $this->email = $email;

        return $this;
    }

    public function getIban(): ?string
    {
        return $this->iban;
    }

    public function setIban($iban): Financial
    {
        $this->iban = $iban;

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): Financial
    {
        $this->id = $id;

        return $this;
    }

    public function getOmitContact(bool $textual = false)
    {
        if ($textual) {
            return self::$omitContactTemplates[$this->omitContact];
        }

        return $this->omitContact;
    }

    public function setOmitContact($omitContact): Financial
    {
        $this->omitContact = $omitContact;

        return $this;
    }

    public function getOrganisation(): ?Organisation
    {
        return $this->organisation;
    }

    public function setOrganisation($organisation): Financial
    {
        $this->organisation = $organisation;

        return $this;
    }

    public function getRequiredPurchaseOrder(bool $textual = false)
    {
        if ($textual) {
            return self::$requiredPurchaseOrderTemplates[$this->requiredPurchaseOrder];
        }

        return $this->requiredPurchaseOrder;
    }

    public function setRequiredPurchaseOrder($requiredPurchaseOrder): Financial
    {
        $this->requiredPurchaseOrder = $requiredPurchaseOrder;

        return $this;
    }

    public function getVat(): ?string
    {
        return $this->vat;
    }

    public function setVat($vat): Financial
    {
        $this->vat = $vat;

        return $this;
    }

    public function getVatStatus(bool $textual = false)
    {
        if ($textual) {
            return self::$vatStatusTemplates[$this->vatStatus];
        }

        return $this->vatStatus;
    }

    public function setVatStatus($vatStatus): Financial
    {
        $this->vatStatus = $vatStatus;

        return $this;
    }

    public function getVatType()
    {
        return $this->vatType;
    }

    public function setVatType($vatType): Financial
    {
        $this->vatType = $vatType;

        return $this;
    }

    public function getReminder()
    {
        return $this->reminder;
    }

    public function setReminder($reminder): Financial
    {
        $this->reminder = $reminder;

        return $this;
    }

    public function getSupplierNumber(): ?string
    {
        return $this->supplierNumber;
    }

    public function setSupplierNumber($supplierNumber): Financial
    {
        $this->supplierNumber = $supplierNumber;

        return $this;
    }
}
