<?php


namespace Organisation\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * OrganisationLink
 *
 * @ORM\Table(name="organisation_link")
 * @ORM\Entity
 */
class OrganisationLink
{
    /**
     * @var integer
     *
     * @ORM\Column(name="link_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $linkId;

    /**
     * @var \Organisation
     *
     * @ORM\ManyToOne(targetEntity="Organisation")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="organisation1_id", referencedColumnName="organisation_id")
     * })
     */
    private $organisation1;

    /**
     * @var \Organisation
     *
     * @ORM\ManyToOne(targetEntity="Organisation")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="organisation2_id", referencedColumnName="organisation_id")
     * })
     */
    private $organisation2;


}
