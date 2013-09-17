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
 * IctOrganisation
 *
 * @ORM\Table(name="organisation")
 * @ORM\Entity
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("organisation")
 */
class Organisation //extends EntityAbstract
{
    /**
     * @ORM\Column(name="organisation_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @var integer
     */
    private $id;
    /**
     * @ORM\Column(name="organisation", type="string", length=60, nullable=false)
     * @var string
     */
    private $organisation;
    /**
     * @ORM\OneToMany(targetEntity="\Contact\Entity\ContactOrganisation", cascade={"persist"}, mappedBy="organisation")
     * @Annotation\Exclude()
     * @var \Contact\Entity\ContactOrganisation[]
     */
    private $contactOrganisation;
    /**
     * @ORM\Column(name="date_created", type="datetime", nullable=true)
     * @var \DateTime
     */
    private $dateCreated;
    /**
     * @ORM\Column(name="docref", type="string", length=255, nullable=false, unique=true)
     * @Gedmo\Slug(fields={"id","organisation"})
     * @Annotation\Exclude()
     * @var string
     */
    private $docRef;
    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="date_updated", type="datetime", nullable=true)
     * @var \DateTime
     */
    private $dateUpdated;
    /**
     * @ORM\OneToMany(targetEntity="Affiliation\Entity\Affiliation", cascade={"persist"}, mappedBy="organisation")
     * @Annotation\Exclude()
     * @var \Affiliation\Entity\Affiliation[]
     */
    private $affiliation;
    /**
     * @ORM\ManyToOne(targetEntity="General\Entity\Country",inversedBy="organisation", cascade={"persist"})
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="country_id", referencedColumnName="country_id", nullable=true)
     * })
     * @var \General\Entity\Country
     */
    private $country;
    /**
     * @ORM\ManyToOne(targetEntity="Organisation\Entity\Type", inversedBy="organisation", cascade={"persist"})
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="type_id", referencedColumnName="type_id", nullable=true)
     * })
     * @var \Organisation\Entity\Type
     */
    private $type;
    /**
     * @ORM\ManyToMany(targetEntity="Program\Entity\Domain", inversedBy="organisation")
     * @ORM\JoinTable(name="organisation_domain",
     *            joinColumns={@ORM\JoinColumn(name="organisation_id", referencedColumnName="organisation_id")},
     *            inverseJoinColumns={@ORM\JoinColumn(name="domain_id", referencedColumnName="domain_id")}
     * )
     * @Annotation\Type("DoctrineORMModule\Form\Element\EntityMultiCheckbox")
     * @Annotation\Options({"target_class":"Program\Entity\Domain"})
     * @Annotation\Attributes({"label":"txt-domain"})
     * @var \Program\Entity\Domain[]
     */
    private $domain;
    /**
     * @ORM\ManyToMany(targetEntity="Program\Entity\Technology", inversedBy="organisation")
     * @ORM\JoinTable(name="organisation_technology",
     *            joinColumns={@ORM\JoinColumn(name="organisation_id", referencedColumnName="organisation_id")},
     *            inverseJoinColumns={@ORM\JoinColumn(name="technology_id", referencedColumnName="technology_id")}
     * )
     * @Annotation\Type("DoctrineORMModule\Form\Element\EntityMultiCheckbox")
     * @Annotation\Options({"target_class":"Program\Entity\Technology"})
     * @Annotation\Attributes({"label":"txt-technology"})
     * @var \Program\Entity\Technology[]
     */
    private $technology;
    /**
     * @ORM\OneToMany(targetEntity="Organisation\Entity\Cluster", cascade={"persist"}, mappedBy="organisation")
     * @Annotation\Exclude()
     * @var \Organisation\Entity\Cluster[]
     */
    private $cluster;
    /**
     * @ORM\OneToMany(targetEntity="Organisation\Entity\ClusterMember", cascade={"persist"}, mappedBy="organisation")
     * @Annotation\Exclude()
     * @var \Organisation\Entity\ClusterMember[]
     */
    private $clusterMember;
    /**
     * @ORM\OneToMany(targetEntity="Organisation\Entity\Log", cascade={"persist"}, mappedBy="organisation")
     * @Annotation\Exclude()
     * @var \Organisation\Entity\Log[]
     */
    private $log;
    /**
     * @ORM\OneToMany(targetEntity="Organisation\Entity\Description", cascade={"persist"}, mappedBy="organisation")
     * @Annotation\Exclude()
     * @var \Organisation\Entity\Description[]
     */
    private $description;
    /**
     * @ORM\OneToMany(targetEntity="Organisation\Entity\Logo", cascade={"persist"}, mappedBy="organisation")
     * @Annotation\Exclude()
     * @var \Organisation\Entity\Logo[]
     */
    private $logo;
    /**
     * @ORM\OneToMany(targetEntity="Organisation\Entity\Note", cascade={"persist"}, mappedBy="organisation")
     * @Annotation\Exclude()
     * @var \Organisation\Entity\Note[]
     */
    private $note;
    /**
     * @ORM\OneToMany(targetEntity="Organisation\Entity\Financial", cascade={"persist"}, mappedBy="organisation")
     * @Annotation\Exclude()
     * @var \Organisation\Entity\Financial[]
     */
    private $financial;
    /**
     * @ORM\OneToMany(targetEntity="Organisation\Entity\Financial", cascade={"persist"}, mappedBy="debtor")
     * @Annotation\Exclude()
     * @var \Organisation\Entity\Financial[]
     */
    private $financialDebtor;
    /**
     * @ORM\OneToMany(targetEntity="\Program\Entity\ProgramDoa", cascade={"persist"}, mappedBy="organisation")
     * @Annotation\Exclude()
     * @var \Program\Entity\ProgramDoa[]
     */
    private $programDoa;
}
