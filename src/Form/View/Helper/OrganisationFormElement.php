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
use Laminas\I18n\Translator\Translator;
use Laminas\View\HelperPluginManager;
use Organisation\Entity\Organisation;
use Organisation\Service\OrganisationService;
use Zf3Bootstrap4\Form\View\Helper\FormElement;

/**
 * Class OrganisationFormElement
 *
 * @package Organisation\Form\View\Helper
 */
final class OrganisationFormElement extends FormElement
{
    private OrganisationService $organisationService;

    public function __construct(
        OrganisationService $organisationService,
        HelperPluginManager $viewHelperManager,
        Translator $translator
    ) {
        parent::__construct($viewHelperManager, $translator);

        $this->organisationService = $organisationService;
    }

    public function __invoke(ElementInterface $element = null, bool $inline = false, bool $formElementOnly = false)
    {
        $this->inline          = $inline;
        $this->formElementOnly = $formElementOnly;

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
                $('.selectpicker-organisation').selectpicker().ajaxSelectPicker();",
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

        $element->setAttribute('class', 'form-control selectpicker selectpicker-organisation');
        $element->setAttribute('data-live-search', 'true');
        $element->setAttribute('data-abs-ajax-url', 'organisation/json/search.json');

        //When we have a value, inject the corresponding contact in the value options
        if (null !== $element->getValue()) {
            $value = $element->getValue();
            if ($element->getValue() instanceof Organisation) {
                $value = $element->getValue()->getId();
            }

            $organisation = $this->organisationService->findOrganisationById((int)$value);
            if (null !== $organisation) {
                $element->setValueOptions([$organisation->getId() => $organisation->parseFormName()]);
            }
        }

        return parent::render($element);
    }
}
