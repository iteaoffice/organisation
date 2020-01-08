<?php

/**
 * ITEA Office all rights reserved
 *
 * @category    Content
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Organisation\Form;

use Doctrine\ORM\EntityManager;
use Organisation\Entity\Organisation;
use Organisation\Repository\Organisation as OrganisationRepository;
use Laminas\Form\Element;
use Laminas\Form\Form;

/**
 * Class OrganisationMerge
 *
 * @package Organisation\Form
 */
class OrganisationMerge extends Form
{
    /**
     * OrganisationMerge constructor.
     * @param EntityManager $entityManager
     * @param Organisation $destination
     */
    public function __construct(EntityManager $entityManager = null, Organisation $destination = null)
    {
        parent::__construct('organisation_merge');

        $this->setAttribute('id', 'organisation-merge');
        $this->setAttribute('method', 'post');
        $this->setAttribute('action', '');

        $mainSuggestions = [];
        if ($entityManager !== null && $destination !== null) {
            /** @var OrganisationRepository $repository */
            $repository = $entityManager->getRepository(Organisation::class);
            $suggestions = $repository->findMergeCandidatesFor($destination);
            /** @var Organisation $organisation */
            foreach ($suggestions as $organisation) {
                $mainSuggestions[$organisation->getId()] = sprintf(
                    '%s (%s)',
                    $organisation->getOrganisation(),
                    $organisation->getCountry()->getCountry()
                );
            }
        }

        $this->add([
            'type'    => Element\Radio::class,
            'name'    => 'source-main',
            'options' => [
                'label'         => '',
                'value_options' => $mainSuggestions
            ],
        ]);

        $this->add([
            'type'       => Element\Select::class,
            'name'       => 'source-search',
            'attributes' => [
                'id' => 'source-search',
            ],
        ]);

        $this->add([
            'type'       => Element\Submit::class,
            'name'       => 'preview',
            'attributes' => [
                'id'    => 'btn-preview',
                'class' => 'btn btn-primary',
                'value' => _('txt-preview-merge'),
            ],
        ]);

        $this->add([
            'type'       => Element\Submit::class,
            'name'       => 'merge',
            'attributes' => [
                'class' => 'btn btn-danger',
                'value' => _('txt-merge'),
            ],
        ]);

        $this->add([
            'type'       => Element\Submit::class,
            'name'       => 'cancel',
            'attributes' => [
                'class' => 'btn btn-warning',
                'value' => _('txt-cancel'),
            ],
        ]);

        $this->add([
            'type'       => Element\Submit::class,
            'name'       => 'swap',
            'attributes' => [
                'class' => 'btn btn-primary',
                'value' => _('txt-swap-source-and-destination'),
            ],
        ]);
    }
}
