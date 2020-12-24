<?php

/**
 * ITEA Office all rights reserved
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Organisation\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Laminas\Form\Annotation;

/**
 * OrganisationLogo.
 *
 * @ORM\Table(name="organisation_logo")
 * @ORM\Entity
 * @Annotation\Hydrator("Laminas\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("organisation_logo")
 */
class Logo extends AbstractEntity
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
     * @var string
     */
    private $organisationLogo;
    /**
     * @ORM\Column(name="logo_extension", type="string", nullable=false)
     *
     * @var string
     */
    private $logoExtension;
    /**
     * @ORM\ManyToOne(targetEntity="General\Entity\ContentType", cascade={"persist"}, inversedBy="organisationLogo")
     * @ORM\JoinColumn(name="contenttype_id", referencedColumnName="contenttype_id", nullable=false)
     * @Annotation\Type("\Laminas\Form\Element\File")
     * @Annotation\Options({"label":"txt-logo"})
     *
     * @var \General\Entity\ContentType
     */
    private $contentType;
    /**
     * @ORM\Column(name="date_updated", type="datetime", nullable=false)
     * @Gedmo\Timestampable(on="update")
     *
     * @var \DateTime
     */
    private $dateUpdated;
    /**
     * @ORM\ManyToOne(targetEntity="Organisation\Entity\Organisation", inversedBy="logo", cascade={"persist"})
     * @ORM\JoinColumn(name="organisation_id", referencedColumnName="organisation_id")
     *
     * @var \Organisation\Entity\Organisation
     */
    private $organisation;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): Logo
    {
        $this->id = $id;
        return $this;
    }

    public function getOrganisationLogo()
    {
        return $this->organisationLogo;
    }

    public function setOrganisationLogo($organisationLogo): Logo
    {
        $this->organisationLogo = $organisationLogo;
        return $this;
    }

    public function getLogoExtension(): ?string
    {
        return $this->logoExtension;
    }

    public function setLogoExtension(?string $logoExtension): Logo
    {
        $this->logoExtension = $logoExtension;
        return $this;
    }

    public function getContentType(): ?\General\Entity\ContentType
    {
        return $this->contentType;
    }

    public function setContentType(?\General\Entity\ContentType $contentType): Logo
    {
        $this->contentType = $contentType;
        return $this;
    }

    public function getDateUpdated(): ?\DateTime
    {
        return $this->dateUpdated;
    }

    public function setDateUpdated(?\DateTime $dateUpdated): Logo
    {
        $this->dateUpdated = $dateUpdated;
        return $this;
    }

    public function getOrganisation(): ?Organisation
    {
        return $this->organisation;
    }

    public function setOrganisation(?Organisation $organisation): Logo
    {
        $this->organisation = $organisation;
        return $this;
    }
}
