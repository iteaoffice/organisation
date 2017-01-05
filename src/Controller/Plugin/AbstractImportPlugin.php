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

use Affiliation\Entity\Affiliation;
use Contact\Entity\Contact;
use Contact\Service\ContactService;
use Doctrine\ORM\EntityManager;
use General\Service\GeneralService;
use Organisation\Entity\OParent;
use Organisation\Entity\Organisation;
use Organisation\Entity\Parent\Organisation as ParentOrganisation;
use Organisation\Service\OrganisationService;
use Organisation\Service\ParentService;
use Program\Entity\Call\Call;
use Program\Entity\Program;
use Program\Service\CallService;
use Program\Service\ProgramService;
use Project\Entity\Funding\Funded;
use Project\Entity\Project;
use Project\Service\ProjectService;
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
     * @var Call[]
     */
    protected $call = [];
    /**
     * @var Program[]
     */
    protected $program = [];
    /**
     * @var Contact[]
     */
    protected $contact = [];
    /**
     * @var OParent[]
     */
    protected $parents = [];
    /**
     * @var Project[]
     */
    protected $project = [];
    /**
     * @var Organisation[]
     */
    protected $organisation = [];
    /**
     * @var Funded[]
     */
    protected $funding = [];
    /**
     * @var Organisation[]
     */
    protected $organisationByParent = [];
    /**
     * @var ParentOrganisation[]
     */
    protected $parentOrganisation = [];
    /**
     * @var Affiliation[]
     */
    protected $parentAffiliation = [];
    /**
     * @var Parent[]
     */
    protected $importedParents = [];
    /**
     * @var Call[]
     */
    protected $importedCall = [];
    /**
     * @var Program[]
     */
    protected $importedProgram = [];
    /**
     * @var Contact[]
     */
    protected $importedContact = [];
    /**
     * @var Project[]
     */
    protected $importedProject = [];
    /**
     * @var Organisation[]
     */
    protected $importedOrganisation = [];
    /**
     * @var Organisation[]
     */
    protected $importedOrganisationByParent = [];
    /**
     * @var ParentOrganisation[]
     */
    protected $importedParentOrganisation = [];
    /**
     * @var Affiliation[]
     */
    protected $importedAffiliation = [];
    /**
     * @var Funded[]
     */
    protected $importedFunding = [];
    /**
     * @var GeneralService
     */
    protected $generalService;
    /**
     * @var ProjectService
     */
    protected $projectService;
    /**
     * @var OrganisationService;
     */
    protected $organisationService;
    /**
     * @var ParentService
     */
    protected $parentService;
    /**
     * @var ContactService
     */
    protected $contactService;
    /**
     * @var ProgramService
     */
    protected $programService;
    /**
     * @var CallService
     */
    protected $callService;
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @param      $data
     * @param bool $doImport
     *
     * @return $this
     */
    public function __invoke($data, $doImport = false)
    {
        $this->setData($data);

        $this->validateData();


        if (! $this->hasErrors()) {
            $this->prepareContent();

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
     * @param bool $doImport
     */
    abstract public function prepareContent(bool $doImport = false);

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
     * @return Call[]
     */
    public function getCall(): array
    {
        return $this->call;
    }

    /**
     * @param Call[] $call
     *
     * @return AbstractImportPlugin
     */
    public function setCall(array $call): AbstractImportPlugin
    {
        $this->call = $call;

        return $this;
    }

    /**
     * @return Program[]
     */
    public function getProgram(): array
    {
        return $this->program;
    }

    /**
     * @param Program[] $program
     *
     * @return AbstractImportPlugin
     */
    public function setProgram(array $program): AbstractImportPlugin
    {
        $this->program = $program;

        return $this;
    }

    /**
     * @return Contact[]
     */
    public function getContact(): array
    {
        return $this->contact;
    }

    /**
     * @param Contact[] $contact
     *
     * @return AbstractImportPlugin
     */
    public function setContact(array $contact): AbstractImportPlugin
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * @return OParent[]
     */
    public function getParents(): array
    {
        return $this->parents;
    }

    /**
     * @param OParent[] $parents
     *
     * @return AbstractImportPlugin
     */
    public function setParents(array $parents): AbstractImportPlugin
    {
        $this->parents = $parents;

        return $this;
    }

    /**
     * @return Project[]
     */
    public function getProject(): array
    {
        return $this->project;
    }

    /**
     * @param Project[] $project
     *
     * @return AbstractImportPlugin
     */
    public function setProject(array $project): AbstractImportPlugin
    {
        $this->project = $project;

        return $this;
    }

    /**
     * @return Organisation[]
     */
    public function getOrganisation(): array
    {
        return $this->organisation;
    }

    /**
     * @param Organisation[] $organisation
     *
     * @return AbstractImportPlugin
     */
    public function setOrganisation(array $organisation): AbstractImportPlugin
    {
        $this->organisation = $organisation;

        return $this;
    }

    /**
     * @return Funded[]
     */
    public function getFunding(): array
    {
        return $this->funding;
    }

    /**
     * @param Funded[] $funding
     *
     * @return AbstractImportPlugin
     */
    public function setFunding(array $funding): AbstractImportPlugin
    {
        $this->funding = $funding;

        return $this;
    }

    /**
     * @return Organisation[]
     */
    public function getOrganisationByParent(): array
    {
        return $this->organisationByParent;
    }

    /**
     * @param Organisation[] $organisationByParent
     *
     * @return AbstractImportPlugin
     */
    public function setOrganisationByParent(array $organisationByParent): AbstractImportPlugin
    {
        $this->organisationByParent = $organisationByParent;

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
     * @return Affiliation[]
     */
    public function getParentAffiliation(): array
    {
        return $this->parentAffiliation;
    }

    /**
     * @param Affiliation[] $parentAffiliation
     *
     * @return AbstractImportPlugin
     */
    public function setParentAffiliation(array $parentAffiliation): AbstractImportPlugin
    {
        $this->parentAffiliation = $parentAffiliation;

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
     * @return Call[]
     */
    public function getImportedCall(): array
    {
        return $this->importedCall;
    }

    /**
     * @param Call[] $importedCall
     *
     * @return AbstractImportPlugin
     */
    public function setImportedCall(array $importedCall): AbstractImportPlugin
    {
        $this->importedCall = $importedCall;

        return $this;
    }

    /**
     * @return Program[]
     */
    public function getImportedProgram(): array
    {
        return $this->importedProgram;
    }

    /**
     * @param Program[] $importedProgram
     *
     * @return AbstractImportPlugin
     */
    public function setImportedProgram(array $importedProgram): AbstractImportPlugin
    {
        $this->importedProgram = $importedProgram;

        return $this;
    }

    /**
     * @return Contact[]
     */
    public function getImportedContact(): array
    {
        return $this->importedContact;
    }

    /**
     * @param Contact[] $importedContact
     *
     * @return AbstractImportPlugin
     */
    public function setImportedContact(array $importedContact): AbstractImportPlugin
    {
        $this->importedContact = $importedContact;

        return $this;
    }

    /**
     * @return Project[]
     */
    public function getImportedProject(): array
    {
        return $this->importedProject;
    }

    /**
     * @param Project[] $importedProject
     *
     * @return AbstractImportPlugin
     */
    public function setImportedProject(array $importedProject): AbstractImportPlugin
    {
        $this->importedProject = $importedProject;

        return $this;
    }

    /**
     * @return Organisation[]
     */
    public function getImportedOrganisation(): array
    {
        return $this->importedOrganisation;
    }

    /**
     * @param Organisation[] $importedOrganisation
     *
     * @return AbstractImportPlugin
     */
    public function setImportedOrganisation(array $importedOrganisation): AbstractImportPlugin
    {
        $this->importedOrganisation = $importedOrganisation;

        return $this;
    }

    /**
     * @return Organisation[]
     */
    public function getImportedOrganisationByParent(): array
    {
        return $this->importedOrganisationByParent;
    }

    /**
     * @param Organisation[] $importedOrganisationByParent
     *
     * @return AbstractImportPlugin
     */
    public function setImportedOrganisationByParent(array $importedOrganisationByParent): AbstractImportPlugin
    {
        $this->importedOrganisationByParent = $importedOrganisationByParent;

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

    /**
     * @return Affiliation[]
     */
    public function getImportedAffiliation(): array
    {
        return $this->importedAffiliation;
    }

    /**
     * @param Affiliation[] $importedAffiliation
     *
     * @return AbstractImportPlugin
     */
    public function setImportedAffiliation(array $importedAffiliation): AbstractImportPlugin
    {
        $this->importedAffiliation = $importedAffiliation;

        return $this;
    }

    /**
     * @return Funded[]
     */
    public function getImportedFunding(): array
    {
        return $this->importedFunding;
    }

    /**
     * @param Funded[] $importedFunding
     *
     * @return AbstractImportPlugin
     */
    public function setImportedFunding(array $importedFunding): AbstractImportPlugin
    {
        $this->importedFunding = $importedFunding;

        return $this;
    }

    /**
     * @return GeneralService
     */
    public function getGeneralService(): GeneralService
    {
        return $this->generalService;
    }

    /**
     * @param GeneralService $generalService
     *
     * @return AbstractImportPlugin
     */
    public function setGeneralService(GeneralService $generalService): AbstractImportPlugin
    {
        $this->generalService = $generalService;

        return $this;
    }

    /**
     * @return OrganisationService
     */
    public function getOrganisationService(): OrganisationService
    {
        return $this->organisationService;
    }

    /**
     * @param OrganisationService $organisationService
     *
     * @return AbstractImportPlugin
     */
    public function setOrganisationService(OrganisationService $organisationService): AbstractImportPlugin
    {
        $this->organisationService = $organisationService;

        return $this;
    }

    /**
     * @return ParentService
     */
    public function getParentService(): ParentService
    {
        return $this->parentService;
    }

    /**
     * @param ParentService $parentService
     *
     * @return AbstractImportPlugin
     */
    public function setParentService(ParentService $parentService): AbstractImportPlugin
    {
        $this->parentService = $parentService;

        return $this;
    }

    /**
     * @return ContactService
     */
    public function getContactService(): ContactService
    {
        return $this->contactService;
    }

    /**
     * @param ContactService $contactService
     *
     * @return AbstractImportPlugin
     */
    public function setContactService(ContactService $contactService): AbstractImportPlugin
    {
        $this->contactService = $contactService;

        return $this;
    }

    /**
     * @return ProgramService
     */
    public function getProgramService(): ProgramService
    {
        return $this->programService;
    }

    /**
     * @param ProgramService $programService
     *
     * @return AbstractImportPlugin
     */
    public function setProgramService(ProgramService $programService): AbstractImportPlugin
    {
        $this->programService = $programService;

        return $this;
    }

    /**
     * @return CallService
     */
    public function getCallService(): CallService
    {
        return $this->callService;
    }

    /**
     * @param CallService $callService
     *
     * @return AbstractImportPlugin
     */
    public function setCallService(CallService $callService): AbstractImportPlugin
    {
        $this->callService = $callService;

        return $this;
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager(): EntityManager
    {
        return $this->entityManager;
    }

    /**
     * @param EntityManager $entityManager
     *
     * @return AbstractImportPlugin
     */
    public function setEntityManager(EntityManager $entityManager): AbstractImportPlugin
    {
        $this->entityManager = $entityManager;

        return $this;
    }

    /**
     * @return ProjectService
     */
    public function getProjectService(): ProjectService
    {
        return $this->projectService;
    }

    /**
     * @param ProjectService $projectService
     *
     * @return AbstractImportPlugin
     */
    public function setProjectService(ProjectService $projectService): AbstractImportPlugin
    {
        $this->projectService = $projectService;

        return $this;
    }
}
