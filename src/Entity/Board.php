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

use Cluster\Entity\Cluster;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Laminas\Form\Annotation;

/**
 * @ORM\Table(name="organisation_board")
 * @ORM\Entity(repositoryClass="Organisation\Repository\BoardRepository")
 * @Annotation\Hydrator("Laminas\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("organisation_board")
 */
class Board extends AbstractEntity
{
    /**
     * @ORM\Column(name="board_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Type("\Laminas\Form\Element\Hidden")
     *
     * @var int
     */
    private $id;
    /**
     * @ORM\Column(name="date_created", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="create")
     * @Annotation\Exclude()
     *
     * @var DateTime
     */
    private $dateCreated;
    /**
     * @ORM\Column(name="date_updated", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     * @Annotation\Exclude()
     *
     * @var DateTime
     */
    private $dateUpdated;
    /**
     * @ORM\Column(name="date_signed", type="date", nullable=false)
     * @Annotation\Type("\Laminas\Form\Element\Date")
     * @Annotation\Options({"help-block":"txt-organisation-board-date-signed-help-block"})
     * @Annotation\Attributes({"label":"txt-organisation-board-date-signed-label"})
     *
     * @var DateTime
     */
    private $dateSigned;
    /**
     * @ORM\Column(name="date_end", type="date", nullable=true)
     * @Annotation\Type("\Laminas\Form\Element\Date")
     * @Annotation\Options({"help-block":"txt-organisation-board-date-end-help-block"})
     * @Annotation\Attributes({"label":"txt-organisation-board-date-end-label"})
     *
     * @var DateTime
     */
    private $dateEnd;
    /**
     * @ORM\ManyToOne(targetEntity="Organisation\Entity\Organisation", cascade={"persist"}, inversedBy="board")
     * @ORM\JoinColumn(name="organisation_id", referencedColumnName="organisation_id", nullable=false)
     * @Annotation\Type("Organisation\Form\Element\OrganisationElement")
     * @Annotation\Options({"help-block":"txt-organisation-board-cluster-help-block"})
     * @Annotation\Attributes({"label":"txt-organisation-board-organisation-label"})
     *
     * @var Organisation
     */
    private $organisation;
    /**
     * @ORM\ManyToOne(targetEntity="Cluster\Entity\Cluster", cascade={"persist"}, inversedBy="board")
     * @ORM\JoinColumn(name="cluster_id", referencedColumnName="cluster_id", nullable=false)
     * @Annotation\Type("DoctrineORMModule\Form\Element\EntitySelect")
     * @Annotation\Options({
     *      "target_class":"Cluster\Entity\Cluster",
     *      "help-block":"txt-organisation-board-cluster-help-block",
     *      "find_method":{
     *          "name":"findAll",
     *          "params": {
     *              "criteria":{},
     *              "orderBy":{
     *                  "tag":"ASC"}
     *              }
     *          }
     *      }
     * )
     * @Annotation\Attributes({"label":"txt-organisation-board-cluster-label"})
     *
     * @var Cluster
     */
    private $cluster;

    public function __toString(): string
    {
        return (string)$this->organisation->parseFullName();
    }

    public function isActive(): bool
    {
        return null === $this->dateEnd;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Board
    {
        $this->id = $id;
        return $this;
    }

    public function getDateCreated(): ?DateTime
    {
        return $this->dateCreated;
    }

    public function setDateCreated(?DateTime $dateCreated): Board
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    public function getDateUpdated(): ?DateTime
    {
        return $this->dateUpdated;
    }

    public function setDateUpdated(?DateTime $dateUpdated): Board
    {
        $this->dateUpdated = $dateUpdated;
        return $this;
    }

    public function getDateSigned(): ?DateTime
    {
        return $this->dateSigned;
    }

    public function setDateSigned(?DateTime $dateSigned): Board
    {
        $this->dateSigned = $dateSigned;
        return $this;
    }

    public function getDateEnd(): ?DateTime
    {
        return $this->dateEnd;
    }

    public function setDateEnd(?DateTime $dateEnd): Board
    {
        $this->dateEnd = $dateEnd;
        return $this;
    }

    public function getOrganisation(): ?Organisation
    {
        return $this->organisation;
    }

    public function setOrganisation(?Organisation $organisation): Board
    {
        $this->organisation = $organisation;
        return $this;
    }

    public function getCluster(): ?Cluster
    {
        return $this->cluster;
    }

    public function setCluster(?Cluster $cluster): Board
    {
        $this->cluster = $cluster;
        return $this;
    }
}
