<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Organisation\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
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

    public function __toString(): string
    {
        return sprintf("Booth %s on %s", $this->organisation, $this->booth);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Booth
    {
        $this->id = $id;
        return $this;
    }

    public function getBooth(): ?\Event\Entity\Booth\Booth
    {
        return $this->booth;
    }

    public function setBooth(?\Event\Entity\Booth\Booth $booth): Booth
    {
        $this->booth = $booth;
        return $this;
    }

    public function getOrganisation(): ?Organisation
    {
        return $this->organisation;
    }

    public function setOrganisation(?Organisation $organisation): Booth
    {
        $this->organisation = $organisation;
        return $this;
    }

    public function getContact(): ?\Contact\Entity\Contact
    {
        return $this->contact;
    }

    public function setContact(?\Contact\Entity\Contact $contact): Booth
    {
        $this->contact = $contact;
        return $this;
    }
}
