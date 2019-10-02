<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Organisation
 * @package     Controller
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Organisation\Controller;

use BjyAuthorize\Controller\Plugin\IsAllowed;
use Contact\Entity\Contact;
use Invoice\Controller\Plugin\GetFilter as InvoiceFilterPlugin;
use Organisation\Controller\Plugin\GetFilter as OrganisationFilterPlugin;
use Organisation\Controller\Plugin\HandleParentAndProjectImport;
use Organisation\Controller\Plugin\MergeOrganisation;
use Organisation\Controller\Plugin\MergeParentOrganisation;
use Organisation\Controller\Plugin\RenderOverviewExtraVariableContributionSheet;
use Organisation\Controller\Plugin\RenderOverviewVariableContributionSheet;
use Organisation\Entity;
use Program\Entity\Program;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Zend\Mvc\Plugin\Identity\Identity;
use ZfcUser\Controller\Plugin\ZfcUserAuthentication;

/**
 * @category    Organisation
 * @package     Controller
 * @method      Identity|Contact identity()
 * @method      ZfcUserAuthentication zfcUserAuthentication()
 * @method      FlashMessenger flashMessenger()
 * @method      IsAllowed isAllowed($resource, $action)
 * @method      InvoiceFilterPlugin getInvoiceFilter()
 * @method      OrganisationFilterPlugin getOrganisationFilter()
 * @method      RenderOverviewVariableContributionSheet renderOverviewVariableContributionSheet(Entity\OParent $parent, Program $program, int $year)
 * @method      RenderOverviewExtraVariableContributionSheet renderOverviewExtraVariableContributionSheet(Entity\OParent $parent, Program $program, int $year)
 * @method      HandleParentAndProjectImport handleParentAndProjectImport($fileData, $keys, $doImport)
 * @method      MergeOrganisation mergeOrganisation()
 * @method      MergeParentOrganisation mergeParentOrganisation(Entity\Parent\Organisation $target, Entity\Parent\Organisation $source)
 */
abstract class OrganisationAbstractController extends AbstractActionController
{
}
