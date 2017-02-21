<?php

/**
 * ITEA Office all rights reserved
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

namespace Organisation\View\Helper;

use BjyAuthorize\Controller\Plugin\IsAllowed;
use BjyAuthorize\Service\Authorize;
use Organisation\Acl\Assertion\AssertionAbstract;
use Organisation\Entity;
use Organisation\Entity\AbstractEntity;
use Organisation\Service\OrganisationService;
use Zend\View\Helper\ServerUrl;
use Zend\View\Helper\Url;

/**
 * Class AbstractLink.
 */
abstract class AbstractLink extends AbstractViewHelper
{
    /**
     * @var string Text to be placed as title or as part of the linkContent
     */
    protected $text;
    /**
     * @var string
     */
    protected $router;
    /**
     * @var Entity\Organisation
     */
    protected $organisation;
    /**
     * @var Entity\Type
     */
    protected $type;
    /**
     * @var string
     */
    protected $action;
    /**
     * @var string
     */
    protected $show;
    /**
     * @var string
     */
    protected $alternativeShow;
    /**
     * @var array List of parameters needed to construct the URL from the router
     */
    protected $routerParams = [];
    /**
     * @var array content of the link (will be imploded during creation of the link)
     */
    protected $linkContent = [];
    /**
     * @var array Classes to be given to the link
     */
    protected $classes = [];
    /**
     * @var array
     */
    protected $showOptions = [];
    /**
     * @var Entity\OParent
     */
    protected $parent;
    /**
     * @var Entity\Parent\Organisation
     */
    protected $parentOrganisation;
    /**
     * @var Entity\Parent\Type
     */
    protected $parentType;
    /**
     * @var Entity\Parent\Status
     */
    protected $parentStatus;
    /**
     * @var Entity\Parent\Doa
     */
    protected $doa;
    /**
     * @var int
     */
    protected $year;
    /**
     * @var int
     */
    protected $period;


    /**
     * This function produces the link in the end.
     *
     * @return string
     */
    public function createLink()
    {
        /**
         * @var $url Url
         */
        $url = $this->getHelperPluginManager()->get('url');
        /**
         * @var $serverUrl ServerUrl
         */
        $serverUrl = $this->getHelperPluginManager()->get('serverUrl');
        $this->linkContent = [];
        $this->classes = [];
        $this->parseAction();
        $this->parseShow();
        if ('social' === $this->getShow()) {
            return $serverUrl() . $url($this->router, $this->routerParams);
        }
        $uri = '<a href="%s" title="%s" class="%s">%s</a>';

        return sprintf(
            $uri,
            $serverUrl() . $url($this->router, $this->routerParams),
            htmlentities($this->text),
            implode(' ', $this->classes),
            in_array($this->getShow(), ['icon', 'button', 'alternativeShow'], true) ? implode('', $this->linkContent)
                : htmlentities(implode('', $this->linkContent))
        );
    }

    /**
     *
     */
    public function parseAction()
    {
        $this->action = null;
    }

    /**
     * @throws \Exception
     */
    public function parseShow()
    {
        switch ($this->getShow()) {
            case 'icon':
            case 'button':
                switch ($this->getAction()) {
                    case 'edit':
                    case 'edit-financial':
                        $this->addLinkContent('<i class="fa fa-pencil-square-o"></i>');
                        break;
                    case 'overview-variable-contribution-pdf':
                    case 'overview-extra-variable-contribution-pdf':
                        $this->addLinkContent('<i class="fa fa-file-pdf-o" aria-hidden="true"></i>');
                        break;
                    default:
                        $this->addLinkContent('<i class="fa fa-link"></i>');
                        break;
                }

                if ($this->getShow() === 'button') {
                    $this->addLinkContent(' ' . $this->getText());
                    $this->addClasses("btn btn-primary");
                }

                break;
            case 'text':
                $this->addLinkContent($this->getText());
                break;
            case 'paginator':
                if (is_null($this->getAlternativeShow())) {
                    throw new \InvalidArgumentException(
                        sprintf("alternativeShow cannot be null for a paginator link")
                    );
                }
                $this->addLinkContent($this->getAlternativeShow());
                break;
            case 'social':
                /*
                 * Social is treated in the createLink function, no content needs to be created
                 */

                return;
            default:
                if (!array_key_exists($this->getShow(), $this->showOptions)) {
                    throw new \InvalidArgumentException(
                        sprintf(
                            "The option \"%s\" should be available in the showOptions array, only \"%s\" are available",
                            $this->getShow(),
                            implode(', ', array_keys($this->showOptions))
                        )
                    );
                }
                $this->addLinkContent($this->showOptions[$this->getShow()]);
                break;
        }
    }

    /**
     * @return string
     */
    public function getShow()
    {
        return $this->show;
    }

    /**
     * @param string $show
     */
    public function setShow($show)
    {
        $this->show = $show;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param string $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * @param $linkContent
     *
     * @return $this
     */
    public function addLinkContent($linkContent)
    {
        if (!is_array($linkContent)) {
            $linkContent = [$linkContent];
        }
        foreach ($linkContent as $content) {
            $this->linkContent[] = $content;
        }

        return $this;
    }

    /**
     * @param $classes
     *
     * @return $this
     */
    public function addClasses($classes)
    {
        if (!is_array($classes)) {
            $classes = (array)$classes;
        }
        foreach ($classes as $class) {
            $this->classes[] = $class;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getAlternativeShow()
    {
        return $this->alternativeShow;
    }

    /**
     * @param string $alternativeShow
     */
    public function setAlternativeShow($alternativeShow)
    {
        $this->alternativeShow = $alternativeShow;
    }

    /**
     * Reset the params.
     */
    public function resetRouterParams()
    {
        $this->routerParams = [];
    }

    /**
     * @param $showOptions
     */
    public function setShowOptions($showOptions)
    {
        $this->showOptions = $showOptions;
    }

    /**
     * @param AbstractEntity $entity
     * @param string $assertion
     * @param string $action
     *
     * @return bool
     */
    public function hasAccess(AbstractEntity $entity, $assertion, $action)
    {
        $assertion = $this->getAssertion($assertion);
        if (!is_null($entity) && !$this->getAuthorizeService()->getAcl()->hasResource($entity)) {
            $this->getAuthorizeService()->getAcl()->addResource($entity);
            $this->getAuthorizeService()->getAcl()->allow([], $entity, [], $assertion);
        }
        if (!$this->isAllowed($entity, $action)) {
            return false;
        }

        return true;
    }

    /**
     * @param string $assertion
     *
     * @return AssertionAbstract
     */
    public function getAssertion($assertion)
    {
        return $this->getServiceManager()->get($assertion);
    }

    /**
     * @return Authorize
     */
    public function getAuthorizeService()
    {
        return $this->getServiceManager()->get(Authorize::class);
    }

    /**
     * @param null|AbstractEntity $resource
     * @param string $privilege
     *
     * @return bool
     */
    public function isAllowed($resource, $privilege = null)
    {
        /**
         * @var $isAllowed IsAllowed
         */
        $isAllowed = $this->getHelperPluginManager()->get('isAllowed');

        return $isAllowed($resource, $privilege);
    }

    /**
     * @return OrganisationService
     */
    public function getOrganisationService()
    {
        return $this->getServiceManager()->get(OrganisationService::class);
    }

    /**
     * Add a parameter to the list of parameters for the router.
     *
     * @param string $key
     * @param        $value
     * @param bool $allowNull
     */
    public function addRouterParam($key, $value, $allowNull = true)
    {
        if (!$allowNull && is_null($value)) {
            throw new \InvalidArgumentException(sprintf("null is not allowed for %s", $key));
        }
        if (!is_null($value)) {
            $this->routerParams[$key] = $value;
        }
    }

    /**
     * @return string
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * @param string $router
     */
    public function setRouter($router)
    {
        $this->router = $router;
    }

    /**
     * @return array
     */
    public function getRouterParams()
    {
        return $this->routerParams;
    }

    /**
     * @return Entity\Organisation
     */
    public function getOrganisation(): Entity\Organisation
    {
        if (is_null($this->organisation)) {
            $this->organisation = new Entity\Organisation();
        }

        return $this->organisation;
    }

    /**
     * @param Entity\Organisation $organisation
     *
     * @return AbstractLink
     */
    public function setOrganisation($organisation)
    {
        $this->organisation = $organisation;

        return $this;
    }

    /**
     * @return Entity\Type
     */
    public function getType(): Entity\Type
    {
        if (is_null($this->type)) {
            $this->type = new Entity\Type();
        }

        return $this->type;
    }

    /**
     * @param Entity\Type $type
     *
     * @return AbstractLink
     */
    public function setType($type): AbstractLink
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Entity\OParent
     */
    public function getParent(): Entity\OParent
    {
        if (is_null($this->parent)) {
            $this->parent = new Entity\OParent();
        }

        return $this->parent;
    }

    /**
     * @param Entity\OParent $parent
     *
     * @return AbstractLink
     */
    public function setParent(Entity\OParent $parent = null): AbstractLink
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Entity\Parent\Type
     */
    public function getParentType(): Entity\Parent\Type
    {
        if (is_null($this->parentType)) {
            $this->parentType = new Entity\Parent\Type();
        }

        return $this->parentType;
    }

    /**
     * @param \Organisation\Entity\Parent\Type $parentType
     *
     * @return AbstractLink
     */
    public function setParentType(Entity\Parent\Type $parentType = null): AbstractLink
    {
        $this->parentType = $parentType;

        return $this;
    }

    /**
     * @return Entity\Parent\Status
     */
    public function getParentStatus(): Entity\Parent\Status
    {
        if (is_null($this->parentStatus)) {
            $this->parentStatus = new Entity\Parent\Status();
        }

        return $this->parentStatus;
    }

    /**
     * @param \Organisation\Entity\Parent\Status $parentStatus
     *
     * @return AbstractLink
     */
    public function setParentStatus(Entity\Parent\Status $parentStatus = null): AbstractLink
    {
        $this->parentStatus = $parentStatus;

        return $this;
    }

    /**
     * @return Entity\Parent\Organisation
     */
    public function getParentOrganisation(): Entity\Parent\Organisation
    {
        if (is_null($this->parentOrganisation)) {
            $this->parentOrganisation = new Entity\Parent\Organisation();
        }

        return $this->parentOrganisation;
    }

    /**
     * @param \Organisation\Entity\Parent\Organisation $parentOrganisation
     *
     * @return AbstractLink
     */
    public function setParentOrganisation(Entity\Parent\Organisation $parentOrganisation = null): AbstractLink
    {
        $this->parentOrganisation = $parentOrganisation;

        return $this;
    }

    /**
     * @return Entity\Parent\Doa
     */
    public function getDoa(): Entity\Parent\Doa
    {
        if (is_null($this->doa)) {
            $this->doa = new Entity\Parent\Doa();
        }

        return $this->doa;
    }

    /**
     * @param Entity\Parent\Doa $doa
     *
     * @return AbstractLink
     */
    public function setDoa(Entity\Parent\Doa $doa = null): AbstractLink
    {
        $this->doa = $doa;

        return $this;
    }


    /**
     * @return int
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @param int $year
     *
     * @return AbstractLink
     */
    public function setYear(int $year = null): AbstractLink
    {
        $this->year = $year;

        return $this;
    }

    /**
     * @return int
     */
    public function getPeriod()
    {
        return $this->period;
    }

    /**
     * @param int $period
     *
     * @return AbstractLink
     */
    public function setPeriod(int $period = null): AbstractLink
    {
        $this->period = $period;

        return $this;
    }
}
