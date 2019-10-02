<?php

/**
 * ITEA Office all rights reserved
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Organisation\View\Helper;

use Content\Entity\Route;
use Organisation\Entity\Organisation;
use Organisation\Service\OrganisationService;
use Organisation\Acl\Assertion\Organisation as OrganisationAssertion;

/**
 * Create a link to an organisation.
 *
 * @category    Organisation
 */
class OrganisationLink extends AbstractLink
{
    /**
     * The branch of the organisation.
     *
     * @var string
     */
    protected $branch;

    public function __invoke(
        Organisation $organisation = null,
        $action = 'view',
        $show = 'name',
        $branch = null,
        $page = null,
        $alternativeShow = null
    ): string {
        $this->setOrganisation($organisation);
        $this->setAction($action);
        $this->setShow($show);
        $this->setBranch($branch);

        if (!$this->hasAccess($this->getOrganisation(), OrganisationAssertion::class, $this->getAction())) {
            return '';//$this->getAction() . ' is not allowed';
        }

        /*
         * If the alternativeShow is not null, use it an otherwise take the page
         */
        $this->setAlternativeShow($alternativeShow);
        if (null !== $organisation) {
            /*
             * Set the non-standard options needed to give an other link value
             */
            $this->setShowOptions(
                [
                    'more'             => $this->translator->translate("txt-read-more"),
                    'name'             => OrganisationService::parseBranch(
                        $this->getBranch(),
                        $this->getOrganisation()
                    ),
                    'name-and-country' => sprintf(
                        '%s (%s)',
                        OrganisationService::parseBranch(
                            $this->getBranch(),
                            $this->getOrganisation()
                        ),
                        $this->getOrganisation()->getCountry()
                    ),
                    'alternativeShow'  => $this->getAlternativeShow(),
                ]
            );
            $this->addRouterParam('id', $this->getOrganisation()->getId());
        }
        $this->addRouterParam('page', $page);

        return $this->createLink();
    }

    /**
     * @return string
     */
    public function getBranch()
    {
        return $this->branch;
    }

    /**
     * @param string $branch
     */
    public function setBranch($branch)
    {
        $this->branch = $branch;
    }

    /**
     * @throws \Exception
     */
    public function parseAction(): void
    {
        if (\in_array($this->getAction(), ['view', 'view-article'], true)) {
            if (null === $this->getOrganisation()) {
                throw new \InvalidArgumentException(
                    sprintf("Organisation is cannot be null for %s", $this->getAction())
                );
            }
            $this->addRouterParam('docRef', $this->getOrganisation()->getDocRef());
        }
        switch ($this->getAction()) {
            case 'new':
                $this->setRouter('zfcadmin/organisation/new');
                $this->setText($this->translator->translate("txt-new-organisation"));
                break;
            case 'view-admin':
                $this->setRouter('zfcadmin/organisation/view');
                $this->setText(
                    sprintf($this->translator->translate("txt-view-organisation-%s"), $this->getOrganisation())
                );
                break;
            case 'edit':
                $this->setRouter('zfcadmin/organisation/edit');
                $this->setText(
                    sprintf(
                        $this->translator->translate("txt-edit-organisation-%s"),
                        OrganisationService::parseBranch($this->getBranch(), $this->getOrganisation())
                    )
                );
                break;
            case 'manage-web':
                $this->setRouter('zfcadmin/organisation/manage-web');
                $this->setText(
                    sprintf(
                        $this->translator->translate("txt-manage-web-organisation-%s"),
                        OrganisationService::parseBranch($this->getBranch(), $this->getOrganisation())
                    )
                );
                break;
            case 'edit-financial':
                $this->setRouter('zfcadmin/organisation/financial/edit');
                $this->setText(
                    sprintf(
                        $this->translator->translate("txt-edit-financial-organisation-%s"),
                        OrganisationService::parseBranch($this->getBranch(), $this->getOrganisation())
                    )
                );
                break;
            case 'list-financial':
                $this->setRouter('zfcadmin/organisation/financial/list');
                $this->setText(sprintf($this->translator->translate("txt-list-financial-organisations")));
                break;
            case 'add-affiliation':
                $this->setRouter('zfcadmin/organisation/add-affiliation');
                $this->setText(
                    sprintf(
                        $this->translator->translate("txt-add-organisation-%s-to-project"),
                        OrganisationService::parseBranch($this->getBranch(), $this->getOrganisation())
                    )
                );
                break;
            case 'list':
                // For a list in the front-end simply use the MatchedRouteName
                $this->setRouter($this->getRouteMatch()->getMatchedRouteName());
                // Push the docRef in the params array
                $this->addRouterParam('docRef', $this->getRouteMatch()->getParam('docRef'));
                $this->setText($this->translator->translate("txt-list-organisations"));
                break;
            case 'list-admin':
                $this->setRouter('zfcadmin/organisation/list');
                $this->setRouter($this->getRouteMatch()->getMatchedRouteName());
                $this->setText($this->translator->translate('txt-list-organisations'));
                break;
            case 'view':
                $this->addRouterParam('docRef', $this->getOrganisation()->getDocRef());
                $this->setRouter(Route::parseRouteName(Route::DEFAULT_ROUTE_ORGANISATION));
                $this->setText(
                    sprintf(
                        $this->translator->translate("txt-view-organisation-%s"),
                        OrganisationService::parseBranch($this->getBranch(), $this->getOrganisation())
                    )
                );
                break;
            default:
                throw new \Exception(sprintf("%s is an incorrect action for %s", $this->getAction(), __CLASS__));
        }
    }
}
