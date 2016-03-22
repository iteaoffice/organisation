<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 */

namespace Organisation\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Zend\Form\Annotation;

/**
 * OrganisationLogo.
 *
 * @ORM\Table(name="organisation_logo")
 * @ORM\Entity
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("organisation_logo")
 */
class Logo
{
    const HASH_KEY = '49fksdr80sdf83409jsadvkljasruwasef';
    /**
     * @ORM\Column(name="logo_id", length=10, type="integer", nullable=false)
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
     * @ORM\Column(name="logo_extension", type="string", length=20, nullable=false)
     *
     * @var string
     */
    private $logoExtension;
    /**
     * @ORM\ManyToOne(targetEntity="General\Entity\ContentType", cascade={"persist"}, inversedBy="organisationLogo")
     * @ORM\JoinColumn(name="contenttype_id", referencedColumnName="contenttype_id", nullable=false)
     * @Annotation\Type("\Zend\Form\Element\File")
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
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="organisation_id", referencedColumnName="organisation_id")
     * })
     *
     * @var \Organisation\Entity\Organisation
     */
    private $organisation;

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
     * @return string
     */
    public function getCacheFileName()
    {
        $cacheDir = __DIR__.'/../../../../../public'.DIRECTORY_SEPARATOR.'assets'.
            DIRECTORY_SEPARATOR.DEBRANOVA_HOST.DIRECTORY_SEPARATOR.'organisation-logo';

        return $cacheDir.DIRECTORY_SEPARATOR.$this->getFileName();
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return sprintf("%s-%s.%s", $this->getId(), $this->getHash(), $this->getContentType()->getExtension());
    }

    /**
     * Although an alternative does not have a clear hash, we can create one based on the id;.
     *
     * @return string
     */
    public function getHash()
    {
        return hash('sha512', $this->id.self::HASH_KEY);
    }

    /**
     * @param \General\Entity\ContentType $contentType
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * @return \General\Entity\ContentType
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @param \DateTime $dateUpdated
     */
    public function setDateUpdated($dateUpdated)
    {
        $this->dateUpdated = $dateUpdated;
    }

    /**
     * @return \DateTime
     */
    public function getDateUpdated()
    {
        return $this->dateUpdated;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $logoExtension
     */
    public function setLogoExtension($logoExtension)
    {
        $this->logoExtension = $logoExtension;
    }

    /**
     * @return string
     */
    public function getLogoExtension()
    {
        return $this->logoExtension;
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
     * @param string $organisationLogo
     */
    public function setOrganisationLogo($organisationLogo)
    {
        $this->organisationLogo = $organisationLogo;
    }

    /**
     * @return resource
     */
    public function getOrganisationLogo()
    {
        return $this->organisationLogo;
    }
}
