<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace OrganisationTest\Controller\Plugin;

use Affiliation\Entity\Affiliation;
use Contact\Entity\Contact;
use Contact\Entity\ContactOrganisation;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use ErrorHeroModule\Handler\Logging;
use General\Entity\Country;
use Invoice\Entity\Invoice;
use Invoice\Entity\Journal;
use Invoice\Entity\Reminder;
use Laminas\I18n\Translator\Translator;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Stdlib\DispatchableInterface;
use Organisation\Controller\Organisation\ManagerController;
use Organisation\Controller\Plugin\Merge\OrganisationMerge;
use Organisation\Entity\Booth;
use Organisation\Entity\Description;
use Organisation\Entity\Financial;
use Organisation\Entity\Log;
use Organisation\Entity\Logo;
use Organisation\Entity\Name;
use Organisation\Entity\Note;
use Organisation\Entity\Organisation;
use Organisation\Entity\ParentEntity;
use Organisation\Entity\Type;
use Organisation\Entity\Update;
use Organisation\Entity\Web;
use Organisation\Service\UpdateService;
use PHPUnit\Framework\MockObject\MockObject;
use Program\Entity\Doa;
use Project\Entity\Idea\Partner;
use Project\Entity\Result\Result;
use Testing\Util\AbstractServiceTest;

use function in_array;

/**
 * Class OrganisationMergeTest
 *
 * @package OrganisationTest\Controller\Plugin
 */
final class MergeOrganisationTest extends AbstractServiceTest
{
    private Organisation $source;
    private Organisation $target;
    private TranslatorInterface $translator;
    private Logging $errorLog;

    /**
     * Set up basic properties
     */
    public function setUp(): void
    {
        $this->source     = $this->createSource();
        $this->target     = $this->createTarget();
        $this->translator = $this->setUpTranslatorMock();
        $this->errorLog   = $this->setUpErrorLoggingMock();
    }

    /**
     * @return Organisation
     */
    private function createSource(): Organisation
    {
        $source = new Organisation();
        $source->setId(1);
        $description = new Description();
        $description->setId(1);
        $description->setOrganisation($source);
        $country = new Country();
        $country->setId(1);
        $type = new Type();
        $type->setId(1);
        $financial = new Financial();
        $financial->setId(1);
        $logo = new Logo();
        $logo->setId(1);
        $logo->setOrganisation($source);
        $log = new Log();
        $log->setId(1);
        $web = new Web();
        $web->setId(1);
        $note = new Note();
        $note->setId(1);
        $name = new Name();
        $name->setId(1);
        $name->setOrganisation($source);
        $affiliation = new Affiliation();
        $affiliation->setId(1);
        $affiliation->setOrganisation($source);
        $affiliationFinancial = new \Affiliation\Entity\Financial();
        $affiliationFinancial->setId(1);
        $affiliationFinancial->setOrganisation($source);
        $parent = new ParentEntity();
        $parent->setId(1);
        $parent->setOrganisation($source);
        $parentFinancial = new \Organisation\Entity\Parent\Financial();
        $parentFinancial->setId(1);
        $parentFinancial->setOrganisation($source);
        $parentOrganisation = new \Organisation\Entity\Parent\Organisation();
        $parentOrganisation->setId(1);
        $parentOrganisation->setOrganisation($source);
        $contactOrganisation = new ContactOrganisation();
        $contactOrganisation->setId(1);
        $contactOrganisation->setOrganisation($source);
        $booth = new Booth();
        $booth->setId(1);
        $booth->setOrganisation($source);
        $boothFinancial = new \Event\Entity\Booth\Financial();
        $boothFinancial->setId(1);
        $boothFinancial->setOrganisation($source);
        $ideaPartner = new Partner();
        $ideaPartner->setId(1);
        $ideaPartner->setOrganisation($source);
        $invoice = new Invoice();
        $invoice->setId(1);
        $invoice->setOrganisation($source);
        $invoiceJournal = new Journal();
        $invoiceJournal->setId(1);
        $invoiceJournal->setOrganisation($source);
        $programDoa = new Doa();
        $programDoa->setId(1);
        $programDoa->setOrganisation($source);
        $doa = new \Program\Entity\Call\Doa();
        $doa->setId(1);
        $doa->setOrganisation($source);
        $reminder = new Reminder();
        $reminder->setId(1);
        $reminder->setOrganisation($source);
        $result = new Result();
        $result->setId(1);
        $result->setOrganisation(new ArrayCollection([$source]));
        $update = new Update();
        $update->setId(1);
        $update->setOrganisation($source);
        $update->setDateApproved(new DateTime());
        $source->setOrganisation('Organisation 1');
        $source->setDateCreated(new DateTime('2017-01-01'));
        $source->setDateUpdated(new DateTime('2017-01-03'));
        $source->setDescription($description);
        $source->setCountry($country);
        $source->setType($type);
        $source->setFinancial($financial);
        $source->setLogo(new ArrayCollection([$logo]));
        $source->setLog(new ArrayCollection([$log]));
        $source->setWeb(new ArrayCollection([$web]));
        $source->setNote(new ArrayCollection([$note]));
        $source->setNames(new ArrayCollection([$name]));
        $source->setAffiliation(new ArrayCollection([$affiliation]));
        $source->setAffiliationFinancial(new ArrayCollection([$affiliationFinancial]));
        $source->setParent($parent);
        $source->setParentFinancial(new ArrayCollection([$parentFinancial]));
        $source->setParentOrganisation($parentOrganisation);
        $source->setContactOrganisation(new ArrayCollection([$contactOrganisation]));
        $source->setOrganisationBooth(new ArrayCollection([$booth]));
        $source->setBoothFinancial(new ArrayCollection([$boothFinancial]));
        $source->setIdeaPartner(new ArrayCollection([$ideaPartner]));
        $source->setInvoice(new ArrayCollection([$invoice]));
        $source->setJournal(new ArrayCollection([$invoiceJournal]));
        $source->setProgramDoa(new ArrayCollection([$programDoa]));
        $source->setDoa(new ArrayCollection([$doa]));
        $source->setReminder(new ArrayCollection([$reminder]));
        $source->setResult(new ArrayCollection([$result]));
        $source->setUpdates(new ArrayCollection([$update]));
        return $source;
    }

    private function createTarget(): Organisation
    {
        $country = new Country();
        $country->setId(1);
        $financial = new Financial();
        $financial->setId(2);
        $target = new Organisation();
        $target->setId(2);
        $target->setOrganisation('Organisation 2');
        $target->setDateCreated(new DateTime('2017-01-02'));
        $target->setDateUpdated(new DateTime('2017-01-02'));
        $target->setCountry($country);
        $target->setFinancial($financial);
        return $target;
    }

    /**
     * Set up the translator mock object.
     *
     * @return Translator|MockObject
     */
    private function setUpTranslatorMock()
    {
        $translatorMock = $this->getMockBuilder(Translator::class)
            ->onlyMethods(['translate'])
            ->getMock();
        // Just let the translator return the untranslated string
        $translatorMock
            ->method('translate')
            ->will(self::returnArgument(0));
        return $translatorMock;
    }

    /**
     * Set up the error logger mock object.
     *
     * @return Logging|MockObject
     */
    private function setUpErrorLoggingMock()
    {
        /** @var Logging|MockObject $errorLoggerMock */
        $errorLoggerMock = $this->getMockBuilder(Logging::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['handleErrorException'])
            ->getMock();
        $errorLoggerMock
            ->method('handleErrorException')
            ->with(self::isInstanceOf('Exception'));
        return $errorLoggerMock;
    }

    /**
     * Test the basic __invoke magic method returning the plugin instance
     *
     * @covers \Organisation\Controller\Plugin\Merge\OrganisationMerge::__invoke
     * @covers \Organisation\Controller\Plugin\Merge\OrganisationMerge::__construct
     */
    public function testInvoke(): void
    {
        $organisationMerge = new OrganisationMerge($this->getEntityManagerMock(), $this->getUpUpdateServiceMock(), $this->translator, $this->errorLog);
        $instance          = $organisationMerge();
        self::assertSame($organisationMerge, $instance);
    }

    /**
     * @return MockObject|UpdateService
     */
    private function getUpUpdateServiceMock()
    {
        $updateServiceMock = $this->getMockBuilder(UpdateService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['hasPendingUpdates'])
            ->getMock();
        $updateServiceMock
            ->method('hasPendingUpdates')
            ->willReturn(true);
        return $updateServiceMock;
    }

    /**
     * Test the pre-merge checks
     *
     * @covers \Organisation\Controller\Plugin\Merge\OrganisationMerge::checkMerge
     */
    public function testCheckMerge(): void
    {
        $organisationMerge = new OrganisationMerge($this->getEntityManagerMock(), $this->getUpUpdateServiceMock(), $this->translator, $this->errorLog);
        // Set up some merge-preventing circumstances
        $this->source->getFinancial()->setVat('NL123');
        $this->target->getFinancial()->setVat('NL456');
        $this->source->setParent(new ParentEntity());
        $this->target->setParent(new ParentEntity());
        $otherCountry = new Country();
        $otherCountry->setId(2);
        $this->source->setCountry($otherCountry);
        $update = new Update();
        $this->source->getUpdates()->add($update);
        // Run the merge check
        $errors = $organisationMerge()->checkMerge($this->source, $this->target);
        self::assertEquals(true, in_array('txt-cannot-merge-VAT-NL456-and-NL123', $errors, true));
        self::assertEquals(true, in_array('txt-organisations-cant-both-be-parents', $errors, true));
        self::assertEquals(true, in_array('txt-organisations-cant-have-different-countries', $errors, true));
        self::assertEquals(true, in_array('txt-source-organisation-has-pending-updates', $errors, true));
    }

    /**
     * Test the actual merge
     *
     * @covers \Organisation\Controller\Plugin\Merge\OrganisationMerge::merge
     */
    public function testMerge(): void
    {
        /** @var DispatchableInterface $controllerMock */
        $controllerMock    = $this->setUpControllerMock();
        $organisationMerge = new OrganisationMerge($this->setUpEntityManagerMock(), $this->getUpUpdateServiceMock(), $this->translator, $this->errorLog);
        $organisationMerge->setController($controllerMock);
        $result = $organisationMerge()->merge($this->source, $this->target);
        self::assertEquals(true, $result['success']);
        self::assertEquals('', $result['errorMessage']);
        self::assertEquals(1, $this->target->getType()->getId());
        self::assertEquals($this->source->getDateCreated(), $this->target->getDateCreated());
        self::assertEquals($this->source->getDateUpdated(), $this->target->getDateUpdated());
        self::assertEquals(1, $this->target->getDescription()->getId());
        self::assertEquals($this->target, $this->target->getDescription()->getOrganisation());
        self::assertEquals(1, $this->target->getFinancial()->getId());
        self::assertEquals($this->target, $this->target->getFinancial()->getOrganisation());
        self::assertEquals(1, $this->target->getLogo()->first()->getId());
        self::assertEquals($this->target, $this->target->getLogo()->first()->getOrganisation());
        self::assertEquals(1, $this->target->getLog()->first()->getId());
        self::assertEquals($this->target, $this->target->getLog()->first()->getOrganisation());
        self::assertEquals(1, $this->target->getWeb()->first()->getId());
        self::assertEquals($this->target, $this->target->getWeb()->first()->getOrganisation());
        self::assertEquals('Merged organisation Organisation 1 (1) into Organisation 2 (2)', $this->target->getNote()->first()->getNote());
        self::assertEquals(1, $this->target->getNote()->last()->getId());
        self::assertEquals($this->target, $this->target->getNote()->first()->getOrganisation());
        self::assertEquals(1, $this->target->getNames()->first()->getId());
        self::assertEquals($this->target, $this->target->getNames()->first()->getOrganisation());
        self::assertEquals(1, $this->target->getAffiliation()->first()->getId());
        self::assertEquals($this->target, $this->target->getAffiliation()->first()->getOrganisation());
        self::assertEquals(1, $this->target->getAffiliationFinancial()->first()->getId());
        self::assertEquals($this->target, $this->target->getAffiliationFinancial()->first()->getOrganisation());
        self::assertEquals(1, $this->target->getParent()->getId());
        self::assertEquals($this->target, $this->target->getParent()->getOrganisation());
        self::assertEquals(1, $this->target->getParentFinancial()->first()->getId());
        self::assertEquals($this->target, $this->target->getParentFinancial()->first()->getOrganisation());
        self::assertEquals(1, $this->target->getParentOrganisation()->getId());
        self::assertEquals($this->target, $this->target->getParentOrganisation()->getOrganisation());
        self::assertEquals(1, $this->target->getContactOrganisation()->first()->getId());
        self::assertEquals($this->target, $this->target->getContactOrganisation()->first()->getOrganisation());
        self::assertEquals(1, $this->target->getOrganisationBooth()->first()->getId());
        self::assertEquals($this->target, $this->target->getOrganisationBooth()->first()->getOrganisation());
        self::assertEquals(1, $this->target->getBoothFinancial()->first()->getId());
        self::assertEquals($this->target, $this->target->getBoothFinancial()->first()->getOrganisation());
        self::assertEquals(1, $this->target->getIdeaPartner()->first()->getId());
        self::assertEquals($this->target, $this->target->getIdeaPartner()->first()->getOrganisation());
        self::assertEquals(1, $this->target->getInvoice()->first()->getId());
        self::assertEquals($this->target, $this->target->getInvoice()->first()->getOrganisation());
        self::assertEquals(1, $this->target->getJournal()->first()->getId());
        self::assertEquals($this->target, $this->target->getJournal()->first()->getOrganisation());
        self::assertEquals(1, $this->target->getProgramDoa()->first()->getId());
        self::assertEquals($this->target, $this->target->getProgramDoa()->first()->getOrganisation());
        self::assertEquals(1, $this->target->getDoa()->first()->getId());
        self::assertEquals($this->target, $this->target->getDoa()->first()->getOrganisation());
        self::assertEquals(1, $this->target->getReminder()->first()->getId());
        self::assertEquals($this->target, $this->target->getReminder()->first()->getOrganisation());
        self::assertEquals(1, $this->target->getResult()->first()->getId());
        self::assertEquals($this->target, $this->target->getResult()->first()->getOrganisation()->first());
        self::assertEquals(1, $this->target->getUpdates()->first()->getId());
        self::assertEquals($this->target, $this->target->getUpdates()->first()->getOrganisation());
    }

    /**
     * Set up the translator mock object.
     *
     * @return Translator|MockObject
     */
    private function setUpControllerMock()
    {
        $contact = new Contact();
        $contact->setId(1);
        $controllerMock = $this->getMockBuilder(ManagerController::class)
            ->disableOriginalConstructor()
            ->addMethods(['identity'])
            ->getMock();
        $controllerMock->expects(self::once())
            ->method('identity')
            ->willReturn($contact);
        return $controllerMock;
    }

    /**
     * Set up the entity manager mock object with expectations depending on the chosen merge strategy.
     * @param bool $throwException
     *
     * @return EntityManager|MockObject
     */
    private function setUpEntityManagerMock(bool $throwException = false)
    {
        $entityManagerMock = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['persist', 'remove', 'flush'])
            ->getMock();
        // Short circuit when an exception should be thrown
        if ($throwException) {
            $exception = new ORMException('Oops!');
            $entityManagerMock->method('persist')->will(self::throwException($exception));
            $entityManagerMock->method('remove')->will(self::throwException($exception));
            $entityManagerMock->method('flush')->will(self::throwException($exception));
            return $entityManagerMock;
        }

        // Setup the parameters depending on merge strategy
        $params = [
            [self::isInstanceOf(Log::class)],
            [self::isInstanceOf(Note::class)],
        ];
        $entityManagerMock->expects(self::exactly(\count($params)))->method('persist')->withConsecutive(...$params);
        $entityManagerMock->expects(self::once())->method('remove')->with($this->source);
        $entityManagerMock->expects(self::exactly(2))->method('flush');
        return $entityManagerMock;
    }

    /**
     * Test a failing merge
     *
     * @covers \Organisation\Controller\Plugin\Merge\OrganisationMerge::merge
     */
    public function testMergeFail(): void
    {
        $entityManagerMock      = $this->setUpEntityManagerMock(true);
        $organisationMergeNoLog = new OrganisationMerge($entityManagerMock, $this->getUpUpdateServiceMock(), $this->translator, $this->errorLog);
        $responseNoLog          = $organisationMergeNoLog->merge($this->source, $this->target);
        self::assertEquals(false, $responseNoLog['success']);
        self::assertEquals('Oops!', $responseNoLog['errorMessage']);

        /** @var Logging|MockObject $errorLoggerMock */
        $errorLoggerMock = $this->getMockBuilder(Logging::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['handleErrorException'])
            ->getMock();
        $errorLoggerMock->expects(self::once())
            ->method('handleErrorException')
            ->with(self::isInstanceOf('Exception'));


        $organisationMergeLog = new OrganisationMerge($entityManagerMock, $this->getUpUpdateServiceMock(), $this->translator, $errorLoggerMock);
        $responseLog          = $organisationMergeLog()->merge($this->source, $this->target);
        self::assertEquals(false, $responseLog['success']);
    }
}
