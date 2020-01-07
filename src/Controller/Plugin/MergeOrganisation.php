<?php
/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Affiliation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/affiliation for the canonical source repository
 */

declare(strict_types=1);

namespace Organisation\Controller\Plugin;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use ErrorHeroModule\Handler\Logging;
use General\Entity\Country;
use Organisation\Controller\OrganisationAbstractController;
use Organisation\Entity\Description;
use Organisation\Entity\Financial;
use Organisation\Entity\Log;
use Organisation\Entity\Logo;
use Organisation\Entity\Note;
use Organisation\Entity\OParent;
use Organisation\Entity\Organisation;
use Organisation\Service\UpdateService;
use Laminas\Http\PhpEnvironment\Request;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Mvc\Controller\Plugin\AbstractPlugin;

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
     * @var UpdateService
     */
    private $updateService;
    /**
     * @var TranslatorInterface
     */
    private $translator;
    /**
     * @var Logging
     */
    private $errorLogger;

    public function __construct(
        EntityManagerInterface $entityManager,
        UpdateService          $updateService,
        TranslatorInterface    $translator,
        Logging                $errorLogger = null
    ) {
        $this->entityManager = $entityManager;
        $this->updateService = $updateService;
        $this->translator    = $translator;
        $this->errorLogger   = $errorLogger;
    }

    public function __invoke(): MergeOrganisation
    {
        return $this;
    }

    public function checkMerge(Organisation $source, Organisation $target): array
    {
        $errors = [];

        // Check VAT
        if (($target->getFinancial() instanceof Financial) && ($source->getFinancial() instanceof Financial)
            && ($target->getFinancial()->getVat() !== $source->getFinancial()->getVat())
        ) {
            $errors[] = \sprintf(
                $this->translator->translate('txt-cannot-merge-VAT-%s-and-%s'),
                $target->getFinancial()->getVat(),
                $source->getFinancial()->getVat()
            );
        }

        // Organisations can't both be parents
        if (($target->getParent() instanceof OParent) && ($source->getParent() instanceof OParent)) {
            $errors[] = $this->translator->translate('txt-organisations-cant-both-be-parents');
        }

        // Check countries
        if (($target->getCountry() instanceof Country) && ($source->getCountry() instanceof Country)
            && ($target->getCountry()->getId() !== $source->getCountry()->getId())
        ) {
            $errors[] = $this->translator->translate('txt-organisations-cant-have-different-countries');
        }

        // Check for pending updates
        if ($this->updateService->hasPendingUpdates($source)) {
            $errors[] = $this->translator->translate('txt-source-organisation-has-pending-updates');
        }

        return $errors;
    }

    public function merge(Organisation $source, Organisation $target): array
    {
        $response = ['success' => true, 'errorMessage' => ''];

        // Update organisation properties
        if ($target->getType() === null) {
            $target->setType($source->getType());
        }
        if ($source->getDateCreated() < $target->getDateCreated()) {
            $target->setDateCreated($source->getDateCreated());
        }
        if ($source->getDateUpdated() > $target->getDateUpdated()) {
            $target->setDateUpdated($source->getDateUpdated());
        }
        if (($target->getDescription() === null) && ($source->getDescription() instanceof Description)) {
            $source->getDescription()->setOrganisation($target);
            $target->setDescription($source->getDescription());
        }
        if (($source->getFinancial() instanceof Financial) && (
            ($target->getFinancial() === null)
                || empty($target->getFinancial()->getVat())
                || ($source->getFinancial()->getVat() === $target->getFinancial()->getVat())
        )
        ) {
            $source->getFinancial()->setOrganisation($target);
            $target->setFinancial($source->getFinancial());
        }
        if ($target->getLogo()->isEmpty() && ! $source->getLogo()->isEmpty()) {
            /** @var Logo $logo */
            $logo = $source->getLogo()->first();
            $logo->setOrganisation($target);
            $target->getLogo()->add($logo);
        }

        // Transfer log
        foreach ($source->getLog() as $key => $log) {
            $log->setOrganisation($target);
            $target->getLog()->add($log);
            $source->getLog()->remove($key);
        }

        // Transfer websites
        foreach ($source->getWeb() as $key => $website) {
            $website->setOrganisation($target);
            $target->getWeb()->add($website);
            $source->getWeb()->remove($key);
        }

        // Transfer notes
        foreach ($source->getNote() as $key => $note) {
            $note->setOrganisation($target);
            $target->getNote()->add($note);
            $source->getNote()->remove($key);
        }

        // Transfer names
        foreach ($source->getNames() as $key => $name) {
            $name->setOrganisation($target);
            $target->getNames()->add($name);
            $source->getNames()->remove($key);
        }

        // Transfer affiliations
        foreach ($source->getAffiliation() as $key => $affiliation) {
            $affiliation->setOrganisation($target);
            $target->getAffiliation()->add($affiliation);
            $source->getAffiliation()->remove($key);
        }

        // Transfer affiliation financial data
        foreach ($source->getAffiliationFinancial() as $key => $affiliationFinancial) {
            $affiliationFinancial->setOrganisation($target);
            $target->getAffiliationFinancial()->add($affiliationFinancial);
            $source->getAffiliationFinancial()->remove($key);
        }

        // Transfer parent (one-to-one)
        if (($target->getParent() === null) && ($source->getParent() instanceof OParent)) {
            $parent = $source->getParent();
            $parent->setOrganisation($target);
            $target->setParent($parent);
        }

        // Transfer parent financial (one-to-many)
        foreach ($source->getParentFinancial() as $key => $parentFinancial) {
            $parentFinancial->setOrganisation($target);
            $target->getParentFinancial()->add($parentFinancial);
            $source->getParentFinancial()->remove($key);
        }

        // Transfer parent organisation (one-to-one)
        if (($target->getParentOrganisation() === null)
            && ($source->getParentOrganisation() instanceof \Organisation\Entity\Parent\Organisation)) {
            $parentOrganisation = $source->getParentOrganisation();
            $parentOrganisation->setOrganisation($target);
            $target->setParentOrganisation($parentOrganisation);
        }


        // Transfer contacts
        foreach ($source->getContactOrganisation() as $key => $contactOrganisation) {
            $contactOrganisation->setOrganisation($target);
            $target->getContactOrganisation()->add($contactOrganisation);
            $source->getContactOrganisation()->remove($key);
        }

        // Transfer booths
        foreach ($source->getOrganisationBooth() as $key => $organisationBooth) {
            $organisationBooth->setOrganisation($target);
            $target->getOrganisationBooth()->add($organisationBooth);
            $source->getOrganisationBooth()->remove($key);
        }

        // Transfer booth financial data
        foreach ($source->getBoothFinancial() as $key => $boothFinancial) {
            $boothFinancial->setOrganisation($target);
            $target->getBoothFinancial()->add($boothFinancial);
            $source->getBoothFinancial()->remove($key);
        }

        // Transfer idea partners
        foreach ($source->getIdeaPartner() as $key => $ideaPartner) {
            $ideaPartner->setOrganisation($target);
            $target->getIdeaPartner()->add($ideaPartner);
            $source->getIdeaPartner()->remove($key);
        }

        // Transfer invoices
        foreach ($source->getInvoice() as $key => $invoice) {
            $invoice->setOrganisation($target);
            $target->getInvoice()->add($invoice);
            $source->getInvoice()->remove($key);
        }

        // Transfer invoice journal
        foreach ($source->getJournal() as $key => $journal) {
            $journal->setOrganisation($target);
            $target->getJournal()->add($journal);
            $source->getJournal()->remove($key);
        }

        // Transfer program doa
        foreach ($source->getProgramDoa() as $key => $programDoa) {
            $programDoa->setOrganisation($target);
            $target->getProgramDoa()->add($programDoa);
            $source->getProgramDoa()->remove($key);
        }

        // Transfer program call doa
        foreach ($source->getDoa() as $key => $callDoa) {
            $callDoa->setOrganisation($target);
            $target->getDoa()->add($callDoa);
            $source->getDoa()->remove($key);
        }

        // Transfer reminders
        foreach ($source->getReminder() as $key => $reminder) {
            $reminder->setOrganisation($target);
            $target->getReminder()->add($reminder);
            $source->getReminder()->remove($key);
        }

        // Transfer results (many-to-many)
        foreach ($source->getResult() as $key => $result) {
            $result->getOrganisation()->removeElement($source);
            $result->getOrganisation()->add($target);
            $target->getResult()->add($result);
            $source->getResult()->remove($key);
        }

        // Transfer past updates
        foreach ($source->getUpdates() as $key => $update) {
            $update->setOrganisation($target);
            $target->getUpdates()->add($update);
            $source->getUpdates()->remove($key);
        }

        try {
            // Keep target organisation, remove the other + flush and update permissions
            $sourceId = $source->getId();
            $this->entityManager->remove($source);
            $this->entityManager->flush();

            // Prepare for logging
            $message = \sprintf(
                'Merged organisation %s (%d) into %s (%d)',
                $source->getOrganisation(),
                $sourceId,
                $target->getOrganisation(),
                $target->getId()
            );
            /** @var OrganisationAbstractController $controller */
            $controller = $this->getController();
            $contact = $controller->identity();

            // Log the merge in the target organisation
            $organisationLog = new Log();
            $organisationLog->setOrganisation($target);
            $organisationLog->setContact($contact);
            $organisationLog->setLog($message);
            $this->entityManager->persist($organisationLog);

            // Add a note to the target organisation about the merge
            $organisationNote = new Note();
            $organisationNote->setOrganisation($target);
            $organisationNote->setSource('Organisation merge');
            $organisationNote->setContact($contact);
            $organisationNote->setNote($message);
            $notes = $target->getNote()->toArray();
            \array_unshift($notes, $organisationNote);
            $target->setNote(new ArrayCollection($notes));
            $this->entityManager->persist($organisationNote);

            $this->entityManager->flush();
        } catch (\Exception $exception) {
            $response = ['success' => false, 'errorMessage' => $exception->getMessage()];
            if ($this->errorLogger instanceof Logging) {
                $this->errorLogger->handleErrorException($exception, new Request());
            }
        }

        return $response;
    }
}
