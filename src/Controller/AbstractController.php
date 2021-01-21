<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Organisation\Controller;

use BjyAuthorize\Controller\Plugin\IsAllowed;
use Contact\Entity\Contact;
use Invoice\Controller\Plugin\GetFilter as InvoiceFilterPlugin;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Laminas\Mvc\Plugin\Identity\Identity;
use Organisation\Controller\Plugin\GetFilter as OrganisationFilterPlugin;
use Organisation\Controller\Plugin\HandleParentAndProjectImport;
use Organisation\Controller\Plugin\Merge\OrganisationMerge;
use Organisation\Controller\Plugin\Merge\ParentOrganisationMerge;
use Organisation\Controller\Plugin\RenderOverviewExtraVariableContributionSheet;
use Organisation\Controller\Plugin\RenderOverviewVariableContributionSheet;
use Organisation\Controller\Plugin\SelectionExport;
use Organisation\Entity;
use Program\Entity\Program;

/**
 * @method      Identity|Contact identity()
 * @method      FlashMessenger flashMessenger()
 * @method      IsAllowed isAllowed($resource, $action)
 * @method      InvoiceFilterPlugin getInvoiceFilter()
 * @method      OrganisationFilterPlugin getOrganisationFilter()
 * @method      RenderOverviewVariableContributionSheet|\TCPDF renderOverviewVariableContributionSheet(Entity\ParentEntity $parent, Program $program, int $year)
 * @method      RenderOverviewExtraVariableContributionSheet|\TCPDF renderOverviewExtraVariableContributionSheet(Entity\ParentEntity $parent, Program $program, int $year)
 * @method      HandleParentAndProjectImport handleParentAndProjectImport($fileData, $keys, $doImport)
 * @method      OrganisationMerge organisationMerge()
 * @method      ParentOrganisationMerge parentOrganisationMerge(Entity\Parent\Organisation $target, Entity\Parent\Organisation $source)
 * @method      SelectionExport organisationSelectionExport(Entity\Selection $selection, int $type)
 */
abstract class AbstractController extends AbstractActionController
{
}
