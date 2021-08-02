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
use Laminas\Form\View\Helper\FormSelect;

/**
 * Class OrganisationFormElement
 *
 * @package Organisation\Form\View\Helper
 */
final class OrganisationSelect extends FormSelect
{
    public function __invoke(ElementInterface $element = null)
    {
        $this->view->headLink()->appendStylesheet('/assets/bootstrap-select-1.14-dev/dist/css/bootstrap-select.min.css');
        $this->view->headScript()->appendFile('/assets/bootstrap-select-1.14-dev/dist/js/bootstrap-select.min.js', 'text/javascript');
        $this->view->headLink()->appendStylesheet('/assets/ajax-bootstrap-select/dist/css/ajax-bootstrap-select.css');
        $this->view->headScript()->appendFile('/assets/ajax-bootstrap-select/dist/js/ajax-bootstrap-select.js', 'text/javascript');
        $this->view->inlineScript()->appendScript("$('.selectpicker-organisation').selectpicker().ajaxSelectPicker();", 'text/javascript');

        if ($element) {
            return $this->render($element);
        }

        return $this;
    }

    public function render(ElementInterface $element): string
    {
        $element->setValueOptions($element->getValueOptions());

        $element->setAttribute('class', 'form-control selectpicker selectpicker-organisation');
        $element->setAttribute('data-live-search', 'true');
        $element->setAttribute('data-abs-ajax-url', 'organisation/json/search.json');

        $element->setValue($element->getValue());

        return parent::render($element);
    }
}
