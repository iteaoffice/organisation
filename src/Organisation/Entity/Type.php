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
 * Type
 *
 * @ORM\Table(name="organisation_type")
 * @ORM\Entity
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("organisation_type")
 */
class Type //extends EntityAbstract
{
    /**
     * Constant for a type without invoice
     */
    const NO_INVOICE = 0;
    /**
     * Constant for a type with a invoice
     */
    const INVOICE = 1;
    /**
     * Textual versions of the invoice
     *
     * @var array
     */
    protected $invoiceTemplates = array(
        self::NO_INVOICE => 'txt-invoice',
        self::INVOICE    => 'txt-no-invoice',
    );


    /**
     * @ORM\Column(name="type_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @var integer
     */
    private $id;
    /**
     * @ORM\Column(name="type", type="string", length=20, nullable=false, unique=true)
     * @var string
     */
    private $type;
    /**
     * @ORM\Column(name="description", type="string", length=40, nullable=false, unique=true)
     * @var string
     */
    private $description;
    /**
     * @ORM\Column(type="smallint",nullable=true)
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Attributes({"array":"invoiceTemplates"})
     * @Annotation\Attributes({"label":"txt-invoice"})
     * @var \int
     */
    private $invoice;
    /**
     * @ORM\OneToMany(targetEntity="Organisation\Entity\Organisation", cascade={"persist"}, mappedBy="type")
     * @Annotation\Exclude()
     * @var \Organisation\Entity\Organisation[]
     */
    private $organisation;
}
