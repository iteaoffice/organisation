<?php
/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Affiliation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/affiliation for the canonical source repository
 */

declare(strict_types=1);

namespace Organisation\Controller\Plugin;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Organisation\Controller\OrganisationAbstractController;
use Organisation\Entity\Log;
use Organisation\Entity\Logo;
use Organisation\Entity\Note;
use Organisation\Entity\Organisation;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\Log\LoggerInterface;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * Class MergeOrganisation
 *
 * @package Organisation\Controller\Plugin
 */
class MergeOrganisation extends AbstractPlugin
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * MergeOrganisation constructor.
     * @param EntityManagerInterface $entityManager
     * @param TranslatorInterface    $translator
     */
    public function __construct(EntityManagerInterface $entityManager, TranslatorInterface $translator)
    {
        $this->entityManager = $entityManager;
        $this->translator    = $translator;
    }

    /**
     * @return MergeOrganisation
     */
    public function __invoke(): MergeOrganisation
    {
        return $this;
    }

    /**
     * @param Organisation $source
     * @param Organisation $target
     * @return array
     */
    public function checkMerge(Organisation $source, Organisation $target): array
    {
        $errors = [];

        // Check VAT
        if (!is_null($target->getFinancial()) && !is_null($source->getFinancial())
            && ($target->getFinancial()->getVat() !== $source->getFinancial()->getVat())
        ) {
            $errors[] = sprintf(
                $this->translate('txt-cannot-merge-VAT-%s-and-%s'),
                $target->getFinancial()->getVat(),
                $source->getFinancial()->getVat()
            );
        }

        // Organisations can't both be parents
        if (!is_null($target->getParent()) && !is_null($source->getParent())) {
            $errors[] = $this->translate('txt-organisations-cant-both-be-parents');
        }

        // Check countries
        if (!is_null($target->getCountry()) && !is_null($source->getCountry())
            && ($target->getCountry()->getId() !== $source->getCountry()->getId())
        ) {
            $errors[] = $this->translate('txt-organisations-cant-have-different-countries');
        }

        return $errors;
    }

    /**
     * @param Organisation         $source
     * @param Organisation         $target
     * @param LoggerInterface|null $logger
     * @return array
     */
    public function merge(Organisation $source, Organisation $target, LoggerInterface $logger = null): array
    {
        $response = ['success' => true, 'errorMessage' => ''];

        try {
            // Update organisation properties
            if (is_null($target->getType())) {
                $target->setType($source->getType());
            }
            if ($source->getDateCreated() < $target->getDateCreated()) {
                $target->setDateCreated($source->getDateCreated());
            }
            if ($source->getDateUpdated() > $target->getDateUpdated()) {
                $target->setDateUpdated($source->getDateUpdated());
            }
            if (is_null($target->getDescription()) && !is_null($source->getDescription())) {
                $source->getDescription()->setOrganisation($target);
                $target->setDescription($source->getDescription());
            }
            if (!is_null($source->getFinancial()) && (
                    is_null($target->getFinancial())
                    || empty($target->getFinancial()->getVat())
                    || ($source->getFinancial()->getVat() === $target->getFinancial()->getVat())
                )
            ) {
                $source->getFinancial()->setOrganisation($target);
                $target->setFinancial($source->getFinancial());
            }
            if ($target->getLogo()->isEmpty() && !$source->getLogo()->isEmpty()) {
                /** @var Logo $logo */
                $logo = $source->getLogo()->first();
                $logo->setOrganisation($target);
                $target->getLogo()->add($logo);
            }

            // Transfer log
            foreach ($source->getLog() as $key => $log) {
                $log->setOrganisation($target);
                $this->persist($log);
                $target->getLog()->add($log);
                $source->getLog()->remove($key);
            }

            // Transfer technology (many-to-many)
            foreach ($source->getTechnology() as $key => $technology) {
                $technology->getOrganisation()->removeElement($source);
                $technology->getOrganisation()->add($target);
                $this->persist($technology);
                $target->getTechnology()->add($technology);
                $source->getTechnology()->remove($key);
            }

            // Transfer websites
            foreach ($source->getWeb() as $key => $website) {
                $website->setOrganisation($target);
                $this->persist($website);
                $target->getWeb()->add($website);
                $source->getWeb()->remove($key);
            }

            // Transfer notes
            foreach ($source->getNote() as $key => $note) {
                $note->setOrganisation($target);
                $this->persist($note);
                $target->getNote()->add($note);
                $source->getNote()->remove($key);
            }

            // Transfer names
            foreach ($source->getNames() as $key => $name) {
                $name->setOrganisation($target);
                $this->persist($name);
                $target->getNames()->add($name);
                $source->getNames()->remove($key);
            }

            // Transfer affiliations
            foreach ($source->getAffiliation() as $key => $affiliation) {
                $affiliation->setOrganisation($target);
                $this->persist($affiliation);
                $target->getAffiliation()->add($affiliation);
                $source->getAffiliation()->remove($key);
            }

            // Transfer affiliation financial data
            foreach ($source->getAffiliationFinancial() as $key => $affiliationFinancial) {
                $affiliationFinancial->setOrganisation($target);
                $this->persist($affiliationFinancial);
                $target->getAffiliationFinancial()->add($affiliationFinancial);
                $source->getAffiliationFinancial()->remove($key);
            }

            // Transfer ICT organisations
            foreach ($source->getIctOrganisation() as $key => $ictOrganisation) {
                $ictOrganisation->setOrganisation($target);
                $this->persist($ictOrganisation);
                $target->getIctOrganisation()->add($ictOrganisation);
                $source->getIctOrganisation()->remove($key);
            }

            // Transfer cluster head
            foreach ($source->getCluster() as $key => $cluster) {
                $cluster->setOrganisation($target);
                $this->persist($cluster);
                $target->getCluster()->add($cluster);
                $source->getCluster()->remove($key);
            }

            // Transfer cluster memberships
            foreach ($source->getClusterMember() as $key => $clusterMember) {
                $clusterMember->setOrganisation($target);
                $this->persist($clusterMember);
                $target->getClusterMember()->add($clusterMember);
                $source->getClusterMember()->remove($key);
            }

            // Transfer contacts
            foreach ($source->getContactOrganisation() as $key => $contactOrganisation) {
                $contactOrganisation->setOrganisation($target);
                $this->persist($contactOrganisation);
                $target->getContactOrganisation()->add($contactOrganisation);
                $source->getContactOrganisation()->remove($key);
            }

            // Transfer booths
            foreach ($source->getOrganisationBooth() as $key => $organisationBooth) {
                $organisationBooth->setOrganisation($target);
                $this->persist($organisationBooth);
                $target->getOrganisationBooth()->add($organisationBooth);
                $source->getOrganisationBooth()->remove($key);
            }

            // Transfer booth financial data
            foreach ($source->getBoothFinancial() as $key => $boothFinancial) {
                $boothFinancial->setOrganisation($target);
                $this->persist($boothFinancial);
                $target->getBoothFinancial()->add($boothFinancial);
                $source->getBoothFinancial()->remove($key);
            }

            // Transfer idea partners
            foreach ($source->getIdeaPartner() as $key => $ideaPartner) {
                $ideaPartner->setOrganisation($target);
                $this->persist($ideaPartner);
                $target->getIdeaPartner()->add($ideaPartner);
                $source->getIdeaPartner()->remove($key);
            }

            // Transfer invoices
            foreach ($source->getInvoice() as $key => $invoice) {
                $invoice->setOrganisation($target);
                $this->persist($invoice);
                $target->getInvoice()->add($invoice);
                $source->getInvoice()->remove($key);
            }

            // Transfer invoice journal
            foreach ($source->getJournal() as $key => $journal) {
                $journal->setOrganisation($target);
                $this->persist($journal);
                $target->getJournal()->add($journal);
                $source->getJournal()->remove($key);
            }

            // Transfer program doa
            foreach ($source->getProgramDoa() as $key => $programDoa) {
                $programDoa->setOrganisation($target);
                $this->persist($programDoa);
                $target->getProgramDoa()->add($programDoa);
                $source->getProgramDoa()->remove($key);
            }

            // Transfer program call doa
            foreach ($source->getDoa() as $key => $callDoa) {
                $callDoa->setOrganisation($target);
                $this->persist($callDoa);
                $target->getDoa()->add($callDoa);
                $source->getDoa()->remove($key);
            }

            // Transfer reminders
            foreach ($source->getReminder() as $key => $reminder) {
                $reminder->setOrganisation($target);
                $this->persist($reminder);
                $target->getReminder()->add($reminder);
                $source->getReminder()->remove($key);
            }

            // Transfer results (many-to-many)
            foreach ($source->getResult() as $key => $result) {
                $result->getOrganisation()->removeElement($source);
                $result->getOrganisation()->add($target);
                $this->persist($result);
                $target->getResult()->add($result);
                $source->getResult()->remove($key);
            }

            // Persist main organisation, remove the other + flush and update permissions
            $this->persist($target);
            $sourceId = $source->getId();
            $this->entityManager->remove($source);
            $this->entityManager->flush();

            // Prepare for logging
            $message = sprintf(
                'Merged organisation %s (%d) into %s (%d)',
                $source->getOrganisation(),
                $sourceId,
                $target->getOrganisation(),
                $target->getId()
            );
            /** @var OrganisationAbstractController $controller */
            $controller = $this->getController();
            $contact = $controller->zfcUserAuthentication()->getIdentity();

            // Log the merge in the target organisation
            $organisationLog = new Log();
            $organisationLog->setOrganisation($target);
            $organisationLog->setContact($contact);
            $organisationLog->setLog($message);
            $this->persist($organisationLog);
            // Add a note to the target organisation about the merge
            $organisationNote = new Note();
            $organisationNote->setOrganisation($target);
            $organisationNote->setSource('auto');
            $organisationNote->setContact($contact);
            $organisationNote->setNote($message);
            $notes = $target->getNote()->toArray();
            array_unshift($notes, $organisationNote);
            $target->setNote(new ArrayCollection($notes));
            $this->persist($organisationNote);

            $this->entityManager->flush();
        } catch (ORMException $exception) {
            $response = ['success' => false, 'errorMessage' => $exception->getMessage()];
            if ($logger instanceof LoggerInterface) {
                $logger->err(sprintf(
                    '%s: %d %s',
                    $exception->getFile(),
                    $exception->getLine(),
                    $exception->getMessage()
                ));
            }
        }

        return $response;
    }

    /**
     * @param string $string
     * @return string
     */
    private function translate(string $string): string
    {
        return $this->translator->translate($string);
    }

    /**
     * Persist an object
     * @param object $object
     *
     */
    private function persist($object): void
    {
        $this->entityManager->persist($object);
    }
}
