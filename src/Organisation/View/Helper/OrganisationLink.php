<?php

/**
 * Japaveh Webdesign copyright message placeholder
 *
 * @category    Organisation
 * @package     View
 * @subpackage  Helper
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Japaveh Webdesign (http://japaveh.nl)
 */
namespace Organisation\View\Helper;

use Zend\View\Helper\AbstractHelper;

use Organisation\Entity;
use Organisation\Service;

/**
 * Create a link to an organisation
 *
 * @category    Organisation
 * @package     View
 * @subpackage  Helper
 */
class OrganisationLink extends AbstractHelper
{

    /**
     * @param \Organisation\Entity\Organisation $organisationService
     * @param                                   $action
     * @param                                   $show
     *
     * @return string
     * @throws \RuntimeException
     * @throws \Exception
     */
    public function __invoke(
        Service\OrganisationService $organisationService = null,
        $action = 'view',
        $show = 'name',
        $branch = null)
    {
        $translate = $this->view->plugin('translate');
        $url       = $this->view->plugin('url');
        $serverUrl = $this->view->plugin('serverUrl');
        $isAllowed = $this->view->plugin('isAllowed');

//        if (!$isAllowed('organisation', $action)) {
//            if ($action === 'view' && $show === 'name') {
//                return $organisationService;
//            }
//
//            return '';
//        }

        switch ($action) {
            case 'new':
                $router              = 'zfcadmin/organisation-manager/new';
                $text                = sprintf($translate("txt-new-organisation"));
                $organisationService = new Entity\Organisation();
                break;
            case 'edit':
                $router = 'zfcadmin/organisation-manager/edit';
                $text   = sprintf($translate("txt-edit-organisation-%s"),
                    $organisationService->parseOrganisationWithBranch($branch)
                );
                break;
            case 'view':
                $router = 'route-' . $organisationService->getOrganisation()->get("underscore_full_entity_name");
                $text   = sprintf($translate("txt-view-organisation-%s"),
                    $organisationService->parseOrganisationWithBranch($branch)
                );
                break;
            default:
                throw new \Exception(sprintf("%s is an incorrect action for %s", $action, __CLASS__));
        }

        if (is_null($organisationService)) {
            throw new \RuntimeException(
                sprintf(
                    "Area needs to be an instance of %s, %s given in %s",
                    "Organisation\Entity\Organisation",
                    get_class($organisationService),
                    __CLASS__
                )
            );
        }

        $params = array(
            'id'     => $organisationService->getOrganisation()->getId(),
            'docRef' => $organisationService->getOrganisation()->getDocRef(),
            'entity' => 'organisation'
        );

        $classes     = array();
        $linkContent = array();

        switch ($show) {
            case 'icon':
                if ($action === 'edit') {
                    $linkContent[] = '<i class="icon-pencil"></i>';
                } elseif ($action === 'delete') {
                    $linkContent[] = '<i class="icon-remove"></i>';
                } else {
                    $linkContent[] = '<i class="icon-info-sign"></i>';
                }
                break;
            case 'button':
                $linkContent[] = '<i class="icon-pencil icon-white"></i> ' . $text;
                $classes[]     = "btn btn-primary";
                break;
            case 'name':
                $linkContent[] = $organisationService->parseOrganisationWithBranch($branch);
                break;
            default:
                $linkContent[] = $organisationService->parseOrganisationWithBranch($branch);
                break;
        }


        $uri = '<a href="%s" title="%s" class="%s">%s</a>';

        return sprintf(
            $uri,
            $serverUrl->__invoke() . $url($router, $params),
            $text,
            implode($classes),
            implode($linkContent)
        );
    }
}
