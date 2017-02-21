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
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

namespace Organisation\Entity\Parent;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use DoctrineORMModule\Proxy\__CG__\Project\Entity\Fee;
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
 */
class Status extends AbstractEntity
{
    const STATUS_MEMBER = 1;
    const STATUS_FREE_RIDER = 3;
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
     * @var string
     */
    private $status;
    /**
     * @ORM\Column(name="description", type="string", nullable=false)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-organisation-description-label","help-block":"txt-organisation-description-help-block"})
     * @var string
     */
    private $description;
    /**
     * @ORM\OneToMany(targetEntity="Organisation\Entity\OParent", cascade={"persist"}, mappedBy="status")
     * @Annotation\Exclude()
     *
     * @var \Organisation\Entity\OParent[]|ArrayCollection
     */
    private $parent;
    /**
     * @ORM\ManyToMany(targetEntity="Project\Entity\Fee", cascade={"persist"}, mappedBy="parentStatus")
     * @Annotation\Exclude()
     *
     * @var Fee[]|ArrayCollection
     */
    private $projectFee;

    /**
     * OrganisationType constructor.
     */
    public function __construct()
    {
        $this->parent = new ArrayCollection();
        $this->projectFee = new ArrayCollection();
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
    public function getStatus()
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
    public function getDescription()
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
     * @return ArrayCollection|\Organisation\Entity\OParent[]
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param ArrayCollection|\Organisation\Entity\OParent[] $parent
     *
     * @return Status
     */
    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return ArrayCollection|Fee[]
     */
    public function getProjectFee()
    {
        return $this->projectFee;
    }

    /**
     * @param ArrayCollection|Fee[] $projectFee
     *
     * @return Status
     */
    public function setProjectFee($projectFee)
    {
        $this->projectFee = $projectFee;

        return $this;
    }
}
