<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 */

namespace Organisation\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use Zend\Permissions\Acl\Resource\ResourceInterface;

/**
 * IctOrganisation.
 *
 * @ORM\Table(name="ict_organisation")
 * @ORM\Entity
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("ict_organisation")
 */
class IctOrganisation extends EntityAbstract implements ResourceInterface
{
    /**
     * @ORM\Column(name="ict_id", length=10, type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @var integer
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="General\Entity\Country", inversedBy="ictOrganisation")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="country_id", referencedColumnName="country_id", nullable=false)
     * })
     *
     * @var \General\Entity\Country
     */
    private $country;
    /**
     * @ORM\ManyToOne(targetEntity="Organisation")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="organisation_id", referencedColumnName="organisation_id")
     * })
     *
     * @var \Organisation\Entity\Organisation
     */
    private $organisation;
    /**
     * @ORM\ManyToMany(targetEntity="Affiliation\Entity\Affiliation", cascade={"persist"}, mappedBy="ictOrganisation")
     * @Annotation\Exclude()
     *
     * @var \Affiliation\Entity\Affiliation[]
     */
    private $affiliation;

    /**
     *
     */
    public function __construct()
    {
    }

    /**
     * Returns the string identifier of the Resource.
     *
     * @return string
     */
    public function getResourceId()
    {
        return sprintf("%s:%s", __CLASS__, $this->id);
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
        return (string)$this->organisation;
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
     * @return IctOrganisation
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return \General\Entity\Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param \General\Entity\Country $country
     *
     * @return IctOrganisation
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return Organisation
     */
    public function getOrganisation()
    {
        return $this->organisation;
    }

    /**
     * @param Organisation $organisation
     *
     * @return IctOrganisation
     */
    public function setOrganisation($organisation)
    {
        $this->organisation = $organisation;

        return $this;
    }

    /**
     * @return \Affiliation\Entity\Affiliation[]
     */
    public function getAffiliation()
    {
        return $this->affiliation;
    }

    /**
     * @param \Affiliation\Entity\Affiliation[] $affiliation
     *
     * @return IctOrganisation
     */
    public function setAffiliation($affiliation)
    {
        $this->affiliation = $affiliation;

        return $this;
    }
}
