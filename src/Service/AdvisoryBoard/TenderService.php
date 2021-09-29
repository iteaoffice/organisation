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
use Organisation\Search\Service\AdvisoryBoard\TenderSearchService;
use Organisation\Service\AbstractService;
use Search\Service\AbstractSearchService;
use Search\Service\SearchUpdateInterface;
use Solarium\Client;
use Solarium\Core\Query\AbstractQuery;
use Solarium\QueryType\Update\Query\Document;

/**
 *
 */
class TenderService extends AbstractService implements SearchUpdateInterface
{
    private TenderSearchService $tenderSearchService;
    private TranslatorInterface $translator;

    public function __construct(EntityManager $entityManager, TenderSearchService $tenderSearchService, TranslatorInterface $translator)
    {
        parent::__construct($entityManager);

        $this->tenderSearchService = $tenderSearchService;
        $this->translator          = $translator;
    }

    public function findTenderById(int $id): ?Entity\AdvisoryBoard\Tender
    {
        return $this->entityManager->getRepository(Entity\AdvisoryBoard\Tender::class)->find($id);
    }

    public function canDeleteTender(Entity\AdvisoryBoard\Tender $tender): bool
    {
        return true;
    }

    public function canDeleteType(Entity\AdvisoryBoard\Tender\Type $type): bool
    {
        $cannotDeleteTenderTypeReasons = [];

        if (! $type->getTenders()->isEmpty()) {
            $cannotDeleteTenderTypeReasons[] = $this->translator->translate('txt-tender-type-has-tenders');
        }

        return count($cannotDeleteTenderTypeReasons) === 0;
    }

    public function updateCollectionInSearchEngine(
        bool $clearIndex = false,
        int $limit = 25
    ): void {
        $this->updateCollectionInSearchEngineByEntity(
            Entity\AdvisoryBoard\Tender::class,
            $this->tenderSearchService,
            $clearIndex,
            $limit
        );
    }

    /**
     * @param Entity\AdvisoryBoard\Tender $tender
     */
    public function updateEntityInSearchEngine($tender): void
    {
        $document = $this->prepareSearchUpdate($tender);

        $this->tenderSearchService->executeUpdateDocument($document);
    }

    /**
     * @param Entity\AdvisoryBoard\Tender $tender
     * @return AbstractQuery
     */
    public function prepareSearchUpdate($tender): AbstractQuery
    {
        $searchClient = new Client();
        $update       = $searchClient->createUpdate();

        /** @var Document $tenderDocument */
        $tenderDocument = $update->createDocument();

        // Tender properties
        $tenderDocument->setField('id', $tender->getResourceId());
        $tenderDocument->setField('tender_id', $tender->getId());
        $tenderDocument->setField('doc_ref', $tender->getDocRef());
        $tenderDocument->setField('date_created', $tender->getDateCreated()->format(AbstractSearchService::DATE_SOLR));
        if (null !== $tender->getDateUpdated()) {
            $tenderDocument->setField('date_updated', $tender->getDateUpdated()->format(AbstractSearchService::DATE_SOLR));
        }

        $tenderDocument->setField('title', $tender->getTitle());
        $tenderDocument->setField('description', $tender->getDescription());

        if (null !== $tender->getDeadline()) {
            $tenderDocument->setField('deadline', $tender->getDeadline()->format(AbstractSearchService::DATE_SOLR));
        }
        if (null !== $tender->getDateApproved()) {
            $tenderDocument->setField('date_approved', $tender->getDateApproved()->format(AbstractSearchService::DATE_SOLR));
        }

        $tenderDocument->setField('is_approved', $tender->isApproved());
        $tenderDocument->setField('is_approved_text', $tender->isApproved() ? $this->translator->translate('txt-yes') : $this->translator->translate('txt-no'));

        $tenderDocument->setField('type_id', $tender->getType()->getId());
        $tenderDocument->setField('type', $tender->getType()->getType());

        $tenderDocument->setField('city_id', $tender->getCity()->getId());
        $tenderDocument->setField('city', $tender->getCity()->getName());

        $tenderDocument->setField('country_id', $tender->getCity()->getCountry()->getId());
        $tenderDocument->setField('country', $tender->getCity()->getCountry()->getCountry());

        $tenderDocument->setField('contact_id', $tender->getContact()->getId());
        $tenderDocument->setField('contact', $tender->getContact()->parseFullName());

        $update->addDocument($tenderDocument);
        $update->addCommit();

        return $update;
    }
}
