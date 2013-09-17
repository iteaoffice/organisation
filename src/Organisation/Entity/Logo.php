<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * OrganisationLogo
 *
 * @ORM\Table(name="organisation_logo")
 * @ORM\Entity
 */
class OrganisationLogo
{
    /**
     * @var integer
     *
     * @ORM\Column(name="logo_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $logoId;

    /**
     * @var string
     *
     * @ORM\Column(name="organisation_logo", type="blob", nullable=false)
     */
    private $organisationLogo;

    /**
     * @var string
     *
     * @ORM\Column(name="logo_extension", type="string", length=20, nullable=false)
     */
    private $logoExtension;

    /**
     * @var integer
     *
     * @ORM\Column(name="contenttype_id", type="integer", nullable=false)
     */
    private $contenttypeId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_updated", type="datetime", nullable=false)
     */
    private $dateUpdated;

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
