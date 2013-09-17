<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * OrganisationFinancial
 *
 * @ORM\Table(name="organisation_financial")
 * @ORM\Entity
 */
class OrganisationFinancial
{
    /**
     * @var integer
     *
     * @ORM\Column(name="financial_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $financialId;

    /**
     * @var string
     *
     * @ORM\Column(name="vat", type="string", length=40, nullable=true)
     */
    private $vat;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_vat", type="datetime", nullable=true)
     */
    private $dateVat;

    /**
     * @var boolean
     *
     * @ORM\Column(name="vat_status", type="boolean", nullable=false)
     */
    private $vatStatus;

    /**
     * @var integer
     *
     * @ORM\Column(name="debtor", type="integer", nullable=true)
     */
    private $debtor;

    /**
     * @var boolean
     *
     * @ORM\Column(name="shiftvat", type="boolean", nullable=false)
     */
    private $shiftvat;

    /**
     * @var boolean
     *
     * @ORM\Column(name="omitcontact", type="boolean", nullable=false)
     */
    private $omitcontact;

    /**
     * @var string
     *
     * @ORM\Column(name="iban", type="string", length=40, nullable=true)
     */
    private $iban;

    /**
     * @var string
     *
     * @ORM\Column(name="bic", type="string", length=40, nullable=true)
     */
    private $bic;

    /**
     * @var boolean
     *
     * @ORM\Column(name="required_purchase_order", type="boolean", nullable=false)
     */
    private $requiredPurchaseOrder;

    /**
     * @var boolean
     *
     * @ORM\Column(name="email", type="boolean", nullable=false)
     */
    private $email;

    /**
     * @var \Organisation
     *
     * @ORM\ManyToOne(targetEntity="Organisation")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="organisation_id", referencedColumnName="organisation_id")
     * })
     */
    private $organisation;


}
