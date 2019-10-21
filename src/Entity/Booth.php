<?php
/**
 * ITEA copyright message placeholder.
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Organisation\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrganisationBooth.
 *
 * @ORM\Table(name="organisation_booth")
 * @ORM\Entity
 */
class Booth extends AbstractEntity
{
    /**
     * @ORM\Column(name="organisation_booth_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @var int
     */
    private $id;
    /**
     * @ORM\OneToOne(targetEntity="Event\Entity\Booth\Booth", cascade="persist", inversedBy="organisationBooth")
     * @ORM\JoinColumn(name="booth_id", referencedColumnName="booth_id", nullable=false)
     *
     * @var \Event\Entity\Booth\Booth
     */
    private $booth;
    /**
     * @ORM\ManyToOne(targetEntity="Organisation\Entity\Organisation", cascade="persist", inversedBy="organisationBooth")
     * @ORM\JoinColumn(name="organisation_id", referencedColumnName="organisation_id", nullable=false)
     *
     * @var \Organisation\Entity\Organisation
     */
    private $organisation;
    /**
     * @ORM\ManyToOne(targetEntity="Contact\Entity\Contact", cascade="persist", inversedBy="organisationBooth")
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id", nullable=false)
     *
     * @var \Contact\Entity\Contact
     */
    private $contact;

    /**
     * @return string
     */
    public function __toString(): string
    {
        return sprintf("Booth %s on %s", $this->organisation, $this->booth);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return Booth
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return \Event\Entity\Booth\Booth
     */
    public function getBooth()
    {
        return $this->booth;
    }

    /**
     * @param \Event\Entity\Booth\Booth $booth
     *
     * @return Booth
     */
    public function setBooth($booth)
    {
        $this->booth = $booth;

        return $this;
    }

    /**
     * @return Organisation
     */
    public function getOrganisation()
    {
        return $this->organisation;
    }

    /**
     * @param Organisation $organisation
     *
     * @return Booth
     */
    public function setOrganisation($organisation)
    {
        $this->organisation = $organisation;

        return $this;
    }

    /**
     * @return \Contact\Entity\Contact
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @param \Contact\Entity\Contact $contact
     *
     * @return Booth
     */
    public function setContact($contact)
    {
        $this->contact = $contact;

        return $this;
    }
}
