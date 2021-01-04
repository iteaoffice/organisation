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

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use General\Entity\ContentType;
use Laminas\Form\Annotation;

/**
 * @ORM\Table(name="organisation_update_logo")
 * @ORM\Entity
 * @Annotation\Name("organisation_update_logo")
 */
class UpdateLogo extends AbstractEntity
{
    /**
     * @ORM\Column(name="logo_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    /**
     * @ORM\Column(name="organisation_logo", type="blob", nullable=false)
     *
     * @var resource
     */
    private $organisationLogo;
    /**
     * @ORM\Column(name="logo_extension", type="string", nullable=false)
     *
     * @var string
     */
    private $logoExtension;
    /**
     * @ORM\ManyToOne(targetEntity="General\Entity\ContentType", cascade={"persist"}, inversedBy="organisationUpdateLogos")
     * @ORM\JoinColumn(name="contenttype_id", referencedColumnName="contenttype_id", nullable=false)
     *
     * @var ContentType
     */
    private $contentType;
    /**
     * @ORM\Column(name="date_created", type="datetime", nullable=false)
     * @Gedmo\Timestampable(on="create")
     *
     * @var DateTime
     */
    private $dateCreated;
    /**
     * @ORM\Column(name="date_updated", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     *
     * @var DateTime
     */
    private $dateUpdated;
    /**
     * @ORM\OneToOne(targetEntity="Organisation\Entity\Update", inversedBy="logo", cascade={"persist"})
     * @ORM\JoinColumn(name="update_id", referencedColumnName="update_id", nullable=true)
     *
     * @var Update
     */
    private $update;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id)
    {
        $this->id = $id;
        return $this;
    }

    public function getOrganisationLogo()
    {
        return $this->organisationLogo;
    }

    public function setOrganisationLogo($organisationLogo): UpdateLogo
    {
        $this->organisationLogo = $organisationLogo;
        return $this;
    }

    public function getLogoExtension(): ?string
    {
        return $this->logoExtension;
    }

    public function setLogoExtension(string $logoExtension): UpdateLogo
    {
        $this->logoExtension = $logoExtension;
        return $this;
    }

    public function getContentType(): ?ContentType
    {
        return $this->contentType;
    }

    public function setContentType(ContentType $contentType): UpdateLogo
    {
        $this->contentType = $contentType;
        return $this;
    }

    public function getDateCreated(): ?DateTime
    {
        return $this->dateCreated;
    }

    public function setDateCreated(DateTime $dateCreated): UpdateLogo
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    public function getDateUpdated(): ?DateTime
    {
        return $this->dateUpdated;
    }

    public function setDateUpdated(DateTime $dateUpdated): UpdateLogo
    {
        $this->dateUpdated = $dateUpdated;
        return $this;
    }

    public function getUpdate(): ?Update
    {
        return $this->update;
    }

    public function setUpdate(Update $update): UpdateLogo
    {
        $this->update = $update;
        return $this;
    }
}
