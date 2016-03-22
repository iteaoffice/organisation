<?php

namespace Organisation\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

/**
 * OrganisationLink.
 *
 * @ORM\Table(name="organisation_link")
 * @ORM\Entity
 */
class Link extends EntityAbstract
{
    /**
     * @var integer
     *
     * @ORM\Column(name="link_id", length=10, type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $linkId;
    /**
     * @var \Organisation
     *
     * @ORM\ManyToOne(targetEntity="Organisation")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="organisation1_id", referencedColumnName="organisation_id")
     * })
     */
    private $organisation1;
    /**
     * @var \Organisation
     *
     * @ORM\ManyToOne(targetEntity="Organisation")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="organisation2_id", referencedColumnName="organisation_id")
     * })
     */
    private $organisation2;

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
     * @param InputFilterInterface $inputFilter
     *
     * @return void
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
        return new InputFilter();
    }

    /**
     * @return int
     */
    public function getLinkId()
    {
        return $this->linkId;
    }

    /**
     * @param int $linkId
     *
     * @return Link
     */
    public function setLinkId($linkId)
    {
        $this->linkId = $linkId;

        return $this;
    }

    /**
     * @return \Organisation
     */
    public function getOrganisation1()
    {
        return $this->organisation1;
    }

    /**
     * @param \Organisation $organisation1
     *
     * @return Link
     */
    public function setOrganisation1($organisation1)
    {
        $this->organisation1 = $organisation1;

        return $this;
    }

    /**
     * @return \Organisation
     */
    public function getOrganisation2()
    {
        return $this->organisation2;
    }

    /**
     * @param \Organisation $organisation2
     *
     * @return Link
     */
    public function setOrganisation2($organisation2)
    {
        $this->organisation2 = $organisation2;

        return $this;
    }
}
