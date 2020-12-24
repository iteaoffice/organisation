<?php

/**
 * ITEA Office all rights reserved
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Organisation\Entity;

use Doctrine\ORM\Mapping as ORM;
use Laminas\Form\Annotation;

/**
 * @ORM\Table(name="organisation_description")
 * @ORM\Entity
 * @Annotation\Hydrator("Laminas\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("organisation_description")
 * @Annotation\Instance("Organisation\Entity\Description")
 */
class Description extends AbstractEntity
{
    /**
     * @ORM\Column(name="description_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @var int
     */
    private $id;
    /**
     * @ORM\Column(name="description", type="text", nullable=false)
     * @Annotation\Type("\Laminas\Form\Element\Textarea")
     * @Annotation\Options({"label":"txt-organisation-description-label","help-block":"txt-organisation-description-help-block"})
     * @Annotation\Attributes({"placeholder":"txt-organisation-description-placeholder","rows":10})
     *
     * @var string
     */
    private $description;
    /**
     * @ORM\OneToOne(targetEntity="Organisation\Entity\Organisation", inversedBy="description", cascade="persist")
     * @ORM\JoinColumn(name="organisation_id", referencedColumnName="organisation_id", nullable=false, unique=true)
     *
     * @var \Organisation\Entity\Organisation
     */
    private $organisation;

    public function __toString(): string
    {
        return (string)$this->description;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Description
    {
        $this->id = $id;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): Description
    {
        $this->description = $description;
        return $this;
    }

    public function getOrganisation(): ?Organisation
    {
        return $this->organisation;
    }

    public function setOrganisation(?Organisation $organisation): Description
    {
        $this->organisation = $organisation;
        return $this;
    }
}
