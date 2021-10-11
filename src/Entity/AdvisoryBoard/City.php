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
use General\Entity\Country;
use Laminas\Form\Annotation;
use Organisation\Entity\AbstractEntity;

/**
 * @ORM\Table(name="organisation_advisory_board_city")
 * @ORM\Entity
 * @Annotation\Hydrator("Laminas\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("organisation_advisory_board_city")
 */
class City extends AbstractEntity
{
    /**
     * @ORM\Column(name="city_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Type("\Laminas\Form\Element\Hidden")
     */
    private ?int $id = null;
    /**
     * @ORM\Column(name="name", unique=true)
     * @Annotation\Type("\Laminas\Form\Element\Text")
     * @Annotation\Options({"label":"txt-advisory-board-city-name-label","help-block":"txt-advisory-board-city-name-help-block"})
     * @Annotation\Attributes({"placeholder":"txt-advisory-board-city-name-placeholder"})
     */
    private string $name = '';
    /**
     * @ORM\Column(name="docref", type="string", nullable=true, unique=true)
     * @Gedmo\Slug(fields={"name"})
     * @Annotation\Exclude()
     */
    private string $docRef = '';
    /**
     * @ORM\Column(name="website",nullable=true)
     * @Annotation\Type("\Laminas\Form\Element\Url")
     * @Annotation\Options({"label":"txt-advisory-board-city-website-label","help-block":"txt-advisory-board-city-website-help-block"})
     * @Annotation\Attributes({"placeholder":"txt-advisory-board-city-website-placeholder"})
     */
    private ?string $website = null;
    /**
     * @ORM\Column(name="tender_website",nullable=true)
     * @Annotation\Type("\Laminas\Form\Element\Url")
     * @Annotation\Options({"label":"txt-advisory-board-city-tender-website-label","help-block":"txt-advisory-board-city-tender-website-help-block"})
     * @Annotation\Attributes({"placeholder":"txt-advisory-board-city-tender-website-placeholder"})
     */
    private ?string $tenderWebsite = null;
    /**
     * @ORM\Column(name="date_created", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="create")
     * @Annotation\Exclude()
     */
    private ?DateTime $dateCreated = null;
    /**
     * @ORM\Column(name="date_updated", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     * @Annotation\Exclude()
     */
    private ?DateTime $dateUpdated = null;
    /**
     * @ORM\ManyToOne(targetEntity="Contact\Entity\Contact", cascade={"persist"}, inversedBy="advisoryBoardCities")
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id", nullable=true)
     * @Annotation\Type("Contact\Form\Element\Contact")
     * @Annotation\Options({"label":"txt-advisory-board-city-contact-label","help-block":"txt-advisory-board-city-contact-help-block"})
     */
    private ?Contact $contact = null;
    /**
     * @ORM\OneToOne(targetEntity="Organisation\Entity\AdvisoryBoard\City\Image", cascade={"persist","remove"}, mappedBy="city")
     * @Annotation\Exclude()
     */
    private ?City\Image $image = null;
    /**
     * @ORM\ManyToOne(targetEntity="General\Entity\Country", cascade={"persist"}, inversedBy="advisoryBoardCities")
     * @ORM\JoinColumn(name="country_id", referencedColumnName="country_id", nullable=false)
     * @Annotation\Type("DoctrineORMModule\Form\Element\EntitySelect")
     * @Annotation\Options({
     *      "target_class":"General\Entity\Country",
     *      "find_method":{
     *          "name":"findForForm",
     *          "params": {
     *              "criteria":{},
     *              "orderBy":{}
     *          }}
     *      }
     * )
     * @Annotation\Attributes({"label":"txt-advisory-board-city-country-label","help-block":"txt-advisory-board-city-country-help-block"})
     */
    private ?Country $country = null;

    public function __toString(): string
    {
        return (string)$this->name;
    }

    public function hasImage(): bool
    {
        return null !== $this->image;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): City
    {
        $this->id = $id;
        return $this;
    }

    public function getDocRef(): ?string
    {
        return $this->docRef;
    }

    public function setDocRef(?string $docRef): City
    {
        $this->docRef = $docRef;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): City
    {
        $this->name = $name;
        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): City
    {
        $this->website = $website;
        return $this;
    }

    public function getTenderWebsite(): ?string
    {
        return $this->tenderWebsite;
    }

    public function setTenderWebsite(?string $tenderWebsite): City
    {
        $this->tenderWebsite = $tenderWebsite;
        return $this;
    }

    public function getDateCreated(): ?DateTime
    {
        return $this->dateCreated;
    }

    public function setDateCreated(?DateTime $dateCreated): City
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    public function getDateUpdated(): ?DateTime
    {
        return $this->dateUpdated;
    }

    public function setDateUpdated(?DateTime $dateUpdated): City
    {
        $this->dateUpdated = $dateUpdated;
        return $this;
    }

    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    public function setContact(?Contact $contact): City
    {
        $this->contact = $contact;
        return $this;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): City
    {
        $this->country = $country;
        return $this;
    }

    public function getImage(): ?City\Image
    {
        return $this->image;
    }

    public function setImage(?City\Image $image): City
    {
        $this->image = $image;
        return $this;
    }
}
