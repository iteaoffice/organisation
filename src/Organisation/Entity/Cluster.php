<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * OrganisationCluster
 *
 * @ORM\Table(name="organisation_cluster")
 * @ORM\Entity
 */
class OrganisationCluster
{
    /**
     * @var integer
     *
     * @ORM\Column(name="cluster_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $clusterId;

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
