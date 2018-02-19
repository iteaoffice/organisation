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
use General\Entity\Country;
use Organisation\Entity;
use Organisation\Entity\OParent;
use Organisation\Entity\Organisation;
use Organisation\Entity\Type;

/**
 * Class HandleImport.
 */
abstract class AbstractImportPlugin extends AbstractOrganisationPlugin
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
     * @param string $data
     * @param array $keys
     * @param bool $doImport
     * @return AbstractOrganisationPlugin
     */
    public function __invoke(string $data, array $keys = [], $doImport = false): AbstractOrganisationPlugin
    {
        $this->setData($data);

        $this->validateData();


        if (!$this->hasErrors()) {
            $this->prepareContent($keys);
        }

        return $this;
    }

    /**
     * @param $data
     */
    abstract public function setData(string $data);

    /**
     * With this function we will do some basic testing to see if the least amount of information is available.
     */
    abstract public function validateData();

    /**
     * @return bool
     */
    public function hasErrors(): bool
    {
        return \count($this->errors) > 0;
    }

    /**
     * @param array $keys
     *
     * @return mixed
     */
    abstract public function prepareContent(array $keys = []);

    /**
     * @param string $name
     * @param Country $country
     *
     * @return Organisation
     */
    public function createOrganisation(string $name, Country $country): Organisation
    {
        /** @var Type $type */
        $type = $this->getOrganisationService()->findEntityById(Type::class, Type::TYPE_UNKNOWN);

        $organisation = new Organisation();
        $organisation->setOrganisation($name);
        $organisation->setType($type);
        $organisation->setCountry($country);

        $this->getEntityManager()->persist($organisation);

        return $organisation;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @return bool
     */
    public function hasWarnings(): bool
    {
        return count($this->warnings) > 0;
    }

    /**
     * @return array
     */
    public function getWarnings(): array
    {
        return $this->warnings;
    }

    /**
     * @return string
     */
    public function getDelimiter(): string
    {
        return $this->delimiter;
    }

    /**
     * @param string $delimiter
     *
     * @return AbstractImportPlugin
     */
    public function setDelimiter(string $delimiter): AbstractImportPlugin
    {
        $this->delimiter = $delimiter;

        return $this;
    }

    /**
     * @return array
     */
    public function getHeader(): array
    {
        return $this->header;
    }

    /**
     * @param array $header
     *
     * @return AbstractImportPlugin
     */
    public function setHeader(array $header): AbstractImportPlugin
    {
        $this->header = $header;

        return $this;
    }

    /**
     * @return array
     */
    public function getHeaderKeys(): array
    {
        return $this->headerKeys;
    }

    /**
     * @param array $headerKeys
     *
     * @return AbstractImportPlugin
     */
    public function setHeaderKeys(array $headerKeys): AbstractImportPlugin
    {
        $this->headerKeys = $headerKeys;

        return $this;
    }

    /**
     * @return array
     */
    public function getContent(): array
    {
        return $this->content;
    }

    /**
     * @param array $content
     *
     * @return AbstractImportPlugin
     */
    public function setContent(array $content): AbstractImportPlugin
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return Affiliation[]
     */
    public function getAffiliation(): array
    {
        return $this->affiliation;
    }

    /**
     * @return Affiliation[]
     */
    public function getImportedAffiliation(): array
    {
        return $this->importedAffiliation;
    }
}
