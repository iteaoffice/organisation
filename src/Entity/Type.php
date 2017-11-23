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

use Doctrine\Common\Collections;
use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use Zend\Permissions\Acl\Resource\ResourceInterface;

/**
 * Type.
 *
 * @ORM\Table(name="organisation_type")
 * @ORM\Entity(repositoryClass="Organisation\Repository\Type")
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("organisation_type")
 */
class Type extends AbstractEntity implements ResourceInterface
{
    /**
     * Constant for a type without invoice.
     */
    public const NO_INVOICE = 0;
    /**
     * Constant for a type with a invoice.
     */
    public const INVOICE = 1;

    public const TYPE_UNKNOWN = 0;
    public const TYPE_IFC = 1;
    public const TYPE_LARGE_INDUSTRY = 2;
    public const TYPE_SME = 3;
    public const TYPE_RESEARCH = 4;
    public const TYPE_UNIVERSITY = 5;
    public const TYPE_GOVERNMENT = 6;
    public const TYPE_OTHER = 7;

    /**
     * Textual versions of the invoice.
     *
     * @var array
     */
    protected static $invoiceTemplates
        = [
            self::NO_INVOICE => 'txt-invoice',
            self::INVOICE    => 'txt-no-invoice',
        ];
    /**
     * @ORM\Column(name="type_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Type("\Zend\Form\Element\Hidden")
     *
     * @var integer
     */
    private $id;
    /**
     * @ORM\Column(name="type", type="string", length=20, nullable=false, unique=true)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-type"})
     * @Annotation\Required(true)
     *
     * @var string
     */
    private $type;
    /**
     * @ORM\Column(name="description", type="string", nullable=false, unique=true)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-type"})
     * @Annotation\Required(true)
     *
     * @var string
     */
    private $description;
    /**
     * @ORM\Column(type="smallint",nullable=true)
     * @Annotation\Type("Zend\Form\Element\Radio")
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
     * @var \Event\Entity\Meeting\Cost[]|Collections\ArrayCollection()
     */
    private $meetingCost;
    /**
     * @ORM\OneToMany(targetEntity="Organisation\Entity\Organisation", cascade={"persist"}, mappedBy="type")
     * @Annotation\Exclude()
     *
     * @var \Organisation\Entity\Organisation[]|Collections\ArrayCollection
     */
    private $organisation;

    /**
     * Class constructor.
     */
    public function __construct()
    {
        $this->organisation = new Collections\ArrayCollection();
        $this->meetingCost = new Collections\ArrayCollection();
    }

    /**
     * @return array
     */
    public static function getInvoiceTemplates(): array
    {
        return self::$invoiceTemplates;
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
        return (string)$this->description;
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
     * @return Type
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return Type
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return Type
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @param bool $textual
     *
     * @return int|string
     */
    public function getInvoice($textual = false)
    {
        if ($textual) {
            return self::$invoiceTemplates[$this->invoice];
        }

        return $this->invoice;
    }

    /**
     * @param int $invoice
     *
     * @return Type
     */
    public function setInvoice($invoice)
    {
        $this->invoice = $invoice;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Event\Entity\Meeting\Cost[]
     */
    public function getMeetingCost()
    {
        return $this->meetingCost;
    }

    /**
     * @param Collections\ArrayCollection|\Event\Entity\Meeting\Cost[] $meetingCost
     *
     * @return Type
     */
    public function setMeetingCost($meetingCost)
    {
        $this->meetingCost = $meetingCost;

        return $this;
    }

    /**
     * @return Organisation[]
     */
    public function getOrganisation()
    {
        return $this->organisation;
    }

    /**
     * @param Organisation[] $organisation
     *
     * @return Type
     */
    public function setOrganisation($organisation)
    {
        $this->organisation = $organisation;

        return $this;
    }
}
