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
use General\Entity\Country;
use General\Entity\Gender;
use General\Entity\Title;
use Organisation\Entity\Name;
use Organisation\Entity\OParent;
use Organisation\Entity\Organisation;
use Organisation\Entity\Parent\Financial;
use Organisation\Entity\Parent\Organisation as ParentOrganisation;
use Organisation\Entity\Parent\Status;
use Organisation\Entity\Parent\Type as ParentType;
use Program\Entity\Call\Call;
use Program\Entity\Program;
use Project\Entity\Funding\Funded;
use Project\Entity\Project;
use Zend\Validator\EmailAddress;

/**
 * Class HandleImport.
 */
class HandleParentAndProjectImport extends AbstractImportPlugin
{
    public const STATUS_MEMBER = 1;
    public const STATUS_DOA_SIGNER = 2;
    public const STATUS_FEE_RIDER = 3;
    public const STATUS_IA_MEMBER = 4;
    public const STATUS_PENTA_DOA = 5;
    public const STATUS_ECSEL_DOA = 6;
    public const STATUS_ECSEL_ENIAC_DOA = 7;

    /**
     * $this function extracts the data and created local arrays.
     *
     * @param $data
     */
    public function setData(string $data)
    {
        $data = trim(($data));

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
        $amount = count($data);
        for ($i = 1; $i < $amount; $i++) {
            $row = explode($this->delimiter, $data[$i]);

            if (\count($row) >= count($this->header)) {
                //Trim all the elements
                $row = array_map('trim', $row);

                //Remove the "
                $row = array_map(function ($element) {
                    return trim($element, '""');
                }, $row);

                $this->content[] = $row;
            } else {
                $this->warnings[] = sprintf(
                    "Row %s has been skipped, does not contain %s elements but %s",
                    $i + 1,
                    count($this->header),
                    count($row)
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
            'Position',
            'Legal Name',
            'BUSINESS_NAME',
            'Parent',
            'Parent country',
            'Membership status',
            'Changes in partners per communication of July 2015',
            'EPS',
            'National cost',
            'National funding',
            'H2020 Cost',
            'EU funding',
            'National costs by non members',
            'H2020 costs by non-members',
            'Total funding',
            'Member ARTEMISIA',
            'Member EPOSS',
            'Member AENEAS',
            'Member Type',
            'AENEAS ECSEL DoA before 27/2/16',
            'AENEAS ECSEL DoA after 27/2/16',
            'AENEAS ENIAC DoA',
            'ARTEMISIA DoA',
            'EPoSS DoA',
            'SUM of members',
            "SUM of DoA's",
            'free-rider',
            'Funding Artemisia',
            'Funding EPoSS',
            'Funding AENEAS',
            'Contact Email',
            'Legal Email',
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
        $validate = new EmailAddress();

        foreach ($this->content as $content) {
            /**
             * Validate the email addresses
             */

            if (!empty($content[$this->headerKeys['Contact Email']])
                && !$validate->isValid($content[$this->headerKeys['Contact Email']])
            ) {
                $this->errors[] = sprintf(
                    'EmailAddress (%s) in row %s is invalid',
                    $content[$this->headerKeys['Contact Email']],
                    $counter
                );
            }

            if (!empty($content[$this->headerKeys['Legal Email']])
                && !$validate->isValid($content[$this->headerKeys['Legal Email']])
            ) {
                $this->errors[] = sprintf(
                    'Legal Email (%s) in row %s is invalid',
                    $content[$this->headerKeys['Legal Email']],
                    $counter
                );
            }

            //Try to parse the status
            $status = $this->parseStatus($content);
            if (\is_null($status)) {
                $this->warnings[] = sprintf(
                    'Status of row %s (%s) could not be found',
                    $counter,
                    $content[$this->headerKeys['Legal Name']]
                );
            }

            if (empty($content[$this->headerKeys['Parent country']])) {
                $this->errors[] = sprintf(
                    'Parent country in row %s is empty',
                    $counter
                );
            } else {
                if (\is_null($this->getGeneralService()
                    ->findCountryByCD($content[$this->headerKeys['Parent country']]))) {
                    $this->errors[] = sprintf(
                        'Parent country (%s) in row %s cannot be found',
                        $content[$this->headerKeys['Parent country']],
                        $counter
                    );
                }
            }

            if (empty($content[$this->headerKeys['EPS']])) {
                $this->errors[] = sprintf(
                    'Country in row %s is empty',
                    $counter
                );
            } else {
                if (\is_null($this->getGeneralService()->findCountryByCD($content[$this->headerKeys['EPS']]))) {
                    $this->errors[] = sprintf(
                        'Country (%s) in row %s cannot be found',
                        $content[$this->headerKeys['EPS']],
                        $counter
                    );
                }
            }

            if (!empty($content[$this->headerKeys['Member Type']])) {
                //Try to find the status
                $type = $this->getParentService()->findParentTypeByName($content[$this->headerKeys['Member Type']]);

                if (\is_null($type)) {
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

    /**
     * @param array $content
     *
     * @return Status|object
     */
    public function parseStatus(array $content): Status
    {
        $isMember = $content[$this->headerKeys['Member AENEAS']] === '1';
        $PENTADoa = $content[$this->headerKeys['AENEAS PENTA DoA']] === '1';
        $ENIACDoa = $content[$this->headerKeys['AENEAS ENIAC DoA']] === '1';
        $ecselDoa = !empty($content[$this->headerKeys['AENEAS ECSEL DoA before 27/2/16']]) || !empty($content[$this->headerKeys['AENEAS ECSEL DoA after 27/2/16']]);
        $artemisiaMember = $content[$this->headerKeys['Member ARTEMISIA']] === '1';
        $epossMember = $content[$this->headerKeys['Member EPOSS']] === '1';
        $freeRider = $content[$this->headerKeys['free-rider']] === '1';

        //Derive the member type
        switch (true) {
            case $isMember:
                return $this->getParentService()->findEntityById(Status::class, self::STATUS_MEMBER);
            case $PENTADoa:
                return $this->getParentService()->findEntityById(Status::class, self::STATUS_PENTA_DOA);
            case $ENIACDoa:
                return $this->getParentService()->findEntityById(Status::class, self::STATUS_ECSEL_ENIAC_DOA);
            case $ecselDoa:
                return $this->getParentService()->findEntityById(Status::class, self::STATUS_ECSEL_DOA);
            case $freeRider:
                return $this->getParentService()->findEntityById(Status::class, self::STATUS_FEE_RIDER);
            case $artemisiaMember:
            case $epossMember:
            default:
                return $this->getParentService()->findEntityById(Status::class, self::STATUS_IA_MEMBER);
        }
    }

    /**
     * @param array $keys
     *
     * @return void
     */
    public function prepareContent(array $keys = [])
    {
        foreach ($this->content as $key => $content) {
            $contact = $this->getContactService()->findContactByEmail($content[$this->headerKeys['Contact Email']]);

            if (\is_null($contact) && !empty($content[$this->headerKeys['Contact Email']])) {
                $contact = new Contact();
                $contact->setEmail($content[$this->headerKeys['Contact Email']]);
                $contact->setGender($this->getGeneralService()->findEntityById(Gender::class, Gender::GENDER_UNKNOWN));
                $contact->setTitle($this->getGeneralService()->findEntityById(Title::class, Title::TITLE_UNKNOWN));

                $this->getEntityManager()->persist($contact);
            }

            if (empty($content[$this->headerKeys['Contact Email']])) {
                $contact = $this->getContactService()->findContactById(1);
            }

            //Find the country
            $country = $this->getGeneralService()->findCountryByCD($content[$this->headerKeys['EPS']]);
            $parentCountry = $this->getGeneralService()->findCountryByCD($content[$this->headerKeys['Parent country']]);

            //Extract some data form the callId
            list($programName, $year, $id) = explode('-', $content[$this->headerKeys['Call']]);

            $callName = sprintf('%s-%s', $year, $id);

            //Try to find the program
            $program = $this->getProgramService()->findProgramByName($programName);

            if (\is_null($program)) {
                $program = new Program();
                $program->setProgram($programName);
            }

            //Try to find the call
            $call = $this->getCallService()->findCallByName($callName);

            if (\is_null($call)) {
                $call = new Call();
                $call->setProgram($program);
                $call->setCall($callName);

                $program->getCall()->add($call);
            }

            $project = $this->getProjectService()->findProjectByName($content[$this->headerKeys['Proposal Acronym']]);

            if (\is_null($project)) {
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


            if (\is_null($organisationForParent)) {
                $organisationForParent = $this->createOrganisation(
                    $content[$this->headerKeys['Parent']],
                    $parentCountry
                );
            }

            //Try to find the organisation
            $organisation = $this->getOrganisationService()->findOrganisationByNameCountry(
                $content[$this->headerKeys['Legal Name']],
                $country,
                false
            );


            if (\is_null($organisation)) {
                $organisation = $this->createOrganisation(
                    $content[$this->headerKeys['Legal Name']],
                    $country
                );
            }

            $parent = $this->handleParentInformation(
                $organisationForParent,
                $parentCountry,
                $contact,
                $content
            );

            //Start with the parent organisation which is found from the organisation
            $parentOrganisation = $organisation->getParentOrganisation();

            if (!\is_null($parentOrganisation) && $parentOrganisation->getParent() !== $parent->getId()) {
                //Replace the parent
                $parentOrganisation->setParent($parent);
            }

            //We have the parent now, we need to create the project information
            foreach ($parent->getParentOrganisation() as $otherParentOrganisation) {
                if (\is_null($parentOrganisation)
                    && $otherParentOrganisation->getOrganisation() === $organisation
                ) {
                    $parentOrganisation = $otherParentOrganisation;
                }
            }

            //If we can't find the $parentOrganisation, create it
            if (\is_null($parentOrganisation)) {
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

            $funding->setFundingEu((float)trim(str_replace(',', '', $content[$this->headerKeys['EU funding']]), '"'));
            $funding->setFundingNational(
                (float)trim(
                    str_replace(
                        ',',
                        '',
                        $content[$this->headerKeys['National funding']]
                    ),
                    '"'
                )
            );

            $affiliation->getFunded()->add($funding);

            $parentOrganisation->getAffiliation()->add($affiliation);


            //Store the name of the organisation in the organisation table per project
            $organisationNameStored = false;
            foreach ($organisation->getNames() as $name) {
                if (!$organisationNameStored && $name->getProject()->getId() === $project->getId()) {
                    $organisationNameStored = true;
                }
            }

            if (!$organisationNameStored) {
                $organisationName = new Name();
                $organisationName->setName($content[$this->headerKeys['Legal Name']]);
                $organisationName->setProject($project);
                $organisationName->setOrganisation($organisation);
                $organisation->getNames()->add($organisationName);
            }


            //Only persist when the key is given
            if (\in_array($key, $keys, false)) {
                $this->getEntityManager()->persist($parentOrganisation);
                $this->getEntityManager()->flush($parentOrganisation);
                $this->importedParentOrganisation[] = $parentOrganisation;
            }


            /** Add the parent to the parents array */
            $this->parentOrganisation[$key] = $parentOrganisation;
        }
    }

    /**
     * @param Organisation $organisation
     * @param Country $country
     * @param Contact $contact
     * @param array $content
     *
     * @return OParent
     */
    public function handleParentInformation(
        Organisation $organisation,
        Country $country,
        Contact $contact,
        array $content
    ): OParent {
        //If we find the organisation and the organisation is a parent, just return it
        if (!\is_null($organisation->getParent())) {
            $parent = $organisation->getParent();
        } else {
            $parent = new OParent();
            $parent->setContact($contact);
            $parent->setOrganisation($organisation);
        }

        $parentType = $this->getParentService()->findParentTypeByName($content[$this->headerKeys['Member Type']]);

        if (\is_null($parentType)) {
            $parentType = $this->getParentService()->findEntityById(ParentType::class, ParentType::TYPE_OTHER);
        }

        $parent->setType($parentType);

        if (!\is_null($this->parseStatus($content))) {
            $parent->setStatus($this->parseStatus($content));
        }
        $parent->setArtemisiaMemberType($this->parseArtimisiaMemberType($content));
        $parent->setEpossMemberType($this->parseEpossMemberType($content));

        if (\is_null($parent->getFinancial())) {
            $financial = new Financial();
            $financial->setParent($parent);
            $financial->setOrganisation($organisation);
            $financial->setContact($contact);

            $parent->setFinancial($financial);
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
    public function parseArtimisiaMemberType(array $content): int
    {
        $artemisiaDoa = $content[$this->headerKeys['ARTEMISIA DoA']] === '1';
        $artemisiaMember = $content[$this->headerKeys['Member ARTEMISIA']] === '1';

        //Derive the member type
        switch (true) {
            case $artemisiaMember:
                return OParent::ARTEMISIA_MEMBER_TYPE_MEMBER;
            case $artemisiaDoa:
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
        $epossMember = $content[$this->headerKeys['Member EPOSS']] === '1';
        $epossDoa = $content[$this->headerKeys['EPoSS DoA']] === '1';

        //Derive the member type
        switch (true) {
            case $epossMember:
                return OParent::EPOSS_MEMBER_TYPE_MEMBER;
            case $epossDoa:
                return OParent::EPOSS_MEMBER_TYPE_DOA_SIGNER;
            default:
                return OParent::EPOSS_MEMBER_TYPE_NO_MEMBER;
        }
    }
}
