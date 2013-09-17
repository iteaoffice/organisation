<?php

namespace Organisation\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrganisationWeb
 *
 * @ORM\Table(name="organisation_web")
 * @ORM\Entity
 */
class OrganisationWeb
{
    /**
     * @var integer
     *
     * @ORM\Column(name="web_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $webId;

    /**
     * @var string
     *
     * @ORM\Column(name="web", type="string", length=60, nullable=false)
     */
    private $web;

    /**
     * @var integer
     *
     * @ORM\Column(name="main", type="smallint", nullable=false)
     */
    private $main;

    /**
     * @var \Organisation
     *
     * @ORM\ManyToOne(targetEntity="Organisation")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="organisation_id", referencedColumnName="organisation_id")
     * })
     */
    private $organisation;


}
