<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Organisation\Entity\AdvisoryBoard;

use Contact\Entity\Contact;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Laminas\Form\Annotation;
use Organisation\Entity\AbstractEntity;

/**
 * @ORM\Table(name="organisation_advisory_board_tender")
 * @ORM\Entity
 * @Annotation\Hydrator("Laminas\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("organisation_advisory_board_tender")
 */
class Tender extends AbstractEntity
{
    /**
     * @ORM\Column(name="tender_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Type("\Laminas\Form\Element\Hidden")
     *
     * @var int
     */
    private $id;
    /**
     * @ORM\Column(name="title")
     * @Annotation\Type("\Laminas\Form\Element\Text")
     * @Annotation\Options({"label":"txt-advisory-board-tender-title-label","help-block":"txt-advisory-board-tender-title-help-block"})
     * @Annotation\Attributes({"placeholder":"txt-advisory-board-tender-title-placeholder"})
     *
     * @var string
     */
    private $title;
    /**
     * @ORM\Column(name="description")
     * @Annotation\Type("\Laminas\Form\Element\Textarea")
     * @Annotation\Options({"label":"txt-advisory-board-tender-description-label","help-block":"txt-advisory-board-tender-description-help-block"})
     * @Annotation\Attributes({"placeholder":"txt-advisory-board-tender-description-placeholder"})
     *
     * @var string
     */
    private $description;
    /**
     * @ORM\Column(name="website",nullable=true)
     * @Annotation\Type("\Laminas\Form\Element\Url")
     * @Annotation\Options({"label":"txt-advisory-board-tender-website-label","help-block":"txt-advisory-board-tender-website-help-block"})
     * @Annotation\Attributes({"placeholder":"txt-advisory-board-tender-website-placeholder"})
     *
     * @var string
     */
    private $website;
    /**
     * @ORM\Column(name="deadline", unique=true)
     * @Annotation\Type("\Laminas\Form\Element\Date")
     * @Annotation\Options({"label":"txt-advisory-board-tender-deadline-label","help-block":"txt-advisory-board-tender-deadline-help-block"})
     * @Annotation\Attributes({"placeholder":"txt-advisory-board-tender-deadline-placeholder"})
     *
     * @var string
     */
    private $daadline;
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
     * @ORM\Column(name="date_approved", type="datetime", nullable=true)
     * @Annotation\Exclude()
     *
     * @var DateTime
     */
    private $dateApproved;
    /**
     * @ORM\ManyToOne(targetEntity="Organisation\Entity\AdvisoryBoard\Tender\Type", cascade={"persist"}, inversedBy="tenders")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="type_id", nullable=false)
     * @Annotation\Type("DoctrineORMModule\Form\Element\EntitySelect")
     * @Annotation\Options({
     *      "target_class":"Organisation\Entity\AdvisoryBoard\Tender\Type",
     *      "find_method":{
     *          "name":"findBy",
     *          "params": {
     *              "criteria":{},
     *              "orderBy":{
     *                  "type":"ASC"}
     *              }
     *          }
     *      }
     * )
     * @Annotation\Options({"label":"txt-advisory-board-tender-type-label","help-block":"txt-advisory-board-tender-type-help-block"})
     *
     * @var \Organisation\Entity\AdvisoryBoard\Tender\Type
     */
    private $type;
    /**
     * @ORM\ManyToOne(targetEntity="Organisation\Entity\AdvisoryBoard\City", cascade={"persist"}, inversedBy="advisoryBoardTenders")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="city_id", nullable=false)
     * @Annotation\Type("DoctrineORMModule\Form\Element\EntitySelect")
     * @Annotation\Options({
     *      "target_class":"Organisation\Entity\AdvisoryBoard\City",
     *      "find_method":{
     *          "name":"findBy",
     *          "params": {
     *              "criteria":{},
     *              "orderBy":{
     *                  "city":"ASC"}
     *              }
     *          }
     *      }
     * )
     * @Annotation\Options({"label":"txt-advisory-board-tender-city-label","help-block":"txt-advisory-board-tender-city-help-block"})
     *
     * @var City
     */
    private $city;
    /**
     * @ORM\ManyToOne(targetEntity="General\Entity\Language", cascade={"persist"}, inversedBy="advisoryBoardTenders")
     * @ORM\JoinColumn(name="language_id", referencedColumnName="language_id", nullable=false)
     * @Annotation\Type("DoctrineORMModule\Form\Element\EntitySelect")
     * @Annotation\Options({
     *      "target_class":"General\Entity\Language",
     *      "find_method":{
     *          "name":"findBy",
     *          "params": {
     *              "criteria":{},
     *              "orderBy":{
     *                  "city":"ASC"}
     *              }
     *          }
     *      }
     * )
     * @Annotation\Options({"label":"txt-advisory-board-tender-language-label","help-block":"txt-advisory-board-tender-language-help-block"})
     *
     * @var \General\Entity\Language
     */
    private $language;

    public function isApproved(): bool
    {
        return null !== $this->dateApproved;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Tender
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): Tender
    {
        $this->name = $name;
        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): Tender
    {
        $this->website = $website;
        return $this;
    }

    public function getDateCreated(): ?DateTime
    {
        return $this->dateCreated;
    }

    public function setDateCreated(?DateTime $dateCreated): Tender
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    public function getDateUpdated(): ?DateTime
    {
        return $this->dateUpdated;
    }

    public function setDateUpdated(?DateTime $dateUpdated): Tender
    {
        $this->dateUpdated = $dateUpdated;
        return $this;
    }

    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    public function setContact(?Contact $contact): Tender
    {
        $this->contact = $contact;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): Tender
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): Tender
    {
        $this->description = $description;
        return $this;
    }

    public function getDaadline(): ?string
    {
        return $this->daadline;
    }

    public function setDaadline(?string $daadline): Tender
    {
        $this->daadline = $daadline;
        return $this;
    }

    public function getDateApproved(): ?DateTime
    {
        return $this->dateApproved;
    }

    public function setDateApproved(?DateTime $dateApproved): Tender
    {
        $this->dateApproved = $dateApproved;
        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): Tender
    {
        $this->city = $city;
        return $this;
    }

    public function getLanguage(): ?\General\Entity\Language
    {
        return $this->language;
    }

    public function setLanguage(?\General\Entity\Language $language): Tender
    {
        $this->language = $language;
        return $this;
    }

    public function getType(): ?Tender\Type
    {
        return $this->type;
    }

    public function setType(?Tender\Type $type): Tender
    {
        $this->type = $type;
        return $this;
    }
}
