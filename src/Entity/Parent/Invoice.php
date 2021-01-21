<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Organisation\Entity\Parent;

use Doctrine\ORM\Mapping as ORM;
use Laminas\Form\Annotation;
use Organisation\Entity\AbstractEntity;
use Organisation\Entity\ParentEntity;
use Program\Entity\Program;

/**
 * @ORM\Table(name="organisation_parent_invoice")
 * @ORM\Entity
 * @Annotation\Hydrator("Laminas\Hydrator\ObjectPropertyHydrator")
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
     * @var int
     */
    private int $id;
    /**
     * @ORM\Column(name="year", type="integer", options={"unsigned":true})
     *
     * @var int
     */
    private int $year;
    /**
     * @ORM\Column(name="amount_invoiced", type="decimal", precision=10, scale=2, nullable=true)
     *
     * @var float|string
     */
    private $amountInvoiced;
    /**
     * @ORM\ManyToOne(targetEntity="Organisation\Entity\ParentEntity", inversedBy="invoice", cascade={"persist"})
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="parent_id", nullable=false)
     *
     * @var ParentEntity
     */
    private ParentEntity $parent;
    /**
     * @ORM\OneToOne(targetEntity="Invoice\Entity\Invoice", inversedBy="parentInvoice", cascade={"persist"})
     * @ORM\JoinColumn(name="invoice_id", referencedColumnName="invoice_id", nullable=false)
     * @var \Invoice\Entity\Invoice
     */
    private \Invoice\Entity\Invoice $invoice;
    /**
     * @ORM\ManyToOne(targetEntity="Program\Entity\Program", inversedBy="parentInvoice", cascade={"persist"})
     * @ORM\JoinColumn(name="program_id", referencedColumnName="program_id", nullable=false)
     * @var Program
     */
    private Program $program;

    public function __toString(): string
    {
        return (string)$this->invoice->getInvoiceNr();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): Invoice
    {
        $this->id = $id;

        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(int $year): Invoice
    {
        $this->year = $year;

        return $this;
    }

    public function getAmountInvoiced()
    {
        return (float)$this->amountInvoiced;
    }

    public function setAmountInvoiced($amountInvoiced): Invoice
    {
        $this->amountInvoiced = $amountInvoiced;

        return $this;
    }

    public function getParent(): ParentEntity
    {
        return $this->parent;
    }

    public function setParent(ParentEntity $parent): Invoice
    {
        $this->parent = $parent;

        return $this;
    }

    public function getInvoice(): \Invoice\Entity\Invoice
    {
        return $this->invoice;
    }

    public function setInvoice(\Invoice\Entity\Invoice $invoice): Invoice
    {
        $this->invoice = $invoice;

        return $this;
    }

    public function getProgram(): Program
    {
        return $this->program;
    }

    public function setProgram(Program $program): Invoice
    {
        $this->program = $program;

        return $this;
    }
}
