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
use General\Entity\Country;
use General\Entity\Gender;
use General\Entity\Title;
use Organisation\Entity\Name;
use Organisation\Entity\OParent;
use Organisation\Entity\Organisation;
use Organisation\Entity\Parent\Financial;
use Organisation\Entity\Parent\Organisation as ParentOrganisation;
use Organisation\Entity\Parent\Type as ParentType;
use Organisation\Entity\Type;
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
    /**
     * $this function extracts the data and created local arrays.
     *
     * @param $data
     */
    public function setData(string $data)
    {
        $data = trim(utf8_encode($data));

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

            if (count($row) === count($this->header)) {
                //Trim all the elements
                $row = array_map('trim', $row);

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
            'parent',
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
            'Membership Status',
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
            'c-member share = funding / sum fundings of all c-members in the project',
            'freerider share = C member share / sum of memberships for AENEAS members only',
            'AENEAS extra var contr 2016',
        ];

        /*
         * Go over all elements and check if the required elements are present
         */
        foreach ($minimalRequiredElements as $element) {
            if (! in_array($element, $this->header)) {
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
        $counter  = 2;
        $validate = new EmailAddress();

        foreach ($this->content as $content) {
            /**
             * Validate the email addresses
             */

            if (! empty($content[$this->headerKeys['Contact Email']])
                 && ! $validate->isValid($content[$this->headerKeys['Contact Email']])
            ) {
                $this->errors[] = sprintf(
                    'EmailAddress (%s) in row %s is invalid',
                    $content[$this->headerKeys['Contact Email']],
                    $counter
                );
            }

            if (! empty($content[$this->headerKeys['Legal Email']])
                 && ! $validate->isValid($content[$this->headerKeys['Legal Email']])
            ) {
                $this->errors[] = sprintf(
                    'Legal Email (%s) in row %s is invalid',
                    $content[$this->headerKeys['Legal Email']],
                    $counter
                );
            }

            if (! empty($content[$this->headerKeys['EPS']])
                 && is_null(
                     $country = $this->getGeneralService()->findCountryByCD($content[$this->headerKeys['EPS']])
                 )
            ) {
                $this->errors[] = sprintf(
                    'Country (%s) in row %s cannot be found',
                    $content[$this->headerKeys['EPS']],
                    $counter
                );
            }

            $counter++;
        }

        return true;
    }

    /**
     * @param bool $doImport
     */
    public function prepareContent(bool $doImport = false)
    {
        foreach ($this->content as $key => $content) {
            $contact = $this->getContactService()->findContactByEmail($content[$this->headerKeys['Contact Email']]);

            if (is_null($contact) && ! empty($content[$this->headerKeys['Contact Email']])) {
                $contact = new Contact();
                $contact->setEmail($content[$this->headerKeys['Contact Email']]);
                $contact->setGender($this->getGeneralService()->findEntityById(Gender::class, Gender::GENDER_UNKNOWN));
                $contact->setTitle($this->getGeneralService()->findEntityById(Title::class, Title::TITLE_UNKNOWN));

                $this->getEntityManager()->persist($contact);
            }

            if (empty($content[$this->headerKeys['Contact Email']])) {
                $contact = $this->getContactService()->findContactById(1);
            }

            $this->contact[$key] = $contact;

            //Find the country
            $country = $this->getGeneralService()->findCountryByCD($content[$this->headerKeys['EPS']]);

            //Extract some data form the callId
            list($programName, $year, $id) = explode('-', $content[$this->headerKeys['Call']]);

            $callName = sprintf('%s-%s', $year, $id);

            //Try to find the program
            $program = $this->getProgramService()->findProgramByName($programName);

            if (is_null($program)) {
                $program = new Program();
                $program->setProgram($programName);

                $this->getEntityManager()->persist($program);
            }

            $this->program[$key] = $program;


            //Try to find the call
            $call = $this->getCallService()->findCallByName($callName);


            if (is_null($call)) {
                $call = new Call();
                $call->setProgram($program);
                $call->setCall($callName);

                $this->getEntityManager()->persist($call);
            }

            $this->call[$key] = $call;

            $project = $this->getProjectService()->findProjectByName($content[$this->headerKeys['Proposal Acronym']]);

            if (is_null($project)) {
                $project = new Project();
                $project->setProject($content[$this->headerKeys['Proposal Acronym']]);
                $project->setCall($call);

                //Derive te start and end date
                $dateStart = new \DateTime();
                $project->setDateStart($dateStart->modify('first day of january ' . ($year + 1)));
                $project->setDateStartActual($dateStart->modify('first day of january ' . ($year + 1)));
                $dateEnd = new \DateTime();
                $project->setDateEnd($dateEnd->modify('last day of december ' . ($year + 4)));
                $project->setDateEndActual($dateEnd->modify('last day of december ' . ($year + 4)));
                $project->setContact($contact);

                //Just persist the entity, do nothing
                $this->getEntityManager()->persist($project);
            }

            $this->project[$key] = $project;


            //Try to find the organisation
            $organisation = $this->getOrganisationService()->findOrganisationByNameCountry(
                $content[$this->headerKeys['Legal Name']],
                $country,
                false
            );

            if (is_null($organisation)) {
                $organisation = $this->createOrganisation($content[$this->headerKeys['Legal Name']], $country);
            }

            $this->organisation[$key] = $organisation;

            $parent = $this->handleParentInformation(
                $content[$this->headerKeys['parent']],
                $country,
                $contact,
                $content
            );

            $organisationFound = false;
            //We have the parent now, we need to create the project information
            foreach ($parent->getParentOrganisation() as $parentOrganisation) {
                if ($parentOrganisation->getOrganisation()->getId() === $organisation->getId()) {
                    $organisationFound = $parentOrganisation;
                }
            }

            if (! $organisationFound) {
                $parentOrganisation = new ParentOrganisation();
                $parentOrganisation->setOrganisation($organisation);
                $parentOrganisation->setParent($parent);
                $parentOrganisation->setContact($contact);

                $this->getEntityManager()->persist($parentOrganisation);
            } else {
                $parentOrganisation = $organisationFound;
            }

            $this->parentOrganisation[$key] = $parentOrganisation;

            $affiliationFound = false;
            //Check if the affiliation already exist
            foreach ($parentOrganisation->getAffiliation() as $affiliation) {
                if ($affiliation->getProject()->getId() === $project->getId()) {
                    $affiliationFound = $affiliation;
                }
            }

            if (! $affiliationFound) {
                $affiliation = new Affiliation();
                $affiliation->setOrganisation($parentOrganisation->getOrganisation()); //Keep this for BC
                $affiliation->setParentOrganisation($parentOrganisation);
                $affiliation->setProject($project);
                $affiliation->setContact($contact);
            } else {
                $affiliation = $affiliationFound;
                $affiliation->setFunding(null);
            }

            $this->getEntityManager()->persist($affiliation);

            //Store the name of the organisation in the organisation table per project
            $organisationNameStored = false;
            foreach ($organisation->getNames() as $name) {
                if ($name->getProject() === $project) {
                    $organisationNameStored = true;
                }
            }

            if (! $organisationNameStored) {
                $organisationName = new Name();
                $organisationName->setName($this->headerKeys['Legal Name']);
                $organisationName->setProject($project);
                $organisationName->setOrganisation($organisation);
                $this->getEntityManager()->persist($organisationName);
            }

            $this->parentAffiliation[$key] = $affiliation;


            $funding = new Funded();
            $funding->setAffiliation($affiliation);
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


            $this->getEntityManager()->persist($funding);

            $this->funding[$key] = $funding;


            /** Add the parent to the parents array */
            $this->parents[$key] = $parent;
        }
    }


    /**
     * @param string  $name
     * @param Country $country
     * @param Contact $contact
     * @param array   $content
     *
     * @return OParent
     */
    public function handleParentInformation(string $name, Country $country, Contact $contact, array $content): OParent
    {
        //Try to find the parent with the organisation, and not via the entities as hydration will not always succeed
        $organisation = $this->getOrganisationService()->findOrganisationByNameCountry($name, $country, false);

        //If we don't find the organisation, create it
        if (is_null($organisation)) {
            $organisation = $this->createOrganisation($name, $country);
        }

        //If we find the organisation and the organisation is a parent, just return it
        if (! is_null($organisation->getParent())) {
            $parent = $organisation->getParent();
        } else {
            $parent = new OParent();
            $parent->setContact($contact);
            $parent->setOrganisation($organisation);
        }


        $parentStatus = $this->getParentService()
                             ->findParentStatusByName($content[$this->headerKeys['Membership Status']]);
        $parent->setStatus($parentStatus);

        /** @var ParentType $type */
        $type = $this->getParentService()->findEntityById(ParentType::class, ParentType::TYPE_FEE_RIDER);
        $parent->setType($type);

        $isMember = $content[$this->headerKeys['Member AENEAS']];
        if (! empty($isMember) && $isMember === '1') {
            /** @var ParentType $type */
            $type = $this->getParentService()->findEntityById(ParentType::class, ParentType::TYPE_MEMBER);
            $parent->setType($type);
        }


        $doaBeforeFebruary = $content[$this->headerKeys['AENEAS ECSEL DoA before 27/2/16']];
        if ((! empty($doaBeforeFebruary) && $doaBeforeFebruary === '1')
            || (! empty($doaAfterFebruary)
                 && $doaAfterFebruary === '1')
        ) {
            /** @var ParentType $type */
            $type = $this->getParentService()->findEntityById(ParentType::class, ParentType::TYPE_DOA_SIGNER);
            $parent->setType($type);
        }

        $aeneasENIACDoa = $content[$this->headerKeys['AENEAS ENIAC DoA']];
        if (! empty($aeneasENIACDoa) && $doaBeforeFebruary === '1') {
            /** @var ParentType $type */
            $type = $this->getParentService()->findEntityById(ParentType::class, ParentType::TYPE_DOA_SIGNER);
            $parent->setType($type);
        }

        $parent->setArtemisiaMemberType(OParent::ARTEMISIA_MEMBER_TYPE_NO_MEMBER);

        //Update the parent with the data from the memberships
        $artemisiaMember = $content[$this->headerKeys['Member ARTEMISIA']];
        if (! empty($artemisiaMember) && $artemisiaMember === '1') {
            $parent->setArtemisiaMemberType(OParent::ARTEMISIA_MEMBER_TYPE_MEMBER);
        }

        $artemisiaDOA = $content[$this->headerKeys['ARTEMISIA DoA']];
        if (! empty($artemisiaDOA) && $artemisiaDOA === '1') {
            $parent->setArtemisiaMemberType(OParent::ARTEMISIA_MEMBER_TYPE_DOA_SIGNER);
        }

        $parent->setEpossMemberType(OParent::EPOSS_MEMBER_TYPE_NO_MEMBER);

        //Update the parent with the data from the memberships
        $epossMember = $content[$this->headerKeys['Member EPOSS']];
        if (! empty($epossMember) && $epossMember === '1') {
            $parent->setEpossMemberType(OParent::EPOSS_MEMBER_TYPE_MEMBER);
        }

        $epossMember = $content[$this->headerKeys['EPoSS DoA']];
        if (! empty($epossMember) && $epossMember === '1') {
            $parent->setEpossMemberType(OParent::EPOSS_MEMBER_TYPE_DOA_SIGNER);
        }

        $this->getEntityManager()->persist($parent);

        if (is_null($parent->getFinancial())) {
            $financial = new Financial();
            $financial->setParent($parent);
            $financial->setOrganisation($organisation);
            $financial->setContact($contact);
            $this->getEntityManager()->persist($financial);
        }

        return $parent;
    }

    /**
     * @param string  $name
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
}
