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
use Contact\Entity\Contact;
use Organisation\Entity\Name;
use Organisation\Entity\OParent;
use Organisation\Entity\Organisation;
use Organisation\Entity\Parent\Doa;
use Organisation\Entity\Parent\Financial;
use Organisation\Entity\Parent\Organisation as ParentOrganisation;
use Organisation\Entity\Parent\Type as ParentType;
use Program\Entity\Call\Call;
use Program\Entity\Program;
use Project\Entity\Funding\Funded;
use Project\Entity\Project;

/**
 * Class HandleImport.
 */
class HandleParentAndProjectImport extends AbstractImportPlugin
{
    /**
     * $this function extracts the data and created local arrays.
     *
     * @param array|string $data
     */
    public function setData(string $data)
    {
        $data = trim($data);

        //Explode first on the \n to have the different rows
        $data = explode(PHP_EOL, $data);

        //Apply a general trim to remove unwated characters
        $data = array_map('trim', $data);

        $this->header = explode($this->delimiter, trim($data[0]));

        //Trim the header
        foreach ($this->header as $key => $header) {
            $this->header[$key] = trim(trim($header, '"'));
        }

        /*
         * Go over the rest of the data and add the rows to the array
         */
        $amount = \count($data);
        for ($i = 1; $i < $amount; $i++) {
            $row = explode($this->delimiter, $data[$i]);

            if (\count($row) >= \count($this->header)) {
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
                    "Row %s has been skipped, does not contain %s elements but %s",
                    $i + 1,
                    \count($this->header),
                    \count($row)
                );
            }
        }
    }

    /**
     * With this function we will do some basic testing to see if the least amount of information is available.
     *
     * @return bool
     */
    public function validateData(): bool
    {
        $minimalRequiredElements = [
            'Call',
            'Proposal Acronym',
            'Applicant Legal Name',
            'EPS',
            'Parent',
            'Parent EPS',
            'EU Funding',
            'National Funding',
            'Member Type',
            'Member AENEAS',
            'Member ARTEMIS-IA',
            'Member EPoSS',
            'AENEAS DOA',
            'ARTEMIS-IA DOA',
            'EPoSS DOA'
        ];

        /*
         * Go over all elements and check if the required elements are present
         */
        foreach ($minimalRequiredElements as $element) {
            if (!\in_array($element, $this->header, true)) {
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
                if (null === $this->getGeneralService()
                        ->findCountryByCD($content[$this->headerKeys['EPS']])
                ) {
                    $this->errors[] = sprintf(
                        'EPS (%s) in row %s cannot be found',
                        $content[$this->headerKeys['EPS']],
                        $counter
                    );
                }
            }

            /**
             * Validate the country
             */
            if (empty($content[$this->headerKeys['Parent EPS']])) {
                $this->errors[] = sprintf(
                    'Parent EPS in row %s is empty',
                    $counter
                );
            } else {
                if (null === $this->getGeneralService()
                        ->findCountryByCD($content[$this->headerKeys['Parent EPS']])
                ) {
                    $this->errors[] = sprintf(
                        'Parent EPS (%s) in row %s cannot be found',
                        $content[$this->headerKeys['Parent EPS']],
                        $counter
                    );
                }
            }

            if (!empty($content[$this->headerKeys['Member Type']])) {
                //Try to find the status
                $type = $this->getParentService()->findParentTypeByName($content[$this->headerKeys['Member Type']]);

                if (null === $type) {
                    $this->errors[] = sprintf(
                        'Type (%s) in row %s (%s) cannot be found',
                        $content[$this->headerKeys['Member Type']],
                        $counter,
                        $content[$this->headerKeys['Applicant Legal Name']]
                    );
                }
            }

            $counter++;
        }

        return true;
    }


    /**
     * @param array $keys
     *
     * @return void
     */
    public function prepareContent(array $keys = []): void
    {
        foreach ($this->content as $key => $content) {
            $contact = $this->getContactService()->findContactById(1);

            //Find the country
            $country = $this->getGeneralService()->findCountryByCD($content[$this->headerKeys['EPS']]);
            $parentCountry = $this->getGeneralService()->findCountryByCD($content[$this->headerKeys['Parent EPS']]);

            if (null === $contact || null === $country || null === $parentCountry) {
                continue;
            }

            //Extract some data form the callId
            [$programName, $year, $id] = explode('-', $content[$this->headerKeys['Call']]);

            $callName = sprintf('%s %s', $year, $id);

            //Try to find the program
            $program = $this->getProgramService()->findProgramByName($programName);

            if (null === $program) {
                $program = new Program();
                $program->setProgram($programName);
            }

            //Try to find the call
            $call = $this->getCallService()->findCallByName($callName);

            if (null === $call) {
                $call = new Call();
                $call->setProgram($program);
                $call->setCall($callName);

                $program->getCall()->add($call);
            }

            $project = $this->getProjectService()->findProjectByName($content[$this->headerKeys['Proposal Acronym']]);

            if (null === $project) {
                $project = new Project();
                $project->setProject($content[$this->headerKeys['Proposal Acronym']]);

                //Derive te start and end date
                $dateStart = new \DateTime();
                $project->setDateStart($dateStart->modify('first day of january ' . ($year + 1)));
                $project->setDateStartActual($dateStart->modify('first day of january ' . ($year + 1)));
                $dateEnd = new \DateTime();
                $project->setDateEnd($dateEnd->modify('last day of december ' . ($year + 4)));
                $project->setDateEndActual($dateEnd->modify('last day of december ' . ($year + 4)));
                $project->setContact($contact);
            }

            //Explicit set the call in the project to have it upon persisting to avoid the creation of double calls
            $project->setCall($call);

            //Try to find the parent organisation
            $organisationForParent = $this->getOrganisationService()->findOrganisationByNameCountry(
                $content[$this->headerKeys['Parent']],
                $parentCountry,
                false
            );


            if (null === $organisationForParent) {
                $organisationForParent = $this->createOrganisation(
                    $content[$this->headerKeys['Parent']],
                    $parentCountry
                );
            }

            //Try to find the organisation
            $organisation = $this->getOrganisationService()->findOrganisationByNameCountry(
                $content[$this->headerKeys['Applicant Legal Name']],
                $country,
                false
            );


            if (null === $organisation) {
                $organisation = $this->createOrganisation(
                    $content[$this->headerKeys['Applicant Legal Name']],
                    $country
                );
            }

            $parent = $this->handleParentInformation(
                $organisationForParent,
                $contact,
                $program,
                $content
            );

            //Start with the parent organisation which is found from the organisation
            $parentOrganisation = $organisation->getParentOrganisation();

            if (null !== $parentOrganisation && $parentOrganisation->getParent() !== $parent->getId()) {
                //Replace the parent
                $parentOrganisation->setParent($parent);
            }

            //We have the parent now, we need to create the project information
            foreach ($parent->getParentOrganisation() as $otherParentOrganisation) {
                if (null === $parentOrganisation && $otherParentOrganisation->getOrganisation() === $organisation) {
                    $parentOrganisation = $otherParentOrganisation;
                }
            }

            //If we can't find the $parentOrganisation, create it
            if (null === $parentOrganisation) {
                $parentOrganisation = new ParentOrganisation();
                $parentOrganisation->setOrganisation($organisation);
                $parentOrganisation->setParent($parent);
                $parentOrganisation->setContact($contact);

                //Add the $parentOrganisation to the parent for further lookups
                $parent->getParentOrganisation()->add($parentOrganisation);
                //Inject the parentOrganisation in the organisation for futher lookups
                $organisation->setParentOrganisation($parentOrganisation);
            }


            $affiliation = false;
            //Check if the affiliation already exist
            foreach ($parentOrganisation->getAffiliation() as $existingAffiliation) {
                if (!$affiliation && $existingAffiliation->getProject()->getId() === $project->getId()) {
                    $affiliation = $existingAffiliation;
                }
            }


            if (!$affiliation) {
                $affiliation = new Affiliation();
                $affiliation->setOrganisation($parentOrganisation->getOrganisation()); //Keep this for BC
                $affiliation->setParentOrganisation($parentOrganisation);
                $affiliation->setProject($project);
                $affiliation->setContact($contact);
            }

            //Try to find the funding, or create if cannot be found
            $funding = $affiliation->getFunded()->first();

            if (!$funding) {
                $funding = new Funded();
                $funding->setAffiliation($affiliation);
            }

            $funding->setFundingEu((float)trim(str_replace(',', '', $content[$this->headerKeys['EU Funding']]), '"'));
            $funding->setFundingNational(
                (float)trim(
                    str_replace(
                        ',',
                        '',
                        $content[$this->headerKeys['National Funding']]
                    ),
                    '"'
                )
            );

            $affiliation->getFunded()->add($funding);

            //$parentOrganisation->getAffiliation()->add($affiliation);


            //Store the name of the organisation in the organisation table per project
            $organisationNameStored = false;
            foreach ($organisation->getNames() as $name) {
                if (!$organisationNameStored && $name->getProject()->getId() === $project->getId()) {
                    $organisationNameStored = true;
                }
            }

            if (!$organisationNameStored) {
                $organisationName = new Name();
                $organisationName->setName($content[$this->headerKeys['Applicant Legal Name']]);
                $organisationName->setProject($project);
                $organisationName->setOrganisation($organisation);
                $organisation->getNames()->add($organisationName);
            }


            //Only persist when the key is given
            if (\in_array($key, $keys, false)) {
                $this->getEntityManager()->persist($affiliation);
                $this->getEntityManager()->flush();
                $this->importedAffiliation[] = $affiliation;
            }

            /** Add the parent to the parents array */
            $this->affiliation[$key] = $affiliation;
        }
    }

    /**
     * @param Organisation $organisation
     * @param Contact      $contact
     * @param array        $content
     *
     * @return OParent
     */
    public function handleParentInformation(
        Organisation $organisation,
        Contact $contact,
        Program $program,
        array $content
    ): OParent {
        //If we find the organisation and the organisation is a parent, just return it
        if (null !== $organisation->getParent()) {
            $parent = $organisation->getParent();
        } else {
            $parent = new OParent();
            $parent->setContact($contact);
            $parent->setOrganisation($organisation);
        }

        $parentType = $this->getParentService()->findParentTypeByName($content[$this->headerKeys['Member Type']]);

        if (null === $parentType) {
            $parentType = $this->getParentService()->findEntityById(ParentType::class, ParentType::TYPE_OTHER);
        }

        $parent->setType($parentType);
        $parent->setMemberType($this->parseMemberType($content));
        $parent->setArtemisiaMemberType($this->parseArtimisiaMemberType($content));
        $parent->setEpossMemberType($this->parseEpossMemberType($content));

        //Fix the DOA's
        $ecselDoa = (string)$content[$this->headerKeys['AENEAS DOA']] === '1';

        $hasDoa = $this->getParentService()->hasDoaForProgram($parent, $program);

        if ($ecselDoa && !$hasDoa) {
            $doa = new Doa();
            $doa->setProgram($program);
            $doa->setParent($parent);
            $doa->setDateApproved(new \DateTime());
            $doa->setDateSigned(new \DateTime());
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

    /**
     * @param array $content
     *
     * @return int
     */
    public function parseMemberType(array $content): int
    {
        $member = $content[$this->headerKeys['Member AENEAS']] === '1';

        //Derive the member type
        switch (true) {
            case $member:
                return OParent::MEMBER_TYPE_MEMBER;
            default:
                return OParent::MEMBER_TYPE_NO_MEMBER;
        }
    }

    /**
     * @param array $content
     *
     * @return int
     */
    public function parseArtimisiaMemberType(array $content): int
    {
        $artemisiaDOA = (string)$content[$this->headerKeys['ARTEMIS-IA DOA']] === '1';
        $artemisiaMember = (string)$content[$this->headerKeys['Member ARTEMIS-IA']] === '1';

        //Derive the member type
        switch (true) {
            case $artemisiaMember:
                return OParent::ARTEMISIA_MEMBER_TYPE_MEMBER;
            case $artemisiaDOA:
                return OParent::ARTEMISIA_MEMBER_TYPE_DOA_SIGNER;
            default:
                return OParent::ARTEMISIA_MEMBER_TYPE_NO_MEMBER;
        }
    }

    /**
     * @param array $content
     *
     * @return int
     */
    public function parseEpossMemberType(array $content): int
    {
        $epossMember = (string)$content[$this->headerKeys['Member EPoSS']] === '1';
        $epossDOA = (string)$content[$this->headerKeys['EPoSS DOA']] === '1';

        //Derive the member type
        switch (true) {
            case $epossMember:
                return OParent::EPOSS_MEMBER_TYPE_MEMBER;
            case $epossDOA:
                return OParent::EPOSS_MEMBER_TYPE_DOA_SIGNER;
            default:
                return OParent::EPOSS_MEMBER_TYPE_NO_MEMBER;
        }
    }
}
