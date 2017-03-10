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

namespace Organisation\Controller\Plugin;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\ORMException;
use Organisation\Entity\Cluster;
use Organisation\Entity\IctOrganisation;
use Organisation\Entity\Organisation;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * Class MergeOrganisation
 *
 * @package Organisation\Controller\Plugin
 */
final class MergeOrganisation extends AbstractPlugin
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
     * @param TranslatorInterface $translator
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface    $translator
    ){
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
        if(!is_null($target->getParent()) && !is_null($source->getParent())){
            $errors[] = $this->translate('txt-organisations-cant-both-be-parents');
        }

        return $errors;
    }

    /**
     * @param Organisation $source
     * @param Organisation $target
     * @return array
     */
    public function merge(Organisation $source, Organisation $target): array
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
            if (is_null($target->getDescription())) {
                $target->setDescription($source->getDescription());
            }
            if (is_null($target->getFinancial())) {
                $target->setFinancial($source->getFinancial());
            }
            if ($target->getLogo()->isEmpty()) {
                $target->getLogo()->add($source->getLogo()->first());
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

            // Transfer ICT organisations
            /** @var EntityRepository $repository */
            $repository = $this->entityManager->getRepository(IctOrganisation::class);
            /** @var IctOrganisation $ictOrganisation */
            foreach ($repository->findBy(['organisation', $source->getId()]) as $ictOrganisation) {
                $ictOrganisation->setOrganisation($target);
                $this->persist($ictOrganisation);
            }

            // Remove cluster membership
            /** @var EntityRepository $repository */
//            $repository = $this->entityManager->getRepository(Cluster::class);
//            /** @var Cluster $cluster */
//            foreach ($repository->findBy(['organisation', $source->getId()]) as $cluster) {
//                $cluster->getOrganisation()->removeElement($source);
//                $technology->getOrganisation()->add($target);
//                $ictOrganisation->setOrganisation($target);
//                $this->persist($ictOrganisation);
//            }

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

            // Persist main organisation, remove the other + flush and update permissions
            $this->persist($target);
            $this->entityManager->remove($source);
            $this->entityManager->flush();

        } catch (ORMException $e) {
            $response = ['success' => false, 'errorMessage' => $e->getMessage()];
            error_log($e->getFile() . ':' . $e->getLine() . ' ' . $e->getMessage());
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
    private function persist(object $object): void
    {
        $this->entityManager->persist($object);
    }
}
