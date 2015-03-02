<?php

/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */

namespace Organisation\View\Helper;

use Organisation\Entity\Organisation;
use Organisation\Service\OrganisationService;

/**
 * Create a link to an organisation.
 *
 * @category    Organisation
 */
class OrganisationLink extends LinkAbstract
{
    /**
     * @var OrganisationService
     */
    protected $organisationService;
    /**
     * The branch of the organisation.
     *
     * @var string
     */
    protected $branch;

    /**
     * @param OrganisationService $organisationService
     * @param string              $action
     * @param string              $show
     * @param null                $branch
     * @param null                $page
     * @param null                $alternativeShow
     *
     * @return string
     *
     * @throws \RuntimeException
     * @throws \Exception
     */
    public function __invoke(
        OrganisationService $organisationService = null,
        $action = 'view',
        $show = 'name',
        $branch = null,
        $page = null,
        $alternativeShow = null
    ) {
        $this->setOrganisationService($organisationService);
        $this->setAction($action);
        $this->setShow($show);
        /*
         * If the alternativeShow is not null, use it an otherwise take the page
         */
        if (!is_null($alternativeShow)) {
            $this->setAlternativeShow($alternativeShow);
        } else {
            $this->setAlternativeShow($page);
        }
        $this->addRouterParam('entity', 'organisation');
        if (!$this->getOrganisationService()->isEmpty()) {
            /*
             * Set the non-standard options needed to give an other link value
             */
            $this->setShowOptions(
                [
                    'more' => $this->translate("txt-read-more"),
                    'name' => $this->getOrganisationService()->parseOrganisationWithBranch(
                        $this->getBranch()
                    ),
                ]
            );
            $this->addRouterParam('id', $this->getOrganisationService()->getOrganisation()->getId());
        }
        $this->addRouterParam('page', $page);

        return $this->createLink();
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
        )
        ) {
            if ($this->getOrganisationService()->isEmpty()) {
                throw new \InvalidArgumentException(
                    sprintf(
                        "ProjectService is cannot be null for %s",
                        $this->getAction()
                    )
                );
            }
            $this->addRouterParam('docRef', $this->getOrganisationService()->getOrganisation()->getDocRef());
        }
        switch ($this->getAction()) {
            case 'new':
                $this->setRouter('zfcadmin/organisation-manager/new');
                $this->setText($this->translate("txt-new-organisation"));
                break;
            case 'edit':
                $this->setRouter('zfcadmin/organisation-manager/edit');
                $this->setText(
                    sprintf(
                        $this->translate("txt-edit-organisation-%s"),
                        $this->getOrganisationService()->parseOrganisationWithBranch(
                            $this->getBranch()
                        )
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
                $this->addRouterParam('docRef', $this->getOrganisationService()->getOrganisation()->getDocRef());
                $this->setRouter(
                    'route-'.$this->getOrganisationService()->getOrganisation()->get("underscore_full_entity_name")
                );
                $this->setText(
                    sprintf(
                        $this->translate("txt-view-organisation-%s"),
                        $this->getOrganisationService()->parseOrganisationWithBranch(
                            $this->getBranch()
                        )
                    )
                );
                break;
            case 'view-article':
                $this->setRouter(
                    'route-'.$this->getOrganisationService()->getOrganisation()->get(
                        "underscore_full_entity_name"
                    ).'-article'
                );
                $this->setText(
                    sprintf(
                        $this->translate("txt-view-article-for-organisation-%s"),
                        $this->getOrganisationService()->parseOrganisationWithBranch(
                            $this->getBranch()
                        )
                    )
                );
                $params['docRef'] = $this->getOrganisationService()->getOrganisation()->getDocRef();
                break;
            default:
                throw new \Exception(sprintf("%s is an incorrect action for %s", $this->getAction(), __CLASS__));
        }
    }

    /**
     * @return OrganisationService
     */
    public function getOrganisationService()
    {
        if (is_null($this->organisationService)) {
            $this->organisationService = new OrganisationService();
            $organisation              = new Organisation();
            $this->organisationService->setOrganisation($organisation);
        }

        return $this->organisationService;
    }

    /**
     * @param OrganisationService $organisationService
     */
    public function setOrganisationService($organisationService)
    {
        $this->organisationService = $organisationService;
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
}
