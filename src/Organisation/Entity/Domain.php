<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * OrganisationDomain
 *
 * @ORM\Table(name="organisation_domain")
 * @ORM\Entity
 */
class OrganisationDomain
{
    /**
     * @var integer
     *
     * @ORM\Column(name="organisation_domain_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $organisationDomainId;

    /**
     * @var \Domain
     *
     * @ORM\ManyToOne(targetEntity="Domain")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="domain_id", referencedColumnName="domain_id")
     * })
     */
    private $domain;

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
