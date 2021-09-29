<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Organisation\Entity\AdvisoryBoard\Solution;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use General\Entity\AbstractEntity;
use General\Entity\ContentType;

/**
 * @ORM\Table(name="organisation_advisory_board_solution_image")
 * @ORM\Entity
 */
class Image extends AbstractEntity
{
    /**
     * @ORM\Column(name="image_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $id;
    /**
     * @ORM\ManyToOne(targetEntity="General\Entity\ContentType", cascade={"persist"}, inversedBy="advisoryBoardSolutionImage")
     * @ORM\JoinColumn(name="contenttype_id", referencedColumnName="contenttype_id", nullable=false)
     */
    private ContentType $contentType;
    /**
     * @ORM\Column(name="image", type="blob", nullable=false)
     */
    private string $image;
    /**
     * @ORM\Column(name="date_created", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private DateTime $dateCreated;
    /**
     * @ORM\Column(name="date_updated", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     */
    private ?DateTime $dateUpdated = null;
    /**
     * @ORM\OneToOne(targetEntity="Organisation\Entity\AdvisoryBoard\Solution", cascade={"persist"}, inversedBy="image")
     * @ORM\JoinColumn(name="solution_id", referencedColumnName="solution_id", nullable=false)
     */
    private \Organisation\Entity\AdvisoryBoard\Solution $solution;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Image
    {
        $this->id = $id;
        return $this;
    }

    public function getContentType(): ?ContentType
    {
        return $this->contentType;
    }

    public function setContentType(?ContentType $contentType): Image
    {
        $this->contentType = $contentType;
        return $this;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image): Image
    {
        $this->image = $image;
        return $this;
    }

    public function getDateUpdated(): ?DateTime
    {
        return $this->dateUpdated;
    }

    public function setDateUpdated(?DateTime $dateUpdated): Image
    {
        $this->dateUpdated = $dateUpdated;
        return $this;
    }

    public function getDateCreated(): ?DateTime
    {
        return $this->dateCreated;
    }

    public function setDateCreated(?DateTime $dateCreated): Image
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    public function getSolution(): ?\Organisation\Entity\AdvisoryBoard\Solution
    {
        return $this->solution;
    }

    public function setSolution(?\Organisation\Entity\AdvisoryBoard\Solution $solution): Image
    {
        $this->solution = $solution;
        return $this;
    }
}
