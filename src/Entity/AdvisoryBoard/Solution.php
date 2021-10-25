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
 * @ORM\Table(name="organisation_advisory_board_solution")
 * @ORM\Entity
 * @Annotation\Hydrator("Laminas\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("organisation_advisory_board_solution")
 */
class Solution extends AbstractEntity
{
    public const HIDDEN_NO  = 0;
    public const HIDDEN_YES = 1;

    private static array $hiddenTemplates = [
        self::HIDDEN_NO  => 'txt-visible',
        self::HIDDEN_YES => 'txt-hidden',
    ];

    /**
     * @ORM\Column(name="solution_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Type("\Laminas\Form\Element\Hidden")
     */
    private ?int $id = null;
    /**
     * @ORM\Column(name="title")
     * @Annotation\Type("\Laminas\Form\Element\Text")
     * @Annotation\Options({"label":"txt-advisory-board-solution-title-label","help-block":"txt-advisory-board-solution-title-help-block"})
     * @Annotation\Attributes({"placeholder":"txt-advisory-board-solution-title-placeholder"})
     */
    private string $title = '';
    /**
     * @ORM\Column(name="docref", type="string", unique=true)
     * @Gedmo\Slug(fields={"title"})
     * @Annotation\Exclude()
     */
    private ?string $docRef = null;
    /**
     * @ORM\Column(name="`hidden`", type="smallint", nullable=false)
     * @Annotation\Type("Laminas\Form\Element\Radio")
     * @Annotation\Attributes({"array":"hiddenTemplates"})
     * @Annotation\Options({"label":"txt-advisory-board-solution-hidden-label","help-block":"txt-advisory-board-solution-hidden-help-block"})
     */
    private int $hidden = self::HIDDEN_NO;
    /**
     * @ORM\Column(name="description")
     * @Annotation\Type("\Laminas\Form\Element\Textarea")
     * @Annotation\Options({"label":"txt-advisory-board-solution-description-label","help-block":"txt-advisory-board-solution-description-help-block"})
     * @Annotation\Attributes({"placeholder":"txt-advisory-board-solution-description-placeholder"})
     */
    private string $description = '';
    /**
     * @ORM\Column(name="targeted_customers")
     * @Annotation\Type("\Laminas\Form\Element\Textarea")
     * @Annotation\Options({"label":"txt-advisory-board-solution-targeted-customers-label","help-block":"txt-advisory-board-solution-targeted-customers-help-block"})
     * @Annotation\Attributes({"placeholder":"txt-advisory-board-solution-targeted-customers-placeholder"})
     */
    private string $targetedCustomers = '';
    /**
     * @ORM\Column(name="condition_of_use")
     * @Annotation\Type("\Laminas\Form\Element\Textarea")
     * @Annotation\Options({"label":"txt-advisory-board-solution-condition-of-use-label","help-block":"txt-advisory-board-solution-condition-of-use-help-block"})
     * @Annotation\Attributes({"placeholder":"txt-advisory-board-solution-condition-of-use-placeholder"})
     */
    private string $conditionOfUse = '';
    /**
     * @ORM\Column(name="website",nullable=true)
     * @Annotation\Type("\Laminas\Form\Element\Url")
     * @Annotation\Options({"label":"txt-advisory-board-solution-website-label","help-block":"txt-advisory-board-solution-website-help-block"})
     * @Annotation\Attributes({"placeholder":"txt-advisory-board-solution-website-placeholder"})
     */
    private ?string $website = null;
    /**
     * @ORM\Column(name="date_created", type="datetime")
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
     * @ORM\OneToOne (targetEntity="Organisation\Entity\AdvisoryBoard\Solution\Image", cascade={"persist","remove"}, mappedBy="solution")
     * @Annotation\Exclude()
     */
    private ?Solution\Image $image = null;
    /**
     * @ORM\ManyToOne(targetEntity="Contact\Entity\Contact", cascade={"persist"}, inversedBy="advisoryBoardSolutions")
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id", nullable=true)
     * @Annotation\Type("Contact\Form\Element\Contact")
     * @Annotation\Options({"label":"txt-advisory-board-solution-contact-label","help-block":"txt-advisory-board-solution-contact-help-block"})
     */
    private ?Contact $contact = null;
    /**
     * @ORM\ManyToOne(targetEntity="Project\Entity\Project", cascade={"persist"}, inversedBy="advisoryBoardSolutions")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="project_id", nullable=true)
     * @Annotation\Type("DoctrineORMModule\Form\Element\EntitySelect")
     * @Annotation\Options({
     *     "target_class":"Project\Entity\Project",
     *     "empty_option":"— Choose a project",
     *     "allow_empty":true,
     *      "find_method":{
     *          "name":"findProjectsForForm",
     *          "params": {
     *              "criteria":{},
     *              "orderBy":{
     *                  "id":"DESC"}
     *              }
     *          }
     *      }
     * )
     * @Annotation\Options({"label":"txt-advisory-board-solution-project-label","help-block":"txt-advisory-board-solution-project-help-block"})
     */
    private ?\Project\Entity\Project $project = null;

    public static function getHiddenTemplates(): array
    {
        return self::$hiddenTemplates;
    }

    public function isHidden(): bool
    {
        return $this->hidden === self::HIDDEN_YES;
    }

    public function hasProject(): bool
    {
        return null !== $this->project;
    }

    public function hasImage(): bool
    {
        return null !== $this->image;
    }

    public function __toString(): string
    {
        return (string)$this->title;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Solution
    {
        $this->id = $id;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): Solution
    {
        $this->title = $title;
        return $this;
    }

    public function getDocRef(): ?string
    {
        return $this->docRef;
    }

    public function setDocRef(?string $docRef): Solution
    {
        $this->docRef = $docRef;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): Solution
    {
        $this->description = $description;
        return $this;
    }

    public function getTargetedCustomers(): ?string
    {
        return $this->targetedCustomers;
    }

    public function setTargetedCustomers(?string $targetedCustomers): Solution
    {
        $this->targetedCustomers = $targetedCustomers;
        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): Solution
    {
        $this->website = $website;
        return $this;
    }

    public function getDateCreated(): ?DateTime
    {
        return $this->dateCreated;
    }

    public function setDateCreated(?DateTime $dateCreated): Solution
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    public function getDateUpdated(): ?DateTime
    {
        return $this->dateUpdated;
    }

    public function setDateUpdated(?DateTime $dateUpdated): Solution
    {
        $this->dateUpdated = $dateUpdated;
        return $this;
    }

    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    public function setContact(?Contact $contact): Solution
    {
        $this->contact = $contact;
        return $this;
    }

    public function getConditionOfUse(): string
    {
        return $this->conditionOfUse;
    }

    public function setConditionOfUse(string $conditionOfUse): Solution
    {
        $this->conditionOfUse = $conditionOfUse;
        return $this;
    }

    public function getProject(): ?\Project\Entity\Project
    {
        return $this->project;
    }

    public function setProject(?\Project\Entity\Project $project): Solution
    {
        $this->project = $project;
        return $this;
    }

    public function getImage(): ?Solution\Image
    {
        return $this->image;
    }

    public function setImage(?Solution\Image $image): Solution
    {
        $this->image = $image;
        return $this;
    }

    public function getHidden(): ?int
    {
        return $this->hidden;
    }

    public function setHidden($hidden): Solution
    {
        $this->hidden = $hidden;

        return $this;
    }

    public function getHiddenText(): string
    {
        return self::$hiddenTemplates[$this->hidden] ?? '';
    }
}
