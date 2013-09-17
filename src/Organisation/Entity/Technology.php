<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * OrganisationTechnology
 *
 * @ORM\Table(name="organisation_technology")
 * @ORM\Entity
 */
class OrganisationTechnology
{
    /**
     * @var integer
     *
     * @ORM\Column(name="organisation_technology_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $organisationTechnologyId;

    /**
     * @var \Organisation
     *
     * @ORM\ManyToOne(targetEntity="Organisation")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="organisation_id", referencedColumnName="organisation_id")
     * })
     */
    private $organisation;

    /**
     * @var \Technology
     *
     * @ORM\ManyToOne(targetEntity="Technology")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="technology_id", referencedColumnName="technology_id")
     * })
     */
    private $technology;


}
