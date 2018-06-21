<?php

/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Project
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        https://github.com/iteaoffice/organisation for the canonical source repository
 */

declare(strict_types=1);

namespace Organisation\Entity\Parent;

use Doctrine\Common\Collections;
use Doctrine\ORM\Mapping as ORM;
use Organisation\Entity\AbstractEntity;
use Zend\Form\Annotation;

/**
 * Entity for the Organisation.
 *
 * @ORM\Table(name="organisation_parent_status")
 * @ORM\Entity(repositoryClass="Organisation\Repository\Parent\Status")
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("organisation_parent_status")
 *
 * @category Organisation
 * @deprecated
 */
class Status extends AbstractEntity
{
    public const STATUS_MEMBER = 1;
    public const STATUS_APPLICANT = 2;
    public const STATUS_FREE_RIDER = 3;
    public const STATUS_PENTA_DOA = 5;
    public const STATUS_ECSEL_ENIAC_DOA = 7;
    public const STATUS_DOA_IA_MEMBER = 9;

    /**
     * @ORM\Column(name="status_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Type("\Zend\Form\Element\Hidden")
     *
     * @var int
     */
    private $id;
    /**
     * @ORM\Column(name="status", type="string", nullable=false)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-organisation-status-label","help-block":"txt-organisation-status-help-block"})
     * @Annotation\Attributes({"placeholder":"txt-organisation-status-placeholder"})
     * @var string
     */
    private $status;
    /**
     * @ORM\Column(name="description", type="string", nullable=false)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-organisation-status-description-label","help-block":"txt-organisation-status-description-help-block"})
     * @Annotation\Attributes({"placeholder":"txt-organisation-description-placeholder"})
     * @var string
     */
    private $description;
    /**
     * @ORM\OneToMany(targetEntity="Organisation\Entity\OParent", cascade={"persist"}, mappedBy="status")
     * @Annotation\Exclude()
     *
     * @var \Organisation\Entity\OParent[]|Collections\ArrayCollection
     */
    private $parent;

    /**
     * OrganisationType constructor.
     */
    public function __construct()
    {
        $this->parent = new Collections\ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->status;
    }

    /**
     * @param $property
     *
     * @return mixed
     */
    public function __get($property)
    {
        return $this->$property;
    }

    /**
     * @param $property
     * @param $value
     *
     * @return void
     */
    public function __set($property, $value)
    {
        $this->$property = $value;
    }

    /**
     * @param $property
     *
     * @return bool
     */
    public function __isset($property)
    {
        return isset($this->$property);
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
     * @return Status
     */
    public function setId($id): Status
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param string $status
     *
     * @return Status
     */
    public function setStatus(string $status): Status
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return Status
     */
    public function setDescription(string $description): Status
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Organisation\Entity\OParent[]
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param Collections\ArrayCollection|\Organisation\Entity\OParent[] $parent
     *
     * @return Status
     */
    public function setParent($parent): Status
    {
        $this->parent = $parent;

        return $this;
    }
}
