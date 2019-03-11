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

namespace Organisation\Entity\Parent;

use Doctrine\ORM\Mapping as ORM;
use Organisation\Entity\AbstractEntity;
use Zend\Form\Annotation;

/**
 * Entity for the Partner.
 *
 * @ORM\Table(name="organisation_parent_invoice")
 * @ORM\Entity
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("organisation_parent_invoice")
 *
 */
class Invoice extends AbstractEntity
{
    /**
     * @ORM\Column(name="partner_invoice_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @var integer
     */
    private $id;
    /**
     * @ORM\Column(name="year", type="integer", options={"unsigned":true})
     *
     * @var integer
     */
    private $year;
    /**
     * @ORM\Column(name="amount_invoiced", type="decimal", precision=10, scale=2, nullable=true)
     *
     * @var float
     */
    private $amountInvoiced;
    /**
     * @ORM\ManyToOne(targetEntity="Organisation\Entity\OParent", inversedBy="invoice", cascade={"persist"})
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="parent_id", nullable=false)
     *
     * @var \Organisation\Entity\OParent
     */
    private $parent;
    /**
     * @ORM\OneToOne(targetEntity="Invoice\Entity\Invoice", inversedBy="parentInvoice", cascade={"persist"})
     * @ORM\JoinColumn(name="invoice_id", referencedColumnName="invoice_id", nullable=false)
     * @var \Invoice\Entity\Invoice
     */
    private $invoice;
    /**
     * @ORM\ManyToOne(targetEntity="Program\Entity\Program", inversedBy="parentInvoice", cascade={"persist"})
     * @ORM\JoinColumn(name="program_id", referencedColumnName="program_id", nullable=false)
     * @var \Program\Entity\Program
     */
    private $program;

    /**
     * Class constructor.
     */
    public function __construct()
    {
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
        return (string)$this->invoice->getInvoiceNr();
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
     * @return Invoice
     */
    public function setId($id): Invoice
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int
     */
    public function getYear(): ?int
    {
        return $this->year;
    }

    /**
     * @param int $year
     *
     * @return Invoice
     */
    public function setYear(int $year): Invoice
    {
        $this->year = $year;

        return $this;
    }

    /**
     * @return float
     */
    public function getAmountInvoiced(): ?float
    {
        return (float) $this->amountInvoiced;
    }

    /**
     * @param float $amountInvoiced
     *
     * @return Invoice
     */
    public function setAmountInvoiced(float $amountInvoiced): Invoice
    {
        $this->amountInvoiced = $amountInvoiced;

        return $this;
    }

    /**
     * @return \Organisation\Entity\OParent
     */
    public function getParent(): \Organisation\Entity\OParent
    {
        return $this->parent;
    }

    /**
     * @param \Organisation\Entity\OParent $parent
     *
     * @return Invoice
     */
    public function setParent(\Organisation\Entity\OParent $parent): Invoice
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return \Invoice\Entity\Invoice
     */
    public function getInvoice(): \Invoice\Entity\Invoice
    {
        return $this->invoice;
    }

    /**
     * @param \Invoice\Entity\Invoice $invoice
     *
     * @return Invoice
     */
    public function setInvoice(\Invoice\Entity\Invoice $invoice): Invoice
    {
        $this->invoice = $invoice;

        return $this;
    }

    /**
     * @return \Program\Entity\Program
     */
    public function getProgram(): \Program\Entity\Program
    {
        return $this->program;
    }

    /**
     * @param \Program\Entity\Program $program
     *
     * @return Invoice
     */
    public function setProgram(\Program\Entity\Program $program): Invoice
    {
        $this->program = $program;

        return $this;
    }
}
