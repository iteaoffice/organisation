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

        /*
         * Go over the rest of the data and add the rows to the array
         */
        $amount = count($explodedData);
        for ($i = 1; $i < $amount; $i++) {
            $row = explode($this->delimiter, $explodedData[$i]);

            //Trim all the elements
            $row = array_map('trim', $row);

            $this->content[] = $row;
        }
    }


    /**
     * @param bool $doImport
     */
    public function prepareContent(bool $doImport = false)
    {
        foreach ($this->content as $key => $content) {
            $parentName = $content[0];
            $type = '';
            if (! empty($content[1])) {
                $type = $content[1];
            }
            $country = '';
            if (empty($content[2])) {
                $country = $content[2];
            }

            var_dump($parentName);
            var_dump($type);
            var_dump($country);
        }
    }

    public function validateData()
    {
        // TODO: Implement validateData() method.
    }
}
