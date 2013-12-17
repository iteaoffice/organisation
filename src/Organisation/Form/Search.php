<?php
/**
 * Japaveh Webdesign copyright message placeholder
 *
 * @category    Project
 * @package     Form
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Japaveh Webdesign (http://japaveh.nl)
 */
namespace Organisation\Form;

use Zend\Form\Form;

/**
 *
 */
class Search extends Form
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-inline');


        $this->add(
            array(
                'type'       => 'Zend\Form\Element\Text',
                'name'       => 'search',
                'attributes' => array(
                    'label'       => 'search',
                    'class'       => 'form-control',
                    'id'          => "search",
                    'placeholder' => _("txt-organisation-search-as-you-type")
                )
            )
        );

        $this->add(
            array(
                'type'       => 'Zend\Form\Element\Submit',
                'name'       => 'submit',
                'attributes' => array(
                    'class' => "btn btn-primary",
                    'value' => _("txt-submit")
                )
            )
        );
    }
}
