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

use Contact\Entity\Address;
use Contact\Entity\AddressType;
use Contact\Entity\Contact;
use Doctrine\Common\Collections\ArrayCollection;
use General\Entity\Country;
use General\Entity\Gender;
use General\Entity\Title;
use Organisation\Entity\Financial;
use Organisation\Entity\OParent;
use Organisation\Entity\Organisation;
use Organisation\Entity\Parent\Type as ParentType;
use Zend\Validator\EmailAddress;

/**
 * Class HandleImport.
 */
class HandleParentImport extends AbstractImportPlugin
{
    /**
     * $this function extracts the data and created local arrays.
     *
     * @param string $data
     */
    public function setData(string $data)
    {
        $data = utf8_encode($data);

        //Explode first on the \n to have the different rows
        $explodedData = explode(PHP_EOL, $data);

        $this->header = explode($this->delimiter, trim($explodedData[0]));

        $this->header = array_map('strtolower', $this->header);

        /*
         * Go over the rest of the data and add the rows to the array
         */
        $amount = count($explodedData);
        for ($i = 1; $i < $amount; $i++) {
            $row = explode($this->delimiter, $explodedData[$i]);

            if (\count($row) === count($this->header)) {
                //Trim all the elements
                $row = array_map('trim', $row);

                $this->content[$i] = $row;
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

    /**
     * @param array $keys
     */
    public function prepareContent(array $keys = []): void
    {
        foreach ($this->content as $key => $content) {
            $name = $content[$this->headerKeys['parent']];
            $status = $this->getParentService()->findParentStatusByName($content[$this->headerKeys['status']]);
            $parentCountry = $this->getGeneralService()->findCountryByCD($content[$this->headerKeys['iso 2']]);

            // Try to find the parent organisation
            $organisation = $this->getOrganisationService()->findOrganisationByNameCountry(
                $name,
                $parentCountry,
                false
            );

            if (\is_null($organisation)) {
                $organisation = $this->createOrganisation(
                    $name,
                    $parentCountry
                );
            }

            // Find the contact
            $contact = $this->handleContactInformation($content);

            $parent = $this->handleParentInformation(
                $organisation,
                $parentCountry,
                $contact,
                $content
            );

            if (\is_null($financialOrganisation = $organisation->getFinancial())) {
                $financialOrganisation = new Financial();
                $financialOrganisation->setOrganisation($organisation);
            }

            if (!empty($content[$this->headerKeys['vat']])) {
                $financialOrganisation->setVat($content[$this->headerKeys['vat']]);
            }

            $organisation->setFinancial($financialOrganisation);

            // Create the financial information
            $financialCollection = $parent->getFinancial();
            if ($financialCollection->isEmpty()) {
                $financial = new \Organisation\Entity\Parent\Financial();
                $financial->setParent($parent);
                $financialCollection = new ArrayCollection([$financial]);
            }

            $financialCollection->first()->setOrganisation($organisation);
            $financialCollection->first()->setContact($contact);

            $parent->setFinancial($financialCollection);

            // Only persist when the key is given
            if (\in_array($key, $keys, false)) {
                $this->getEntityManager()->persist($parent);
                $this->getEntityManager()->flush($parent);

                $this->importedParents[] = $parent;
            }

            /** Add the parent to the parents array */
            $this->parents[$key] = $parent;
        }
    }

    /**
     * @param array $content
     * @return Contact
     */
    public function handleContactInformation(array $content): Contact
    {
        //Try first to find the contact based on the email address
        $contact = $this->getContactService()->findContactByEmail($content[$this->headerKeys['email']]);

        //Only when we have an email we can create a new contact
        if (\is_null($contact) && !empty($content[$this->headerKeys['email']])) {
            $contact = new Contact();
            $contact->setEmail($content[$this->headerKeys['email']]);
            $contact->setGender($this->getGeneralService()->findEntityById(Gender::class, Gender::GENDER_UNKNOWN));
            $contact->setTitle($this->getGeneralService()->findEntityById(Title::class, Title::TITLE_UNKNOWN));
        }

        //We have no contact, no name, so we can't do anything
        if (\is_null($contact)) {
            return $this->getContactService()->findContactById(1);
        }

        $contact->setFirstName($content[$this->headerKeys['first name']]);
        if (!empty($content[$this->headerKeys['middle name']])) {
            $contact->setMiddleName($content[$this->headerKeys['middle name']]);
        }
        $contact->setLastName($content[$this->headerKeys['last name']]);

        if (!empty($content[$this->headerKeys['country']])) {
            //Set the address
            $financialAddress = null;
            if (!$contact->isEmpty()) {
                $financialAddress = $this->getContactService()->getFinancialAddress($contact);
            }

            if (\is_null($financialAddress)) {
                $financialAddress = new Address();
                /** @var AddressType $addressType */
                $addressType = $this->getContactService()->findEntityById(
                    AddressType::class,
                    AddressType::ADDRESS_TYPE_FINANCIAL
                );
                $financialAddress->setType($addressType);
                $financialAddress->setContact($contact);
            }

            $financialAddress->setAddress($content[$this->headerKeys['address']]);
            $financialAddress->setZipCode($content[$this->headerKeys['zip']]);
            $financialAddress->setCity($content[$this->headerKeys['city']]);

            $country = $this->getGeneralService()->findCountryByCD($content[$this->headerKeys['country']]);
            $financialAddress->setCountry($country);

            //@todo, figure out why the address is not updated
            $contact->getAddress()->add($financialAddress);
        }

        return $contact;
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

        $parentType = $this->getParentService()->findParentTypeByName($content[$this->headerKeys['type']]);
        if (\is_null($parentType)) {
            $parentType = $this->getParentService()->findEntityById(ParentType::class, ParentType::TYPE_OTHER);
        }
        $parent->setType($parentType);

        $status = $this->getParentService()->findParentStatusByName($content[$this->headerKeys['status']]);
        $parent->setStatus($status);

        $parent->setArtemisiaMemberType(OParent::ARTEMISIA_MEMBER_TYPE_NO_MEMBER);
        $parent->setEpossMemberType(OParent::EPOSS_MEMBER_TYPE_NO_MEMBER);

        $parent->setContact($contact);

        //Add the parent to the organisation
        $organisation->setParent($parent);

        return $parent;
    }

    /**
     * validate the data
     */
    public function validateData()
    {
        $minimalRequiredElements = [
            'parent',
            'type',
            'iso 2',
            'vat',
            'first name',
            'middle name',
            'last name',
            'email',
            'address',
            'city',
            'country'
        ];


        /*
         * Go over all elements and check if the required elements are present
         */
        foreach ($minimalRequiredElements as $element) {
            if (!\in_array(strtolower($element), $this->header, true)) {
                $this->errors[] = sprintf('Element %s is missing in the file', $element);
            }
        }

        //Break the validation already here as further testing makes no sense
        if (\count($this->errors) === 0) {
            /**
             * Create the lookup-table
             */
            $this->headerKeys = array_flip($this->header);

            $counter = 2;
            foreach ($this->content as $content) {
                //Try to find the status
                $status = $this->getParentService()->findParentStatusByName($content[$this->headerKeys['status']]);

                if (\is_null($status)) {
                    $this->errors[] = sprintf(
                        'Status (%s) in row %s cannot be found',
                        $content[$this->headerKeys['status']],
                        $counter
                    );
                }

                if (!empty($content[$this->headerKeys['email']])) {
                    /**
                     * Validate the email addresses
                     */
                    $validate = new EmailAddress();
                    if (!$validate->isValid($content[$this->headerKeys['email']])) {
                        $this->errors[] = sprintf(
                            "EmailAddress (%s) in row %s is invalid",
                            $content[$this->headerKeys['email']],
                            $counter
                        );
                    }
                }


                if (!empty($content[$this->headerKeys['type']])) {
                    //Try to find the type
                    $type = $this->getParentService()->findParentTypeByName($content[$this->headerKeys['type']]);

                    if (\is_null($type)) {
                        $this->errors[] = sprintf(
                            'Type (%s) in row %s cannot be found',
                            $content[$this->headerKeys['type']],
                            $counter
                        );
                    }
                }


                //Try to find the parent country
                $country = $this->getGeneralService()->findCountryByCD($content[$this->headerKeys['iso 2']]);

                if (\is_null($country)) {
                    $this->errors[] = sprintf(
                        'Parent Country (%s) in row %s cannot be found',
                        $content[$this->headerKeys['iso 2']],
                        $counter
                    );
                }

                if (!empty($content[$this->headerKeys['country']])) {
                    //Try to find the financial country
                    $country = $this->getGeneralService()->findCountryByCD($content[$this->headerKeys['country']]);

                    if (\is_null($country)) {
                        $this->errors[] = sprintf(
                            'Financial Country (%s) in row %s cannot be found',
                            $content[$this->headerKeys['country']],
                            $counter
                        );
                    }
                }

                $counter++;
            }
        }
    }
}
