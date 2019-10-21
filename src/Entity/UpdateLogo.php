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
use General\Entity\ContentType;
use Zend\Form\Annotation;

/**
 * OrganisationLogo.
 *
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
     * @ORM\ManyToOne(targetEntity="General\Entity\ContentType", cascade={"persist"}, inversedBy="organisationUpdateLogos")
     * @ORM\JoinColumn(name="contenttype_id", referencedColumnName="contenttype_id", nullable=false)
     *
     * @var ContentType
     */
    private $contentType;
    /**
     * @ORM\OneToOne(targetEntity="Organisation\Entity\Update", inversedBy="logo", cascade={"persist"})
     * @ORM\JoinColumn(name="update_id", referencedColumnName="update_id")
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
