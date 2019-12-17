<?php
/**
*
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        https://github.com/iteaoffice/organisation for the canonical source repository
 */

declare(strict_types=1);

namespace Organisation\Entity\Parent;

use Doctrine\ORM\Mapping as ORM;
use Organisation\Entity\AbstractEntity;
use Organisation\Entity\OParent;
use Program\Entity\Program;
use Zend\Form\Annotation;

/**
 * @ORM\Table(name="organisation_parent_invoice_extra")
 * @ORM\Entity
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("organisation_parent_invoice_extra")
 */
class InvoiceExtra extends AbstractEntity
{
    /**
     * @ORM\Column(name="parent_extra_invoice_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @var int
     */
    private $id;
    /**
     * @ORM\Column(name="year", type="integer", options={"unsigned":true})
     *
     * @var int
     */
    private $year;
    /**
     * @ORM\Column(name="amount_invoiced", type="decimal", precision=10, scale=2, nullable=true)
     *
     * @var float
     */
    private $amountInvoiced;
    /**
     * @ORM\ManyToOne(targetEntity="Organisation\Entity\OParent", inversedBy="invoiceExtra", cascade={"persist"})
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="parent_id", nullable=false)
     *
     * @var OParent
     */
    private $parent;
    /**
     * @ORM\OneToOne(targetEntity="Invoice\Entity\Invoice", inversedBy="parentInvoiceExtra", cascade={"persist"})
     * @ORM\JoinColumn(name="invoice_id", referencedColumnName="invoice_id", nullable=false)
     *
     * @var \Invoice\Entity\Invoice
     */
    private $invoice;
    /**
     * @ORM\ManyToOne(targetEntity="Program\Entity\Program", inversedBy="parentInvoiceExtra", cascade={"persist"})
     * @ORM\JoinColumn(name="program_id", referencedColumnName="program_id", nullable=false)
     *
     * @var Program
     */
    private $program;

    public function __toString(): string
    {
        return (string)$this->getInvoice()->getInvoiceNr();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): InvoiceExtra
    {
        $this->id = $id;
        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(?int $year): InvoiceExtra
    {
        $this->year = $year;
        return $this;
    }

    public function getAmountInvoiced(): ?float
    {
        return $this->amountInvoiced;
    }

    public function setAmountInvoiced(?float $amountInvoiced): InvoiceExtra
    {
        $this->amountInvoiced = $amountInvoiced;
        return $this;
    }

    public function getParent(): ?OParent
    {
        return $this->parent;
    }

    public function setParent(?OParent $parent): InvoiceExtra
    {
        $this->parent = $parent;
        return $this;
    }

    public function getInvoice(): ?\Invoice\Entity\Invoice
    {
        return $this->invoice;
    }

    public function setInvoice(?\Invoice\Entity\Invoice $invoice): InvoiceExtra
    {
        $this->invoice = $invoice;
        return $this;
    }

    public function getProgram(): ?Program
    {
        return $this->program;
    }

    public function setProgram(?Program $program): InvoiceExtra
    {
        $this->program = $program;
        return $this;
    }
}
