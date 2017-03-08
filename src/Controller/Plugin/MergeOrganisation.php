<?php
/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Affiliation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/affiliation for the canonical source repository
 */

namespace Organisation\Controller\Plugin;

use Organisation\Entity\Organisation;

/**
 * Class MergeOrganisation
 *
 * @package Organisation\Controller\Plugin
 */
class MergeOrganisation extends AbstractOrganisationPlugin
{
    /**
     * @return MergeOrganisation
     */
    public function __invoke()
    {
        return $this;
    }

    /**
     * @param Organisation $source
     * @param Organisation $destination
     * @return array
     */
    public function checkMerge(Organisation $source, Organisation $destination)
    {
        $errors = [];

        // Check VAT
        if (!is_null($destination->getFinancial()) && !is_null($source->getFinancial())
            && ($destination->getFinancial()->getVat() !==  $source->getFinancial()->getVat())
        ) {
            $errors[] = '';
        }

        return $errors;
    }

    /**
     * @param Organisation $source
     * @param Organisation $destination
     */
    public function merge(Organisation $source, Organisation $destination)
    {

    }
}
