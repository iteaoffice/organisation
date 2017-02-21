<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Project
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

namespace Organisation\Controller\Plugin;

use DoctrineORMModule\Proxy\__CG__\Organisation\Entity\ParentOrganisation;
use General\Entity\Country;
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
     * @var OParent[]
     */
    protected $parents = [];
    /**
     * @var ParentOrganisation[]
     */
    protected $parentOrganisation = [];
    /**
     * @var Parent[]
     */
    protected $importedParents = [];
    /**
     * @var ParentOrganisation[]
     */
    protected $importedParentOrganisation = [];


    /**
     * @param string $data
     * @param array $keys
     * @param bool $doImport
     *
     * @return AbstractImportPlugin
     */
    public function __invoke(string $data, array $keys = [], $doImport = false): AbstractOrganisationPlugin
    {
        $this->setData($data);

        $this->validateData();


        if (!$this->hasErrors()) {
            $this->prepareContent($keys);

            if ($doImport) {
                $this->getEntityManager()->flush();
            }
        }

        return $this;
    }

    /**
     * @param $data
     *
     * @return void
     */
    abstract public function setData(string $data);

    /**
     * With this function we will do some basic testing to see if the least amount of information is available.
     */
    abstract public function validateData();

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
     * @return bool
     */
    public function hasErrors(): bool
    {
        return count($this->errors) > 0;
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
     * @return ParentOrganisation[]
     */
    public function getParentOrganisation(): array
    {
        return $this->parentOrganisation;
    }

    /**
     * @param ParentOrganisation[] $parentOrganisation
     *
     * @return AbstractImportPlugin
     */
    public function setParentOrganisation(array $parentOrganisation): AbstractImportPlugin
    {
        $this->parentOrganisation = $parentOrganisation;

        return $this;
    }

    /**
     * @return OParent[]
     */
    public function getImportedParents(): array
    {
        return $this->importedParents;
    }

    /**
     * @param array $importedParents
     *
     * @return AbstractImportPlugin
     */
    public function setImportedParents(array $importedParents): AbstractImportPlugin
    {
        $this->importedParents = $importedParents;

        return $this;
    }

    /**
     * @return ParentOrganisation[]
     */
    public function getImportedParentOrganisation(): array
    {
        return $this->importedParentOrganisation;
    }

    /**
     * @param ParentOrganisation[] $importedParentOrganisation
     *
     * @return AbstractImportPlugin
     */
    public function setImportedParentOrganisation(array $importedParentOrganisation): AbstractImportPlugin
    {
        $this->importedParentOrganisation = $importedParentOrganisation;

        return $this;
    }
}
