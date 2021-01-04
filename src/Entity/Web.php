<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Organisation\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrganisationWeb.
 *
 * @ORM\Table(name="organisation_web")
 * @ORM\Entity
 */
class Web extends AbstractEntity
{
    public const NOT_MAIN = 0;
    public const MAIN = 1;

    protected static array $mainTemplates
        = [
            self::NOT_MAIN => 'txt-not-main-web-address',
            self::MAIN     => 'txt-main-web-address',
        ];

    /**
     * @ORM\Column(name="web_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @var int
     */
    private $id;
    /**
     * @ORM\Column(name="web", type="string", nullable=false)
     *
     * @var string
     */
    private $web;
    /**
     * @ORM\Column(name="main", type="smallint", nullable=false)
     *
     * @var int
     */
    private $main;
    /**
     * @ORM\ManyToOne(targetEntity="Organisation", cascade={"persist"}, inversedBy="web")
     * @ORM\JoinColumn(name="organisation_id", referencedColumnName="organisation_id", nullable=true)
     *
     * @var \Organisation\Entity\Organisation
     */
    private $organisation;

    public function __toString(): string
    {
        return sprintf($this->web);
    }

    public function isMain(): bool
    {
        return $this->main === self::MAIN;
    }

    public static function getMainTemplates(): array
    {
        return self::$mainTemplates;
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
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getMain()
    {
        return $this->main;
    }

    /**
     * @param int $main
     */
    public function setMain($main)
    {
        $this->main = $main;
    }

    /**
     * @return \Organisation\Entity\Organisation
     */
    public function getOrganisation()
    {
        return $this->organisation;
    }

    /**
     * @param \Organisation\Entity\Organisation $organisation
     */
    public function setOrganisation($organisation)
    {
        $this->organisation = $organisation;
    }

    /**
     * @return string
     */
    public function getWeb()
    {
        return $this->web;
    }

    /**
     * @param string $web
     */
    public function setWeb($web)
    {
        $this->web = $web;
    }
}
