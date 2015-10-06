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

use Doctrine\ORM\Mapping as ORM;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Permissions\Acl\Resource\ResourceInterface;

/**
 * OrganisationWeb.
 *
 * @ORM\Table(name="organisation_web")
 * @ORM\Entity
 */
class Web extends EntityAbstract implements ResourceInterface
{
    const NOT_MAIN = 0;
    const MAIN = 1;
    /**
     * @ORM\Column(name="web_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @var integer
     */
    private $id;
    /**
     * @ORM\Column(name="web", type="string", length=60, nullable=false)
     *
     * @var string
     */
    private $web;
    /**
     * @ORM\Column(name="main", type="smallint", nullable=false)
     *
     * @var integer
     */
    private $main;
    /**
     * @ORM\ManyToOne(targetEntity="Organisation", cascade={"persist"}, inversedBy="web")
     * @ORM\JoinColumns({
     *  @ORM\JoinColumn(name="organisation_id", referencedColumnName="organisation_id", nullable=true)
     * })
     *
     * @var \Organisation\Entity\Organisation
     */
    private $organisation;

    /**
     * @return string
     */
    public function __toString()
    {
        $url = '<a href="http://%s">%s</a>';

        return sprintf($url, $this->web, $this->web);
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
     * Magic Getter.
     *
     * @param $property
     *
     * @return mixed
     */
    public function __get($property)
    {
        return $this->$property;
    }

    /**
     * Magic Setter.
     *
     * @param $property
     * @param $value
     */
    public function __set($property, $value)
    {
        $this->$property = $value;
    }

    /**
     * Set input filter.
     *
     * @param InputFilterInterface $inputFilter
     *
     * @return InputFilterAwareInterface
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
    }

    /**
     * Retrieve input filter.
     *
     * @return InputFilterInterface
     */
    public function getInputFilter()
    {
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
     * @param int $main
     */
    public function setMain($main)
    {
        $this->main = $main;
    }

    /**
     * @return int
     */
    public function getMain()
    {
        return $this->main;
    }

    /**
     * @param \Organisation\Entity\Organisation $organisation
     */
    public function setOrganisation($organisation)
    {
        $this->organisation = $organisation;
    }

    /**
     * @return \Organisation\Entity\Organisation
     */
    public function getOrganisation()
    {
        return $this->organisation;
    }

    /**
     * @param string $web
     */
    public function setWeb($web)
    {
        $this->web = $web;
    }

    /**
     * @return string
     */
    public function getWeb()
    {
        return $this->web;
    }
}
