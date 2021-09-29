<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Organisation\Service\AdvisoryBoard;

use Doctrine\ORM\EntityManager;
use Laminas\I18n\Translator\TranslatorInterface;
use Organisation\Entity;
use Organisation\Search\Service\AdvisoryBoard\SolutionSearchService;
use Organisation\Service\AbstractService;
use Search\Service\AbstractSearchService;
use Search\Service\SearchUpdateInterface;
use Solarium\Client;
use Solarium\Core\Query\AbstractQuery;
use Solarium\QueryType\Update\Query\Document;

/**
 *
 */
class SolutionService extends AbstractService implements SearchUpdateInterface
{
    private SolutionSearchService $solutionSearchService;
    private TranslatorInterface $translator;

    public function __construct(EntityManager $entityManager, SolutionSearchService $solutionSearchService, TranslatorInterface $translator)
    {
        parent::__construct($entityManager);

        $this->solutionSearchService = $solutionSearchService;
        $this->translator            = $translator;
    }

    public function findSolutionById(int $id): ?Entity\AdvisoryBoard\Solution
    {
        return $this->entityManager->getRepository(Entity\AdvisoryBoard\Solution::class)->find($id);
    }

    public function canDeleteSolution(Entity\AdvisoryBoard\Solution $solution): bool
    {
        return true;
    }

    public function updateCollectionInSearchEngine(
        bool $clearIndex = false,
        int $limit = 25
    ): void {
        $this->updateCollectionInSearchEngineByEntity(
            Entity\AdvisoryBoard\Solution::class,
            $this->solutionSearchService,
            $clearIndex,
            $limit
        );
    }

    /**
     * @param Entity\AdvisoryBoard\Solution $solution
     */
    public function updateEntityInSearchEngine($solution): void
    {
        $document = $this->prepareSearchUpdate($solution);

        $this->solutionSearchService->executeUpdateDocument($document);
    }

    /**
     * @param Entity\AdvisoryBoard\Solution $solution
     * @return AbstractQuery
     */
    public function prepareSearchUpdate($solution): AbstractQuery
    {
        $searchClient = new Client();
        $update       = $searchClient->createUpdate();

        /** @var Document $solutionDocument */
        $solutionDocument = $update->createDocument();

        // Solution properties
        $solutionDocument->setField('id', $solution->getResourceId());
        $solutionDocument->setField('solution_id', $solution->getId());
        $solutionDocument->setField('doc_ref', $solution->getDocRef());
        $solutionDocument->setField('date_created', $solution->getDateCreated()->format(AbstractSearchService::DATE_SOLR));
        if (null !== $solution->getDateUpdated()) {
            $solutionDocument->setField('date_updated', $solution->getDateUpdated()->format(AbstractSearchService::DATE_SOLR));
        }

        $solutionDocument->setField('title', $solution->getTitle());
        $solutionDocument->setField('description', $solution->getDescription());
        $solutionDocument->setField('target_customers', $solution->getTargetedCustomers());

        $solutionDocument->setField('contact_id', $solution->getContact()->getId());
        $solutionDocument->setField('contact', $solution->getContact()->parseFullName());

        $update->addDocument($solutionDocument);
        $update->addCommit();

        return $update;
    }
}
