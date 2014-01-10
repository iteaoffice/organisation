<?php
/**
 * Debranova copyright message placeholder
 *
 * @category    Organisation
 * @package     Entity
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 Debranova
 */

namespace Organisation\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrganisationWeb
 *
 * @ORM\Table(name="organisation_web")
 * @ORM\Entity
 */
class Web
{
    const NOT_MAIN = 0;
    const MAIN     = 1;

    /**
     * @ORM\Column(name="web_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @var integer
     */
    private $id;
    /**
     * @ORM\Column(name="web", type="string", length=60, nullable=false)
     * @var string
     */
    private $web;
    /**
     * @ORM\Column(name="main", type="smallint", nullable=false)
     * @var integer
     */
    private $main;
    /**
     * @ORM\ManyToOne(targetEntity="Organisation", cascade={"persist"}, inversedBy="web")
     * @ORM\JoinColumns({
     *  @ORM\JoinColumn(name="organisation_id", referencedColumnName="organisation_id", nullable=true)
     * })
     * @var \Organisation\Entity\Organisation
     */
    private $organisation;

    public function __toString()
    {
        $url = '<a href="http://%s">%s</a>';

        return sprintf($url, $this->web, $this->web);
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
