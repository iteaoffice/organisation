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
use Organisation\Search\Service\AdvisoryBoard\CitySearchService;
use Organisation\Service\AbstractService;
use Search\Service\AbstractSearchService;
use Search\Service\SearchUpdateInterface;
use Solarium\Client;
use Solarium\Core\Client\Adapter\Http;
use Solarium\Core\Query\AbstractQuery;
use Solarium\QueryType\Update\Query\Document;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 *
 */
class CityService extends AbstractService implements SearchUpdateInterface
{
    private CitySearchService $citySearchService;
    private TranslatorInterface $translator;

    public function __construct(EntityManager $entityManager, CitySearchService $citySearchService, TranslatorInterface $translator)
    {
        parent::__construct($entityManager);

        $this->citySearchService = $citySearchService;
        $this->translator        = $translator;
    }


    public function findCityById(int $id): ?Entity\AdvisoryBoard\City
    {
        return $this->entityManager->getRepository(Entity\AdvisoryBoard\City::class)->find($id);
    }

    public function canDeleteCity(Entity\AdvisoryBoard\City $city): bool
    {
        return true;
    }

    public function updateCollectionInSearchEngine(
        bool $clearIndex = false,
        int  $limit = 25
    ): void
    {
        $this->updateCollectionInSearchEngineByEntity(
            Entity\AdvisoryBoard\City::class,
            $this->citySearchService,
            $clearIndex,
            $limit
        );
    }

    public function delete(Entity\AbstractEntity $abstractEntity): void
    {
        if ($abstractEntity instanceof Entity\AdvisoryBoard\Solution) {
            $this->citySearchService->deleteDocument($abstractEntity);
        }

        parent::delete($abstractEntity);
    }

    public function save(Entity\AbstractEntity $abstractEntity): Entity\AbstractEntity
    {
        parent::save($abstractEntity);

        if ($abstractEntity instanceof Entity\AdvisoryBoard\City) {
            $this->updateEntityInSearchEngine($abstractEntity);
        }

        return $abstractEntity;
    }

    /**
     * @param Entity\AdvisoryBoard\City $city
     */
    public function updateEntityInSearchEngine($city): void
    {
        $document = $this->prepareSearchUpdate($city);

        $this->citySearchService->executeUpdateDocument($document);
    }

    /**
     * @param Entity\AdvisoryBoard\City $city
     * @return AbstractQuery
     */
    public function prepareSearchUpdate($city): AbstractQuery
    {
        $searchClient = new Client(new Http(), new EventDispatcher(), []);
        $update       = $searchClient->createUpdate();

        /** @var Document $cityDocument */
        $cityDocument = $update->createDocument();

        // Organisation properties
        $cityDocument->setField('id', $city->getResourceId());
        $cityDocument->setField('city_id', $city->getId());
        $cityDocument->setField('doc_ref', $city->getDocRef());
        $cityDocument->setField('date_created', $city->getDateCreated()->format(AbstractSearchService::DATE_SOLR));
        if (null !== $city->getDateUpdated()) {
            $cityDocument->setField('date_updated', $city->getDateUpdated()->format(AbstractSearchService::DATE_SOLR));
        }
        $cityDocument->setField('name', $city->getName());
        $cityDocument->setField('contact', $city->getContact()->parseFullName());
        $cityDocument->setField('contact_id', $city->getContact()->getId());

        $cityDocument->setField('language', $city->getLanguage()->getLanguage());
        $cityDocument->setField('language_id', $city->getLanguage()->getId());

        $cityDocument->setField('country', $city->getCountry()->getCountry());
        $cityDocument->setField('country_id', $city->getContact()->getId());

        $cityDocument->setField('website', $city->getWebsite());
        $cityDocument->setField('tender_website', $city->getTenderWebsite());
        $cityDocument->setField('has_tender_website', $city->hasTenderWebsite());

        $cityDocument->setField('hidden', $city->getHidden());
        $cityDocument->setField('hidden_text', $this->translator->translate($city->getHiddenText()));
        $cityDocument->setField('is_hidden', $city->isHidden());

        $update->addDocument($cityDocument);
        $update->addCommit();

        return $update;
    }
}
