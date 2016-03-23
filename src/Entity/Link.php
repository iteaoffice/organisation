<?php

namespace Organisation\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @return int
     */
    public function getId()
    {
        return $this->linkId;
    }

    /**
     * @param int $linkId
     *
     * @return Link
     */
    public function setId($linkId)
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
