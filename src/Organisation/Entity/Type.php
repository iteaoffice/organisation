<?php
/**
 * Debranova copyright message placeholder.
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 Debranova
 */

namespace Organisation\Entity;

use Doctrine\Common\Collections;
use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

/**
 * Type.
 *
 * @ORM\Table(name="organisation_type")
 * @ORM\Entity
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("organisation_type")
 */
class Type extends EntityAbstract
{
    /**
     * Constant for a type without invoice.
     */
    const NO_INVOICE = 0;
    /**
     * Constant for a type with a invoice.
     */
    const INVOICE = 1;

    const TYPE_UNKNOWN = 0;
    const TYPE_IFC = 1;
    const TYPE_LARGE_INDUSTRY = 2;
    const TYPE_SME = 3;
    const TYPE_RESEARCH = 4;
    const TYPE_UNIVERSITY = 5;
    const TYPE_GOVERNMENT = 6;
    const TYPE_OTHER = 7;

    /**
     * Textual versions of the invoice.
     *
     * @var array
     */
    protected $invoiceTemplates = [
        self::NO_INVOICE => 'txt-invoice',
        self::INVOICE    => 'txt-no-invoice',
    ];
    /**
     * @ORM\Column(name="type_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Exclude()
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
     * @ORM\Column(name="description", type="string", length=40, nullable=false, unique=true)
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
     * @var \int
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
     * @var \Organisation\Entity\Organisation[]
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
     * ToString
     * Return the id here for form population.
     *
     * @return string
     */
    public function __toString()
    {
        return (string)$this->description;
    }

    /**
     * @param InputFilterInterface $inputFilter
     *
     * @throws \Exception
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception(sprintf("This class %s is unused", __CLASS__));
    }

    /**
     * @return \Zend\InputFilter\InputFilter|\Zend\InputFilter\InputFilterInterface
     */
    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory = new InputFactory();
            $inputFilter->add(
                $factory->createInput(
                    [
                        'name'       => 'type',
                        'required'   => true,
                        'filters'    => [
                            ['name' => 'StripTags'],
                            ['name' => 'StringTrim'],
                        ],
                        'validators' => [
                            [
                                'name'    => 'StringLength',
                                'options' => [
                                    'encoding' => 'UTF-8',
                                    'min'      => 1,
                                    'max'      => 255,
                                ],
                            ],
                        ],
                    ]
                )
            );
            $inputFilter->add(
                $factory->createInput(
                    [
                        'name'       => 'invoice',
                        'required'   => true,
                        'validators' => [
                            [
                                'name'    => 'InArray',
                                'options' => [
                                    'haystack' => array_keys($this->getInvoiceTemplates()),
                                ],
                            ],
                        ],
                    ]
                )
            );
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

    /**
     * Needed for the hydration of form elements.
     *
     * @return array
     */
    public function getArrayCopy()
    {
        return [
            'type'        => $this->type,
            'description' => $this->description,
            'invoice'     => $this->invoice,
        ];
    }

    /**
     * @return array
     */
    public function populate()
    {
        return $this->getArrayCopy();
    }

    /**
     * @return array
     */
    public function getInvoiceTemplates()
    {
        return $this->invoiceTemplates;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $invoice
     */
    public function setInvoice($invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * @return int
     */
    public function getInvoice()
    {
        return $this->invoice;
    }

    /**
     * @param \Organisation\Entity\Organisation[] $organisation
     */
    public function setOrganisation($organisation)
    {
        $this->organisation = $organisation;
    }

    /**
     * @return \Organisation\Entity\Organisation[]
     */
    public function getOrganisation()
    {
        return $this->organisation;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
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
     * @return Type
     */
    public function setMeetingCost($meetingCost)
    {
        $this->meetingCost = $meetingCost;

        return $this;
    }
}
