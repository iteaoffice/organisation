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

use Contact\Entity\Contact;
use Organisation\Entity;

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
        /*
         * Go over the rest of the data and add the rows to the array
         */
        $amount = count($explodedData);
        for ($i = 1; $i < $amount; $i++) {
            $row = explode($this->delimiter, $explodedData[$i]);

            if (count($row) === count($this->header)) {
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
    public function prepareContent(array $keys = [])
    {
        foreach ($this->content as $key => $content) {
            $name = $content[$this->headerKeys['parent']];


            $status  = $this->getParentService()->findParentStatusByName($content[$this->headerKeys['status']]);
            $country = $this->getGeneralService()->findCountryByCD($content[$this->headerKeys['country']]);

            //Try first to see if we can find the parent based on the country and on the $name
            $organisation = $this->getOrganisationService()->findOrganisationByNameCountry($name, $country);

            $type = $this->getParentService()
                         ->findEntityById(Entity\Parent\Type::class, Entity\Parent\Type::TYPE_OTHER);
            if (! empty($content[$this->headerKeys['type']])) {
                //Try to find the type
                $type = $this->getParentService()->findParentTypeByName($content[$this->headerKeys['type']]);
            }

            if (is_null($organisation)) {
                $organisation = $this->createOrganisation($name, $country);

                //We have no parent at all, so create it
                $parent = new Entity\OParent();
                $parent->setOrganisation($organisation);
                $parent->setType($type);
                $parent->setStatus($status);

                $this->getEntityManager()->persist($parent);

                //Add the $organisation so the parent becomes his own organisation as well
                $parentOrganisation = new Entity\Parent\Organisation();
                $parentOrganisation->setParent($parent);
                $parentOrganisation->setOrganisation($organisation);

                /** @var Contact $contact */
                $contact = $this->getEntityManager()->find(Contact::class, 1);

                $parentOrganisation->setContact($contact);

                //Only persist when the result is in the array
                if (in_array($key, $keys, false)) {
                    $this->getEntityManager()->persist($parent);
                    $this->importedParents[] = $parent;
                }

                $this->parents[$key] = $parent;
            }

            if (! is_null($organisation)) {
                //We have the organisation, but we need to check if the organisation is a parent
                if (is_null($organisation->getParent())) {
                    //We have no parent at all, so create it
                    $parent = new Entity\OParent();
                    $parent->setOrganisation($organisation);
                    $parent->setType($type);
                    $parent->setStatus($status);

                    $this->getEntityManager()->persist($parent);

                    //Add the $organisation so the parent becomes his own organisation as well
                    $parentOrganisation = new Entity\Parent\Organisation();
                    $parentOrganisation->setParent($parent);
                    $parentOrganisation->setOrganisation($organisation);

                    /** @var Contact $contact */
                    $contact = $this->getContactService()->findContactById(1);

                    $parentOrganisation->setContact($contact);
                    $this->getEntityManager()->persist($parent);

                    //Only persist when the result is in the array
                    if (in_array($key, $keys, false)) {
                        $this->getEntityManager()->persist($parent);
                        $this->importedParents[] = $parent;
                    }

                    $this->parents[$key] = $parent;
                }


                if (! is_null($organisation->getParent())) {
                    //We have the parent, update the status now
                    $parent = $organisation->getParent();
                    $parent->setStatus($status);
                    $parent->setType($type);

                    //Only persist when the result is in the array
                    if (in_array($key, $keys, false)) {
                        $this->getEntityManager()->persist($parent);
                        $this->importedParents[] = $parent;
                    }

                    $this->parents[$key] = $parent;
                }
            }
        }
    }

    /**
     * validate the data
     */
    public function validateData()
    {
        $minimalRequiredElements = ['parent', 'type', 'status', 'country'];

        /*
         * Go over all elements and check if the required elements are present
         */
        foreach ($minimalRequiredElements as $element) {
            if (! in_array(strtolower($element), $this->header, true)) {
                $this->errors[] = sprintf('Element %s is missing in the file', $element);
            }
        }

        //Break the validation already here as further testing makes no sense
        if (count($this->errors) === 0) {
            /**
             * Create the lookup-table
             */
            $this->headerKeys = array_flip($this->header);

            $counter = 2;
            foreach ($this->content as $content) {
                //Try to find the status
                $status = $this->getParentService()->findParentStatusByName($content[$this->headerKeys['status']]);

                if (is_null($status)) {
                    $this->errors[] = sprintf(
                        'Status (%s) in row %s cannot be found',
                        $content[$this->headerKeys['status']],
                        $counter
                    );
                }


                if (! empty($content[$this->headerKeys['type']])) {
                    //Try to find the type
                    $type = $this->getParentService()->findParentTypeByName($content[$this->headerKeys['type']]);

                    if (is_null($type)) {
                        $this->errors[] = sprintf(
                            'Type (%s) in row %s cannot be found',
                            $content[$this->headerKeys['type']],
                            $counter
                        );
                    }
                }


                //Try to find the country
                $country = $this->getGeneralService()->findCountryByCD($content[$this->headerKeys['country']]);

                if (is_null($country)) {
                    $this->errors[] = sprintf(
                        'Country (%s) in row %s cannot be found',
                        $content[$this->headerKeys['country']],
                        $counter
                    );
                }

                $counter++;
            }
        }
    }
}
