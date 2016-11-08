<?php

/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 */

namespace Organisation\View\Helper;

use Organisation\Entity\Organisation;

/**
 * Create a link to an organisation.
 *
 * @category    Organisation
 */
class OrganisationLink extends LinkAbstract
{
    /**
     * The branch of the organisation.
     *
     * @var string
     */
    protected $branch;

    /**
     * @param Organisation $organisation
     * @param string       $action
     * @param string       $show
     * @param null         $branch
     * @param null         $page
     * @param null         $alternativeShow
     *
     * @return string
     *
     * @throws \RuntimeException
     * @throws \Exception
     */
    public function __invoke(
        Organisation $organisation = null,
        $action = 'view',
        $show = 'name',
        $branch = null,
        $page = null,
        $alternativeShow = null
    ) {
        $this->setOrganisation($organisation);
        $this->setAction($action);
        $this->setShow($show);
        $this->setBranch($branch);
        /*
         * If the alternativeShow is not null, use it an otherwise take the page
         */
        $this->setAlternativeShow($alternativeShow);
        if (! is_null($organisation)) {
            /*
             * Set the non-standard options needed to give an other link value
             */
            $this->setShowOptions(
                [
                    'more'            => $this->translate("txt-read-more"),
                    'name'            => $this->getOrganisationService()
                        ->parseOrganisationWithBranch($this->getBranch(), $this->getOrganisation()),
                    'alternativeShow' => $this->getAlternativeShow(),
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
    public function parseAction()
    {
        if (in_array(
            $this->getAction(),
            [
            'view',
            'view-article',
            ]
        )) {
            if (is_null($this->getOrganisation())) {
                throw new \InvalidArgumentException(
                    sprintf(
                        "OrganisationService is cannot be null for %s",
                        $this->getAction()
                    )
                );
            }
            $this->addRouterParam('docRef', $this->getOrganisation()->getDocRef());
        }
        switch ($this->getAction()) {
            case 'new':
                $this->setRouter('zfcadmin/organisation/new');
                $this->setText($this->translate("txt-new-organisation"));
                break;
            case 'view-admin':
                $this->setRouter('zfcadmin/organisation/view');
                $this->setText(sprintf($this->translate("txt-view-organisation-%s"), $this->getOrganisation()));
                break;
            case 'edit':
                $this->setRouter('zfcadmin/organisation/edit');
                $this->setText(
                    sprintf(
                        $this->translate("txt-edit-organisation-%s"),
                        $this->getOrganisationService()
                        ->parseOrganisationWithBranch($this->getBranch(), $this->getOrganisation())
                    )
                );
                break;
            case 'edit-financial':
                $this->setRouter('zfcadmin/organisation/financial/edit');
                $this->setText(
                    sprintf(
                        $this->translate("txt-edit-financial-organisation-%s"),
                        $this->getOrganisationService()
                            ->parseOrganisationWithBranch($this->getBranch(), $this->getOrganisation())
                    )
                );
                break;
            case 'list-financial':
                $this->setRouter('zfcadmin/organisation/financial/list');
                $this->setText(sprintf($this->translate("txt-list-financial-organisations")));
                break;
            case 'add-affiliation':
                $this->setRouter('zfcadmin/organisation/add-affiliation');
                $this->setText(
                    sprintf(
                        $this->translate("txt-add-organisation-%s-to-project"),
                        $this->getOrganisationService()
                            ->parseOrganisationWithBranch($this->getBranch(), $this->getOrganisation())
                    )
                );
                break;
            case 'list':
                /*
                 * For a list in the front-end simply use the MatchedRouteName
                 */
                $this->setRouter($this->getRouteMatch()->getMatchedRouteName());
                /*
                 * Push the docRef in the params array
                 */
                $this->addRouterParam('docRef', $this->getRouteMatch()->getParam('docRef'));
                $this->setText($this->translate("txt-list-organisations"));
                break;
            case 'view':
                $this->addRouterParam('docRef', $this->getOrganisation()->getDocRef());
                $this->setRouter('route-organisation_entity_organisation');
                $this->setText(
                    sprintf(
                        $this->translate("txt-view-organisation-%s"),
                        $this->getOrganisationService()
                        ->parseOrganisationWithBranch($this->getBranch(), $this->getOrganisation())
                    )
                );
                break;
            case 'view-article':
                $this->setRouter('route-organisation_entity_organisation-article');
                $this->setText(
                    sprintf(
                        $this->translate("txt-view-article-for-organisation-%s"),
                        $this->getOrganisationService()
                            ->parseOrganisationWithBranch($this->getBranch(), $this->getOrganisation())
                    )
                );
                $this->addRouterParam('docRef', $this->getOrganisation()->getDocRef());
                break;
            default:
                throw new \Exception(sprintf("%s is an incorrect action for %s", $this->getAction(), __CLASS__));
        }
    }
}
