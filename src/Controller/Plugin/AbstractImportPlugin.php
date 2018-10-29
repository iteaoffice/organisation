<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Project
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Organisation\Controller\Plugin;

use Affiliation\Entity\Affiliation;
use Doctrine\ORM\EntityManager;
use General\Entity\Country;
use Organisation\Entity;
use Organisation\Entity\OParent;
use Organisation\Entity\Organisation;
use Organisation\Entity\Type;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * Class HandleImport.
 */
abstract class AbstractImportPlugin extends AbstractPlugin
{
    /**
     * @var string
     */
    protected $delimiter = "\t";
    /**
     * @var array
     */
    protected $header = [];
    /**
     * @var array
     */
    protected $headerKeys = [];
    /**
     * @var array
     */
    protected $keys = [];
    /**
     * @var array
     */
    protected $content = [];
    /**
     * @var array
     */
    protected $errors = [];
    /**
     * @var array
     */
    protected $warnings = [];
    /**
     * @var Affiliation[]
     */
    protected $affiliation = [];
    /**
     * @var Affiliation[]
     */
    protected $importedAffiliation = [];
    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(string $data, array $keys = [], $doImport = false): AbstractPlugin
    {
        $this->setData($data);

        $this->validateData();


        if (!$this->hasErrors()) {
            $this->prepareContent($keys);
        }

        return $this;
    }

    abstract public function setData(string $data);

    abstract public function validateData();

    public function hasErrors(): bool
    {
        return \count($this->errors) > 0;
    }

    abstract public function prepareContent(array $keys = []);

    public function createOrganisation(string $name, Country $country): Organisation
    {
        /** @var Type $type */
        $type = $this->entityManager->find(Type::class, Type::TYPE_UNKNOWN);

        $organisation = new Organisation();
        $organisation->setOrganisation($name);
        $organisation->setType($type);
        $organisation->setCountry($country);

        $this->entityManager->persist($organisation);

        return $organisation;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function hasWarnings(): bool
    {
        return count($this->warnings) > 0;
    }

    public function getWarnings(): array
    {
        return $this->warnings;
    }

    public function getDelimiter(): string
    {
        return $this->delimiter;
    }

    public function setDelimiter(string $delimiter): AbstractImportPlugin
    {
        $this->delimiter = $delimiter;

        return $this;
    }

    public function getHeader(): array
    {
        return $this->header;
    }

    public function setHeader(array $header): AbstractImportPlugin
    {
        $this->header = $header;

        return $this;
    }

    public function getHeaderKeys(): array
    {
        return $this->headerKeys;
    }

    public function setHeaderKeys(array $headerKeys): AbstractImportPlugin
    {
        $this->headerKeys = $headerKeys;

        return $this;
    }

    public function getContent(): array
    {
        return $this->content;
    }

    public function setContent(array $content): AbstractImportPlugin
    {
        $this->content = $content;

        return $this;
    }

    public function getAffiliation(): array
    {
        return $this->affiliation;
    }

    public function getImportedAffiliation(): array
    {
        return $this->importedAffiliation;
    }
}
