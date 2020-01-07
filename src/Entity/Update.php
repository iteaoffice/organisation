<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Organisation\Entity;

use Contact\Entity\Contact;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Laminas\Form\Annotation;

/**
 * @ORM\Table(name="organisation_update")
 * @ORM\Entity()
 * @Annotation\Name("organisation_update")
 */
class Update extends AbstractEntity
{
    /**
     * @ORM\Column(name="update_id", type="integer", options={"unsigned":true}, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Exclude()
     *
     * @var integer
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="Contact\Entity\Contact", inversedBy="organisationUpdates")
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id", nullable=false)
     * @Annotation\Exclude()
     *
     * @var Contact
     */
    private $contact;
    /**
     * @ORM\ManyToOne(targetEntity="Organisation\Entity\Organisation",inversedBy="updates")
     * @ORM\JoinColumn(name="organisation_id", referencedColumnName="organisation_id", nullable=false)
     * @Annotation\Exclude()
     *
     * @var Organisation
     */
    private $organisation;
    /**
     * @ORM\ManyToOne(targetEntity="Organisation\Entity\Type", inversedBy="organisationUpdates")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="type_id", nullable=true)
     * @Annotation\Type("DoctrineORMModule\Form\Element\EntitySelect")
     * @Annotation\Options({
     *      "target_class":"Organisation\Entity\Type",
     *      "find_method":{
     *          "name":"findBy",
     *          "params": {
     *              "criteria":{},
     *              "orderBy":{"type":"ASC"}
     *          }
     *      }
     * })
     * @Annotation\Attributes({"label":"txt-organisation-type"})
     *
     * @var Type
     */
    private $type;
    /**
     * @ORM\OneToOne(targetEntity="Organisation\Entity\UpdateLogo", mappedBy="update", cascade={"persist","remove"})
     *
     * @var UpdateLogo
     */
    private $logo;
    /**
     * @ORM\Column(name="description", type="text", length=65535, nullable=false)
     * @Annotation\Type("\Laminas\Form\Element\Textarea")
     * @Annotation\Options({
     *     "label":"txt-organisation-description-label",
     *     "help-block":"txt-organisation-description-help-block"
     * })
     * @Annotation\Attributes({"placeholder":"txt-organisation-description-placeholder","rows":10})
     *
     * @var string
     */
    private $description;
    /**
     * @ORM\Column(name="date_created", type="datetime", nullable=false)
     * @Gedmo\Timestampable(on="create")
     * @Annotation\Exclude()
     *
     * @var DateTime
     */
    private $dateCreated;
    /**
     * @ORM\Column(name="date_approved", type="datetime", nullable=true)
     * @Annotation\Exclude()
     *
     * @var DateTime
     */
    private $dateApproved;

    public function isApproved(): bool
    {
        return null !== $this->dateApproved;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): Update
    {
        $this->id = $id;
        return $this;
    }

    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    public function setContact(Contact $contact): Update
    {
        $this->contact = $contact;
        return $this;
    }

    public function getOrganisation(): ?Organisation
    {
        return $this->organisation;
    }

    public function setOrganisation(Organisation $organisation): Update
    {
        $this->organisation = $organisation;
        return $this;
    }

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(Type $type): Update
    {
        $this->type = $type;
        return $this;
    }

    public function getLogo(): ?UpdateLogo
    {
        return $this->logo;
    }

    public function setLogo(UpdateLogo $logo): Update
    {
        $this->logo = $logo;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): Update
    {
        $this->description = $description;
        return $this;
    }

    public function getDateCreated(): ?DateTime
    {
        return $this->dateCreated;
    }

    public function setDateCreated(DateTime $dateCreated): Update
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    public function getDateApproved(): ?DateTime
    {
        return $this->dateApproved;
    }

    public function setDateApproved(DateTime $dateApproved): Update
    {
        $this->dateApproved = $dateApproved;
        return $this;
    }
}
