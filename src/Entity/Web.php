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
 * @ORM\Table(name="organisation_web")
 * @ORM\Entity
 */
class Web extends AbstractEntity
{
    public const NOT_MAIN = 0;
    public const MAIN     = 1;

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

    public static function getMainTemplates(): array
    {
        return self::$mainTemplates;
    }

    public function __toString(): string
    {
        return sprintf($this->web);
    }

    public function isMain(): bool
    {
        return $this->main === self::MAIN;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Web
    {
        $this->id = $id;
        return $this;
    }

    public function getWeb(): ?string
    {
        return $this->web;
    }

    public function setWeb(?string $web): Web
    {
        $this->web = $web;
        return $this;
    }

    public function getMain(): ?int
    {
        return $this->main;
    }

    public function setMain(?int $main): Web
    {
        $this->main = $main;
        return $this;
    }

    public function getOrganisation(): ?Organisation
    {
        return $this->organisation;
    }

    public function setOrganisation(?Organisation $organisation): Web
    {
        $this->organisation = $organisation;
        return $this;
    }
}
