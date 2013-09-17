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
 * OrganisationFinancial
 *
 * @ORM\Table(name="organisation_financial")
 * @ORM\Entity
 */
class Financial
{
    const VAT_STATUS_UNDEFINED = 0;
    const VAT_STATUS_VALID     = 1;
    const VAT_STATUS_INVALID   = 2;
    const VAT_STATUS_UNCHECKED = 3;

    const VAT_NOT_SHIFT = 0;
    const VAT_SHIFT     = 1;

    const NO_OMIT_CONTACT = 0;
    const OMIT_CONTACT    = 1;

    const NO_REQUIRED_PURCHASE_ORDER = 0;
    const REQUIRED_PURCHASE_ORDER    = 1;


    /**
     * Textual versions of the vat status
     *
     * @var array
     */
    protected $vatStatusTemplates = array(
        self::VAT_STATUS_UNDEFINED => 'txt-vat-status-undefined',
        self::VAT_STATUS_VALID     => 'txt-vat-status-valid',
        self::VAT_STATUS_INVALID   => 'txt-vat-status-invalid',
        self::VAT_STATUS_UNCHECKED => 'txt-vat-status-unchecked',
    );

    /**
     * Textual versions of the vat shift
     *
     * @var array
     */
    protected $vatShiftTemplates = array(
        self::VAT_NOT_SHIFT => 'txt-no-vat-shift',
        self::VAT_SHIFT     => 'txt-vat-shift',
    );

    /**
     * Textual versions of the vat shift
     *
     * @var array
     */
    protected $omitContactTemplates = array(
        self::NO_OMIT_CONTACT => 'txt-no-omit-contact',
        self::OMIT_CONTACT    => 'txt-omit-contact',
    );

    /**
     * Textual versions of the vat shift
     *
     * @var array
     */
    protected $requiredPurchaseOrderTemplates = array(
        self::NO_REQUIRED_PURCHASE_ORDER => 'txt-no-purchase-order-required',
        self::REQUIRED_PURCHASE_ORDER    => 'txt-purchase-order-required',
    );


    /**
     * @ORM\Column(name="financial_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @var integer
     */
    private $id;
    /**
     * @ORM\Column(name="vat", type="string", length=40, nullable=true)
     * @var string
     */
    private $vat;
    /**
     * @ORM\Column(name="date_vat", type="datetime", nullable=true)
     * @var \DateTime
     */
    private $dateVat;
    /**
     * @ORM\Column(name="vat_status", type="smallint", nullable=false)
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Attributes({"array":"vatStatusTemplates"})
     * @Annotation\Attributes({"label":"txt-vat-status"})
     * @var int
     */
    private $vatStatus;
    /**
     * @ORM\ManyToOne(targetEntity="Organisation\Entity\Organisation", inversedBy="financialDebtor", cascade={"persist"})
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="debtor", referencedColumnName="organisation_id", nullable=true)
     * })
     * @var \Organisation\Entity\Organisation
     */
    private $debtor;
    /**
     * @ORM\Column(name="shiftvat", type="smallint", nullable=false)
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Attributes({"array":"shiftVatTemplates"})
     * @Annotation\Attributes({"label":"txt-shift-vat"})
     * @var int
     */
    private $shiftVat;
    /**
     * @ORM\Column(name="omitcontact", type="smallint", nullable=false)
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Attributes({"array":"omitContactTemplates"})
     * @Annotation\Attributes({"label":"txt-omit-contact"})
     * @var int
     */
    private $omitContact;
    /**
     * @ORM\Column(name="iban", type="string", length=40, nullable=true)
     * @var string
     */
    private $iban;
    /**
     * @ORM\Column(name="bic", type="string", length=40, nullable=true)
     * @var string
     */
    private $bic;
    /**
     * @ORM\Column(name="required_purchase_order", type="smallint", nullable=false)
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Attributes({"array":"requiredPurchaseOrderTemplates"})
     * @Annotation\Attributes({"label":"txt-required-purchase-order"})
     * @var int
     */
    private $requiredPurchaseOrder;
    /**
     * @ORM\Column(name="email", type="boolean", nullable=false)
     * @var boolean
     */
    private $email;
    /**
     * @ORM\ManyToOne(targetEntity="Organisation\Entity\Organisation", inversedBy="financial", cascade="persist")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="organisation_id", referencedColumnName="organisation_id", nullable=false)
     * })
     * @var \Organisation\Entity\Organisation
     */
    private $organisation;
}
