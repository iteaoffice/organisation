<?php
/**
 * ITEA Office all rights reserved
 *
 * @category   Project
 *
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license    https://itea3.org/license.txt proprietary
 *
 * @link       https://itea3.org
 */

declare(strict_types=1);

namespace Organisation\Entity\Parent;

use Doctrine\ORM\Mapping as ORM;
use Organisation\Entity\AbstractEntity;

/**
 * @ORM\Table(name="organisation_parent_doa_object")
 * @ORM\Entity
 */
class DoaObject extends AbstractEntity
{
    /**
     * @ORM\Column(name="object_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @var int
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="Organisation\Entity\Parent\Doa", inversedBy="object", cascade={"persist"})
     * @ORM\JoinColumn(name="doa_id", referencedColumnName="doa_id", nullable=false)
     *
     * @var Doa
     */
    private $doa;
    /**
     * @ORM\Column(name="object", type="blob", nullable=false)
     *
     * @var resource
     */
    private $object;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): DoaObject
    {
        $this->id = $id;
        return $this;
    }

    public function getDoa(): ?Doa
    {
        return $this->doa;
    }

    public function setDoa(?Doa $doa): DoaObject
    {
        $this->doa = $doa;
        return $this;
    }

    public function getObject()
    {
        return $this->object;
    }

    public function setObject($object): DoaObject
    {
        $this->object = $object;
        return $this;
    }
}
