<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * OrganisationBooth
 *
 * @ORM\Table(name="organisation_booth")
 * @ORM\Entity
 */
class OrganisationBooth
{
    /**
     * @var integer
     *
     * @ORM\Column(name="organisation_booth_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $organisationBoothId;

    /**
     * @var \Booth
     *
     * @ORM\ManyToOne(targetEntity="Booth")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="booth_id", referencedColumnName="booth_id")
     * })
     */
    private $booth;

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
