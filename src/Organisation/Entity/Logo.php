<?php
/**
 * Debranova copyright message placeholder
 *
 * @category    Organisation
 * @package     Entity
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Debranova
 */
namespace Organisation\Entity;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\Factory as InputFactory;
use Zend\Form\Annotation;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * OrganisationLogo
 *
 * @ORM\Table(name="organisation_logo")
 * @ORM\Entity
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("organisation_logo")
 */
class Logo
{
    /**
     * @ORM\Column(name="logo_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
    @var integer
     */
    private $id;
    /**
     * @ORM\Column(name="organisation_logo", type="blob", nullable=false)
     * @var string
     */
    private $organisationLogo;
    /**
     * @ORM\Column(name="logo_extension", type="string", length=20, nullable=false)
     * @var string
     */
    private $logoExtension;
    /**
     * @ORM\ManyToOne(targetEntity="General\Entity\ContentType", cascade={"persist"}, inversedBy="organisationLogo")
     * @ORM\JoinColumn(name="contenttype_id", referencedColumnName="contenttype_id", nullable=false)
     * @Annotation\Type("\Zend\Form\Element\File")
     * @Annotation\Options({"label":"txt-logo"})
     * @var \General\Entity\ContentType
     */
    private $contentType;
    /**
     * @ORM\Column(name="date_updated", type="datetime", nullable=false)
     * @Gedmo\Timestampable(on="update")
     * @var \DateTime
     */
    private $dateUpdated;
    /**
     * @ORM\ManyToOne(targetEntity="Organisation\Entity\Organisation", inversedBy="logo", cascade={"persist"})
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="organisation_id", referencedColumnName="organisation_id")
     * })
     * @var \Organisation\Entity\Organisation
     */
    private $organisation;

    /**
     * Although an alternative does not have a clear hash, we can create one based on the id;
     *
     * @return string
     */
    public function getHash()
    {
        return sha1($this->id . $this->getOrganisation()->getOrganisation());
    }

    /**
     * @return string
     * @todo: make the location of the logo dynamic
     */
    public function getCacheFileName()
    {
        $cacheDir = __DIR__ . '/../../../../../../public' . DIRECTORY_SEPARATOR . 'assets' .
            DEBRANOVA_HOST . DIRECTORY_SEPARATOR . 'organisation-logo';

        return $cacheDir . DIRECTORY_SEPARATOR . $this->getHash() . '.' . $this->getContentType()->getExtension();
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
     * @return string
     */
    public function getOrganisationLogo()
    {
        return $this->organisationLogo;
    }
}
