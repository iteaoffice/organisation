<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Organisation\Form;

use Doctrine\ORM\EntityManager;
use DoctrineORMModule\Form\Element\EntityMultiCheckbox;
use Program\Entity\Program;
use Zend\Form;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Class CreateParentDoa
 * @package Organisation\Form
 */
class CreateParentDoa extends Form\Form implements InputFilterProviderInterface
{
    /**
     * ParentDoa constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct();
        $this->setAttribute('method', 'post');
        $this->setAttribute('action', '');
        $this->setAttribute('class', 'form-horizontal');

        $this->add(
            [
                'type'    => EntityMultiCheckbox::class,
                'name'    => 'program',
                'options' => [
                    'target_class'   => Program::class,
                    'find_method'    => [
                        'name'   => 'findBy',
                        'params' => [
                            'criteria' => [],
                            'orderBy'  => [
                                'program' => 'ASC',
                            ],
                        ],
                    ],
                    'object_manager' => $entityManager,
                    'help-block'     => _('txt-create-doa-for-program-help-block'),
                    'label'          => _("txt-create-doa-for-program-label"),
                ],
            ]
        );
        $this->add(
            [
                'type'       => Form\Element\Submit::class,
                'name'       => 'submit',
                'attributes' => [
                    'class' => 'btn btn-primary',
                    'value' => _('txt-create-parent-doa'),
                ],
            ]
        );
    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification(): array
    {
        return [
            'program' => [
                'required' => true,
            ],
        ];
    }
}
