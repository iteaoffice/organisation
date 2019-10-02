<?php

/**
*
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

declare(strict_types=1);

namespace Organisation\View\Helper;

use Thumbor\Url\Builder;
use Zend\View\Helper\ServerUrl;
use Zend\View\Helper\Url;

/**
 * Class LinkAbstract.
 */
abstract class ImageAbstract extends AbstractViewHelper
{
    /**
     * @var string
     */
    protected $router;
    /**
     * @var string
     */
    protected $imageId;
    /**
     * @var array List of parameters needed to construct the URL from the router
     */
    protected $routerParams = [];
    /**
     * @var array Classes to be given to the link
     */
    protected $classes = [];
    /**
     * @var int
     */
    protected $width;

    public function createImageUrl(bool $onlyUrl = false): string
    {
        $url = $this->getHelperPluginManager()->get(Url::class);
        $serverUrl = $this->getHelperPluginManager()->get(ServerUrl::class);
        /**
         * Get the thumber config
         */
        $config = $this->getServiceManager()->get('content_module_config');

        $thumberLink = Builder::construct(
            $config['image']['server'],
            $config['image']['secret'],
            $serverUrl() . $url($this->router, $this->routerParams)
        )
            ->fitIn($this->width, null)
            ->smartCrop(false);

        $imageUrl = '<img src="%s" id="%s" class="%s">';

        $image = sprintf(
            $imageUrl,
            $thumberLink,
            $this->imageId,
            implode(' ', $this->classes)
        );

        if ($onlyUrl) {
            return (string)$thumberLink;
        }

        $thumberLinkFull = Builder::construct(
            $config['image']['server'],
            $config['image']['secret'],
            $serverUrl() . $url($this->router, $this->routerParams)
        );

        return '<a href="' . $thumberLinkFull . '" class="thumbnail fancybox-thumbs" data-fancybox-group="album-6">'
            . $image . '</a>';
    }

    public function addRouterParam($key, $value, $allowNull = true): void
    {
        if (!$allowNull && null === $value) {
            throw new \InvalidArgumentException(sprintf("null is not allowed for %s", $key));
        }
        if (null !== $value) {
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
    public function setRouter($router): void
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
     * @return string
     */
    public function getImageId()
    {
        return $this->imageId;
    }

    /**
     * @param string $imageId
     */
    public function setImageId($imageId): void
    {
        $this->imageId = $imageId;
    }

    /**
     * @param string $classes
     *
     * @return $this
     */
    public function addClasses($classes): ImageAbstract
    {
        foreach ((array)$classes as $class) {
            $this->classes[] = $class;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getClasses(): array
    {
        return $this->classes;
    }

    /**
     * @param array $classes
     */
    public function setClasses($classes): void
    {
        $this->classes = $classes;
    }

    /**
     * @return int
     */
    public function getWidth(): int
    {
        return $this->width;
    }

    /**
     * @param int $width
     *
     * @return ImageAbstract
     */
    public function setWidth($width): ImageAbstract
    {
        $this->width = $width;

        return $this;
    }
}
