<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Organisation\Controller\Plugin;

use Affiliation\Entity\Affiliation;
use Contact\Entity\Contact;
use Contact\Service\ContactService;
use DateTime;
use Doctrine\ORM\EntityManager;
use General\Service\CountryService;
use Organisation\Entity\Name;
use Organisation\Entity\ParentEntity;
use Organisation\Entity\Organisation;
use Organisation\Entity\Parent\Doa;
use Organisation\Entity\Parent\Financial;
use Organisation\Entity\Parent\Type as ParentType;
use Organisation\Service\OrganisationService;
use Organisation\Service\ParentService;
use Program\Entity\Call\Call;
use Program\Entity\Program;
use Program\Service\CallService;
use Program\Service\ProgramService;
use Project\Entity\Funding\Funded;
use Project\Entity\Project;
use Project\Service\ProjectService;

use function array_flip;
use function array_map;
use function count;
use function explode;
use function in_array;
use function sprintf;
use function str_replace;
use function trim;

/**
 * Class HandleImport.
 */
final class HandleParentAndProjectImport extends AbstractImportPlugin
{
    private CountryService $countryService;
    private ParentService $parentService;
    private ProjectService $projectService;
    private ContactService $contactService;
    private OrganisationService $organisationService;
    private CallService $callService;
    private ProgramService $programService;

    public function __construct(
        EntityManager $entityManager,
        CountryService $countryService,
        ParentService $parentService,
        ProjectService $projectService,
        ContactService $contactService,
        OrganisationService $organisationService,
        CallService $callService,
        ProgramService $programService
    ) {
        parent::__construct($entityManager);

        $this->countryService      = $countryService;
        $this->parentService       = $parentService;
        $this->projectService      = $projectService;
        $this->contactService      = $contactService;
        $this->organisationService = $organisationService;
        $this->callService         = $callService;
        $this->programService      = $programService;
    }

    public function setData(string $sourceData): void
    {
        $data = trim($sourceData);

        //Explode first on the \n to have the different rows
        $data = explode(PHP_EOL, $data);

        //Apply a general trim to remove unwanted characters
        $data = array_map('trim', $data);

        $this->header = explode($this->delimiter, trim($data[0]));

        //Trim the header
        foreach ($this->header as $key => $header) {
            $this->header[$key] = trim(trim($header, '"'));
        }

        /*
         * Go over the rest of the data and add the rows to the array
         */
        $amount = count($data);
        for ($i = 1; $i < $amount; $i++) {
            $row = explode($this->delimiter, $data[$i]);

            if (count($row) >= count($this->header)) {
                //Trim all the elements
                $row = array_map('trim', $row);

                //Remove the "
                $row = array_map(
                    function ($element) {
                        return trim($element, '""');
                    },
                    $row
                );

                $this->content[] = $row;
            } else {
                $this->warnings[] = sprintf(
                    'Row %s has been skipped, does not contain %s elements but %s',
                    $i + 1,
                    count($this->header),
                    count($row)
                );
            }
        }
    }

    public function validateData(): bool
    {
        $minimalRequiredElements = [
            'Call',
            'Proposal Acronym',
            'Legal Name',
            'Parent',
            'EPS',
            'EU funding',
            'National funding',
            'Member Type',
            'Member AENEAS',
            'Member ARTEMISIA',
            'Member EPOSS',
            'AENEAS ECSEL DoA',
            'ARTEMISIA DoA',
            'EPoSS DoA'
        ];

        /*
         * Go over all elements and check if the required elements are present
         */
        foreach ($minimalRequiredElements as $element) {
            if (! in_array($element, $this->header, true)) {
                $this->errors[] = sprintf('Element %s is missing in the file', $element);
            }
        }

        /*
         * Create the lookup-table
         */
        $this->headerKeys = array_flip($this->header);

        /*
         * Validate the elements.
         */
        $counter = 2;

        foreach ($this->content as $content) {
            /**
             * Validate the country
             */
            if (empty($content[$this->headerKeys['EPS']])) {
                $this->errors[] = sprintf(
                    'EPS in row %s is empty',
                    $counter
                );
            } else {
                if (
                    null === $this->countryService->findCountryByCD($content[$this->headerKeys['EPS']])
                ) {
                    $this->errors[] = sprintf(
                        'EPS (%s) in row %s cannot be found',
                        $content[$this->headerKeys['EPS']],
                        $counter
                    );
                }
            }

            if (! empty($content[$this->headerKeys['Member Type']])) {
                //Try to find the status
                $type = $this->parentService->findParentTypeByName($content[$this->headerKeys['Member Type']]);

                if (null === $type) {
                    $this->errors[] = sprintf(
                        'Type (%s) in row %s (%s) cannot be found',
                        $content[$this->headerKeys['Member Type']],
                        $counter,
                        $content[$this->headerKeys['Legal Name']]
                    );
                }
            }

            $counter++;
        }

        return true;
    }

    public function prepareContent(array $keys = []): void
    {
        //First de-activate all the partners
        if (count($keys) > 0) {
            $projects = [];
            foreach ($this->content as $key => $content) {
                if (in_array($key, $keys, false)) {
                    /** @var Project $project */
                    $project = $this->projectService->findProjectByName(
                        $content[$this->headerKeys['Proposal Acronym']]
                    );

                    if (null !== $project) {
                        $projects[$project->getId()] = $project;
                    }
                }
            }

            //Disable all affiliations
            foreach ($projects as $project) {
                foreach ($project->getAffiliation() as $affiliation) {
                    $affiliation->setDateEnd(new DateTime());
                    $this->entityManager->persist($affiliation);
                }
            }
            $this->entityManager->flush();
        }

        foreach ($this->content as $key => $content) {
            $contact = $this->contactService->findContactById(1);

            //Find the country
            $country = $this->countryService->findCountryByCD($content[$this->headerKeys['EPS']]);


            if (null === $contact || null === $country) {
                continue;
            }

            //Extract some data form the callId
            [$programName, $year, $id] = explode('-', $content[$this->headerKeys['Call']]);

            $callName = sprintf('%s %s', $year, $id);

            //Try to find the program
            $program = $this->programService->findProgramByName($programName);

            if (null === $program) {
                $program = new Program();
                $program->setProgram($programName);
            }

            //Try to find the call
            $call = $this->callService->findCallByName($callName);

            if (null === $call) {
                $call = new Call();
                $call->setProgram($program);
                $call->setCall($callName);
                $call->setActive(Call::INACTIVE);

                $program->getCall()->add($call);
            }

            $project = $this->projectService->findProjectByName($content[$this->headerKeys['Proposal Acronym']]);

            if (null === $project) {
                $project = new Project();
                $project->setProject($content[$this->headerKeys['Proposal Acronym']]);

                //Derive te start and end date
                $dateStart = new DateTime();
                $project->setDateStart($dateStart->modify('first day of january ' . ($year + 1)));
                $project->setDateStartActual($dateStart->modify('first day of january ' . ($year + 1)));
                $dateEnd = new DateTime();
                $project->setDateEnd($dateEnd->modify('last day of december ' . ($year + 4)));
                $project->setDateEndActual($dateEnd->modify('last day of december ' . ($year + 4)));
                $project->setContact($contact);
            }

            //Explicit set the call in the project to have it upon persisting to avoid the creation of double calls
            $project->setCall($call);


            /**
             * We are trying to create or import affiliations here, so that is the approach.
             *
             * Per line from the import we are having a
             *
             * 1) Partner name in the project
             * 2) A legal name of the parent
             * 3) Country
             *
             * But, due to changes in the database, it might be possible that the parent is wrong, so if we find the partner in the
             * project with the name, we only need to check if the found organisation has a parent or not.
             *
             * For an affiliation we need to have a
             *
             * $organisation (for legacy) and a
             * $parentOrganisation
             */


            //Try first to find the organisation in based on the name
            $legalName  = $content[$this->headerKeys['Legal Name']];
            $parentName = $content[$this->headerKeys['Parent']];

            //First initiate the needed variables.
            $affiliation        = false;
            $organisation       = false;
            $parentOrganisation = false;

            //First iterate over the organisations in the project to see if we find an organisation with the $legalName in the country
            foreach ($project->getOrganisationName() as $organisationName) {
                if (
                    ! $organisation
                    && $legalName === $organisationName->getOrganisation()->getOrganisation()
                    && $organisationName->getOrganisation()->getCountry()->getId() === $country->getId()
                ) {
                    //Bingo, we found the organisation we are now immediately  done if the organisation has a parent has a
                    $organisation = $organisationName->getOrganisation();

                    //Hey, this organisation has a parent, so we can store that info now for later use.
                    if ($organisation->hasParent()) {
                        $parentOrganisation = $organisation->getParentOrganisation();
                    }
                }
            }


            //Imagine we found not organisation, this means that the organisation is not yet in the project. We need to dig further
            if (! $organisation) {
                //Try to find the organisation somewhere in the database
                $organisations = $this->organisationService->findOrganisationsByNameCountry(
                    $legalName,
                    $country,
                    false
                );

                //We now might find more than organisation, try to find one which has a parent
                /** @var Organisation $anyOrganisation */
                foreach ($organisations as $anyOrganisation) {
                    if (! $organisation && $anyOrganisation->hasParent()) {
                        $organisation       = $anyOrganisation;
                        $parentOrganisation = $anyOrganisation->getParentOrganisation();
                    }
                }

                //if we still cannot find one, just take the first one.
                if (! $organisation) {
                    $organisation = $this->organisationService->findOrganisationByNameCountry(
                        $legalName,
                        $country,
                        false
                    );
                }
            }

            //if we still have not found one, then create one.
            if (! $organisation) {
                $organisation = $this->createOrganisation(
                    $legalName,
                    $country
                );
            }

            //Now, if the organisation has not parent
            if (! $organisation->hasParent()) {
                //Try if we can find a parent for the organisation
                $parent = $this->parentService->findParentByOrganisationName($parentName);

                if (null === $parent) {
                    $organisationForParent = $organisation;
                    //Only create a new organisation if the names differ
                    if ($parentName !== $legalName) {
                        $organisationForParent = $this->createOrganisation($parentName, $country);
                    }

                    $parent = $this->handleParentInformation(
                        $organisationForParent,
                        $contact,
                        $program,
                        $content
                    );
                }

                $parentOrganisation = new \Organisation\Entity\Parent\Organisation();
                $parentOrganisation->setOrganisation($organisation);
                $parentOrganisation->setParent($parent);
                $parentOrganisation->setContact($contact);

                //Add the $parentOrganisation to the parent for further lookups
                $parent->getParentOrganisation()->add($parentOrganisation);
                //Inject the parentOrganisation in the organisation for further lookups
                $organisation->setParentOrganisation($parentOrganisation);
            }


            //We have a parent organisation (either created not), lets see if we can find the project in the list
            foreach ($parentOrganisation->getAffiliation() as $existingAffiliation) {
                if (! $affiliation && $existingAffiliation->getProject()->getId() === $project->getId()) {
                    $affiliation = $existingAffiliation;
                }
            }

            if (! $affiliation) {
                $affiliation = new Affiliation();
                $affiliation->setOrganisation($parentOrganisation->getOrganisation()); //Keep this for BC
                $affiliation->setParentOrganisation($parentOrganisation);
                $affiliation->setProject($project);
                $affiliation->setContact($contact);
            }

            //Try to find the funding, or create if cannot be found
            $funding = $affiliation->getFunded()->first();

            if (! $funding) {
                $funding = new Funded();
                $funding->setAffiliation($affiliation);
            }

            $funding->setFundingEu(
                (float)((int)trim(str_replace(['.', ','], '', $content[$this->headerKeys['EU funding']]), '"') / 100)
            );
            $funding->setFundingNational(
                (float)((int)trim(
                    str_replace(
                        ['.', ','],
                        '',
                        $content[$this->headerKeys['National funding']]
                    ),
                    '"'
                ) / 100)
            );

            $affiliation->getFunded()->add($funding);
            $parentOrganisation->getAffiliation()->add($affiliation);


            //Store the name of the organisation in the organisation table per project
            $organisationNameStored = false;
            foreach ($organisation->getNames() as $name) {
                if (! $organisationNameStored && $name->getProject()->getId() === $project->getId()) {
                    $organisationNameStored = true;
                }
            }

            if (! $organisationNameStored) {
                $organisationName = new Name();
                $organisationName->setName($content[$this->headerKeys['Legal Name']]);
                $organisationName->setProject($project);
                $organisationName->setOrganisation($organisation);
                $organisation->getNames()->add($organisationName);
            }


            //Only persist when the key is given
            if (in_array($key, $keys, false)) {
                $affiliation->setDateEnd(null);
                $this->entityManager->persist($affiliation);
                $this->entityManager->flush($affiliation);
                $this->entityManager->persist($funding);
                $this->entityManager->flush($funding);
                $this->importedAffiliation[] = $affiliation;
            }

            /** Add the parent to the parents array */
            $this->affiliation[$key] = $affiliation;
        }
    }

    public function handleParentInformation(
        Organisation $organisation,
        Contact $contact,
        Program $program,
        array $content
    ): ParentEntity {
        //If we find the organisation and the organisation is a parent, just return it
        if (null !== $organisation->getParent()) {
            $parent = $organisation->getParent();
        } else {
            $parent = new ParentEntity();
            $parent->setContact($contact);
            $parent->setOrganisation($organisation);
        }

        $parentType = $this->parentService->findParentTypeByName($content[$this->headerKeys['Member Type']]);

        if (null === $parentType) {
            $parentType = $this->parentService->find(ParentType::class, ParentType::TYPE_OTHER);
        }

        $parent->setType($parentType);
        $parent->setMemberType($this->parseMemberType($content));
        $parent->setArtemisiaMemberType($this->parseArtimisiaMemberType($content));
        $parent->setEpossMemberType($this->parseEpossMemberType($content));

        //Fix the DOA's
        $ecselDoa = (string)$content[$this->headerKeys['AENEAS ECSEL DoA']] === '1';

        $hasDoa = $this->parentService->hasDoaForProgram($parent, $program);

        if ($ecselDoa && ! $hasDoa) {
            $doa = new Doa();
            $doa->setProgram($program);
            $doa->setParent($parent);
            $doa->setDateApproved(new DateTime());
            $doa->setDateSigned(new DateTime());
            $doa->setContact($contact);
            $parent->getDoa()->add($doa);
        }

        if (null === $parent->getFinancial()) {
            $financial = new Financial();
            $financial->setParent($parent);
            $financial->setOrganisation($organisation);
            $financial->setContact($contact);

            $parent->getFinancial()->add($financial);
        }

        //Add the parent to the organisation
        $organisation->setParent($parent);

        return $parent;
    }

    public function parseMemberType(array $content): int
    {
        $member = $content[$this->headerKeys['Member AENEAS']] === '1';

        //Derive the member type
        switch (true) {
            case $member:
                return ParentEntity::MEMBER_TYPE_MEMBER;
            default:
                return ParentEntity::MEMBER_TYPE_NO_MEMBER;
        }
    }

    public function parseArtimisiaMemberType(array $content): int
    {
        $artemisiaDOA    = (string)$content[$this->headerKeys['ARTEMISIA DoA']] === '1';
        $artemisiaMember = (string)$content[$this->headerKeys['Member ARTEMISIA']] === '1';

        //Derive the member type
        switch (true) {
            case $artemisiaMember:
                return ParentEntity::ARTEMISIA_MEMBER_TYPE_MEMBER;
            case $artemisiaDOA:
                return ParentEntity::ARTEMISIA_MEMBER_TYPE_DOA_SIGNER;
            default:
                return ParentEntity::ARTEMISIA_MEMBER_TYPE_NO_MEMBER;
        }
    }

    public function parseEpossMemberType(array $content): int
    {
        $epossMember = (string)$content[$this->headerKeys['Member EPOSS']] === '1';
        $epossDOA    = (string)$content[$this->headerKeys['EPoSS DoA']] === '1';

        //Derive the member type
        switch (true) {
            case $epossMember:
                return ParentEntity::EPOSS_MEMBER_TYPE_MEMBER;
            case $epossDOA:
                return ParentEntity::EPOSS_MEMBER_TYPE_DOA_SIGNER;
            default:
                return ParentEntity::EPOSS_MEMBER_TYPE_NO_MEMBER;
        }
    }
}
