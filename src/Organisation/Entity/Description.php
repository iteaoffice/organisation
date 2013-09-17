<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * OrganisationDescription
 *
 * @ORM\Table(name="organisation_description")
 * @ORM\Entity
 */
class OrganisationDescription
{
    /**
     * @var integer
     *
     * @ORM\Column(name="description_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $descriptionId;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=false)
     */
    private $description;

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
