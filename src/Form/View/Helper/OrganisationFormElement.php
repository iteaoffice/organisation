<?php
/**
 * Jield BV all rights reserved.
 *
 * @category    Equipment
 *
 * @author      Dr. Ir. Johan van der Heide <info@jield.nl>
 * @copyright   Copyright (c) 2004-2017 Jield BV (http://jield.nl)
 */

declare(strict_types=1);

namespace Organisation\Form\View\Helper;

use Zend\Form\Element\Select;
use Zend\Form\ElementInterface;
use ZfcTwitterBootstrap\Form\View\Helper\FormElement;

/**
 * Form Element.
 */
class OrganisationFormElement extends FormElement
{
    /**
     * @var string
     */
    protected $groupWrapper = '<div class="form-group row %s" id="control-group-%s">%s</div>';

    public function __invoke(ElementInterface $element = null, $groupWrapper = null, $controlWrapper = null)
    {
        //Inject the javascript in the header
        $this->view->headLink()
            ->appendStylesheet('/assets/css/bootstrap-select.min.css');
        $this->view->headScript()
            ->appendFile(
                '/assets/js/bootstrap-select.min.js',
                'text/javascript'
            );
        $this->view->headScript()
            ->appendFile(
                '/assets/js/ajax-bootstrap-select.min.js',
                'text/javascript'
            );


        $this->view->inlineScript()->appendScript(
            "var options = {
        ajax: {
            url: '" . $this->view->url('organisation/json/search') . "',
            type: 'POST',
            dataType: 'json',
            data: {
                search: '{{{q}}}'
            }
        },
        locale: {
            emptyTitle: 'Select your organisation by start typing'
        },
        langCode: 'en',
    };
    $('.select-picker-organisation').selectpicker().ajaxSelectPicker(options);",
            'text/javascript'
        );

        if ($element) {
            return $this->render($element, $groupWrapper, $controlWrapper);
        }

        return $this;
    }

    public function render(ElementInterface $element, $groupWrapper = null, $controlWrapper = null): string
    {
        $labelHelper = $this->getLabelHelper();
        $escapeHelper = $this->getEscapeHtmlHelper();
        $elementHelper = $this->getElementHelper();
        $elementErrorHelper = $this->getElementErrorHelper();
        $descriptionHelper = $this->getDescriptionHelper();
        $groupWrapper = $groupWrapper ?: $this->groupWrapper;
        $controlWrapper = $controlWrapper ?: $this->controlWrapper;
        /*
         * Disable by default the inArrayValidator
         */
        $element->setDisableInArrayValidator(true);
        $elementHelper->getView();

        $id = $element->getAttribute('id') ?: $element->getAttribute('name');
        $element->setAttribute('class', 'form-control');

        $controlLabel = '';
        $label = $element->getLabel();
        if (empty($label)) {
            $label = $element->getOption('label') ?: $element->getAttribute('label');
        }

        if ($label && !$element->getOption('skipLabel')) {
            $controlLabel .= $labelHelper->openTag(
                [
                    'class' => 'col-md-3 ' . ($element->getOption('wrapCheckboxInLabel') ? 'checkbox'
                            : 'control-label col-form-label'),
                ] + ($element->hasAttribute('id') ? ['for' => $id] : [])
            );

            if (null !== ($translator = $labelHelper->getTranslator())) {
                $label = $translator->translate($label, $labelHelper->getTranslatorTextDomain());
            }
            if ($element->getOption('wrapCheckboxInLabel')) {
                $controlLabel .= $elementHelper->render($element) . ' ';
            }
            if ($element->getOption('skipLabelEscape')) {
                $controlLabel .= $label;
            } else {
                $controlLabel .= $escapeHelper($label);
            }
            $controlLabel .= $labelHelper->closeTag();
        }

        if ($element->getOption('wrapCheckboxInLabel')) {
            $controls = $controlLabel;
            $controlLabel = '';
        } else {
            $controls = $elementHelper->render($element);
        }

        $controls = str_replace(
            ['<select'],
            ['<select class="select-picker-organisation form-control" data-live-search="true"'],
            $controls
        );

        /***
         * Now apply the magic
         */
        if ($element->isMultiple()) {
            $controls = str_replace(['data-live-search="true"'], ['multiple data-live-search="true"'], $controls);
        }

        $html = $controlLabel . sprintf(
            $controlWrapper,
            $controls,
            $descriptionHelper->render($element),
            $elementErrorHelper->render($element)
        );
        $addtClass = ($element->getMessages()) ? ' has-error' : '';

        return sprintf($groupWrapper, $addtClass, $id, $html);
    }
}
