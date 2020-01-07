<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Organisation\Entity;

use Doctrine\Common\Collections;
use Doctrine\ORM\Mapping as ORM;
use Event\Entity\Meeting\Cost;
use Laminas\Form\Annotation;

/**
 * @ORM\Table(name="organisation_type")
 * @ORM\Entity(repositoryClass="Organisation\Repository\Type")
 * @Annotation\Hydrator("Laminas\Hydrator\ObjectProperty")
 * @Annotation\Name("organisation_type")
 */
class Type extends AbstractEntity
{
    public const NO_INVOICE = 0;
    public const INVOICE = 1;

    public const TYPE_IFC = 1;
    public const TYPE_LARGE_INDUSTRY = 2;
    public const TYPE_SME = 3;
    public const TYPE_RESEARCH = 4;
    public const TYPE_UNIVERSITY = 5;
    public const TYPE_GOVERNMENT = 6;
    public const TYPE_OTHER = 7;
    public const TYPE_UNKNOWN = 8;

    protected static array $invoiceTemplates
        = [
            self::NO_INVOICE => 'txt-invoice',
            self::INVOICE => 'txt-no-invoice',
        ];
    /**
     * @ORM\Column(name="type_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Type("\Laminas\Form\Element\Hidden")
     *
     * @var int
     */
    private $id;
    /**
     * @ORM\Column(name="type", type="string", nullable=false, unique=true)
     * @Annotation\Type("\Laminas\Form\Element\Text")
     * @Annotation\Options({"label":"txt-type"})
     * @Annotation\Required(true)
     *
     * @var string
     */
    private $type;
    /**
     * @ORM\Column(name="description", type="string", nullable=false, unique=true)
     * @Annotation\Type("\Laminas\Form\Element\Text")
     * @Annotation\Options({"label":"txt-type"})
     * @Annotation\Required(true)
     *
     * @var string
     */
    private $description;
    /**
     * @ORM\Column(type="smallint",nullable=true)
     * @Annotation\Type("Laminas\Form\Element\Radio")
     * @Annotation\Attributes({"array":"invoiceTemplates"})
     * @Annotation\Attributes({"label":"txt-invoice"})
     *
     * @var int
     */
    private $invoice;
    /**
     * @ORM\OneToMany(targetEntity="Event\Entity\Meeting\Cost", cascade={"persist"}, mappedBy="type")
     * @Annotation\Exclude()
     *
     * @var Cost[]|Collections\ArrayCollection()
     */
    private $meetingCost;
    /**
     * @ORM\OneToMany(targetEntity="Organisation\Entity\Organisation", cascade={"persist"}, mappedBy="type")
     * @Annotation\Exclude()
     *
     * @var Organisation[]|Collections\ArrayCollection
     */
    private $organisation;
    /**
     * @ORM\OneToMany(targetEntity="Organisation\Entity\Update", cascade={"persist"}, mappedBy="type")
     * @Annotation\Exclude()
     *
     * @var Update[]|Collections\Collection
     */
    private $organisationUpdates;

    public function __construct()
    {
        $this->organisation = new Collections\ArrayCollection();
        $this->meetingCost = new Collections\ArrayCollection();
        $this->organisationUpdates = new Collections\ArrayCollection();
    }

    public static function getInvoiceTemplates(): array
    {
        return self::$invoiceTemplates;
    }

    public function __toString(): string
    {
        return (string)$this->description;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): Type
    {
        $this->id = $id;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType($type): Type
    {
        $this->type = $type;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription($description): Type
    {
        $this->description = $description;

        return $this;
    }

    public function getInvoice(bool $textual = false)
    {
        if ($textual) {
            return self::$invoiceTemplates[$this->invoice];
        }

        return $this->invoice;
    }

    public function setInvoice($invoice): Type
    {
        $this->invoice = $invoice;

        return $this;
    }

    public function getMeetingCost()
    {
        return $this->meetingCost;
    }

    public function setMeetingCost($meetingCost): Type
    {
        $this->meetingCost = $meetingCost;

        return $this;
    }

    public function getOrganisation()
    {
        return $this->organisation;
    }

    public function setOrganisation($organisation): Type
    {
        $this->organisation = $organisation;

        return $this;
    }

    public function getOrganisationUpdates(): Collections\Collection
    {
        return $this->organisationUpdates;
    }

    public function setOrganisationUpdates(Collections\Collection $organisationUpdates): Type
    {
        $this->organisationUpdates = $organisationUpdates;
        return $this;
    }
}
