<?php

/**
 * Jield BV all rights reserved.
 *
 * @category    Equipment
 *
 * @author      Dr. ir. Johan van der Heide <info@jield.nl>
 * @copyright   Copyright (c) 2004-2017 Jield BV (https://jield.nl)
 */

declare(strict_types=1);

namespace Organisation\Form\View\Helper;

use Laminas\Form\ElementInterface;
use LaminasBootstrap5\Form\View\Helper\FormElement;

/**
 * Class ParentFormElement
 *
 * @package Organisation\Form\View\Helper
 */
final class ParentFormElement extends FormElement
{
    public function __invoke(ElementInterface $element = null, $type = self::TYPE_HORIZONTAL, bool $formElementOnly = false)
    {
        $this->type          = $type;

        $this->view->headLink()
            ->appendStylesheet('/assets/css/bootstrap-select.min.css');
        $this->view->headLink()
            ->appendStylesheet('/assets/css/ajax-bootstrap-select.min.css');
        $this->view->headScript()->appendFile(
            '/assets/js/bootstrap-select.min.js',
            'text/javascript'
        );
        $this->view->headScript()->appendFile(
            '/assets/js/ajax-bootstrap-select.min.js',
            'text/javascript'
        );
        $this->view->inlineScript()->appendScript(
            "
                $('.selectpicker-parent').selectpicker().ajaxSelectPicker();",
            'text/javascript'
        );


        if ($element) {
            return $this->render($element);
        }

        return $this;
    }

    public function render(ElementInterface $element): string
    {
        $element->setValueOptions($element->getValueOptions());

        $element->setAttribute('class', 'form-control selectpicker selectpicker-parent');
        $element->setAttribute('data-live-search', 'true');
        $element->setAttribute('data-abs-ajax-url', 'organisation/json/search-parent.json');

        $element->setValue($element->getValue());

        return parent::render($element);
    }
}
