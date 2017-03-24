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

namespace OrganisationTest\Controller\Plugin;

use Affiliation\Entity\Affiliation;
use Affiliation\Entity\Financial as AffiliationFinancial;
use Contact\Entity\Contact;
use Contact\Entity\ContactOrganisation;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Event\Entity\Booth\Financial as BoothFinancial;
use General\Entity\Country;
use Invoice\Entity\Invoice;
use Invoice\Entity\Journal;
use Invoice\Entity\Reminder;
use Organisation\Controller\OrganisationAdminController;
use Organisation\Controller\Plugin\MergeOrganisation;
use Organisation\Entity\Booth;
use Organisation\Entity\Cluster;
use Organisation\Entity\Description;
use Organisation\Entity\Financial;
use Organisation\Entity\IctOrganisation;
use Organisation\Entity\Log;
use Organisation\Entity\Logo;
use Organisation\Entity\Name;
use Organisation\Entity\Note;
use Organisation\Entity\OParent;
use Organisation\Entity\Organisation;
use Organisation\Entity\Type;
use Organisation\Entity\Web;
use Program\Entity\Doa as ProgramDoa;
use Program\Entity\Call\Doa as CallDoa;
use Program\Entity\Technology;
use Project\Entity\Idea\Partner;
use Project\Entity\Result\Result;
use Testing\Util\AbstractServiceTest;
use Zend\I18n\Translator\Translator;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Zend\Log\Logger;
use ZfcUser\Controller\Plugin\ZfcUserAuthentication;

/**
 * Class MergeOrganisationTest
 *
 * @package OrganisationTest\Controller\Plugin
 */
final class MergeOrganisationTest extends AbstractServiceTest
{
    /** @var Organisation */
    private $source;

    /** @var Organisation */
    private $target;

    /** @var Translator */
    private $translator;

    /**
     * Set up basic properties
     */
    public function setUp()
    {
        $this->source     = $this->createSource();
        $this->target     = $this->createTarget();
        $this->translator = $this->setUpTranslatorMock();
    }

    /**
     * Test the basic __invoke magic method returning the plugin instance
     *
     * @covers \Organisation\Controller\Plugin\MergeOrganisation::__invoke
     * @covers \Organisation\Controller\Plugin\MergeOrganisation::__construct
     */
    public function testInvoke()
    {
        $mergeOrganisation = new MergeOrganisation($this->getEntityManagerMock(), $this->translator);
        $instance = $mergeOrganisation();
        $this->assertSame($mergeOrganisation, $instance);
    }

    /**
     * Test the pre-merge checks
     *
     * @covers \Organisation\Controller\Plugin\MergeOrganisation::checkMerge
     * @covers \Organisation\Controller\Plugin\MergeOrganisation::translate
     */
    public function testCheckMerge()
    {
        $mergeOrganisation = new MergeOrganisation($this->getEntityManagerMock(), $this->translator);

        // Set up some merge-preventing circumstances
        $this->source->getFinancial()->setVat('NL123');
        $this->target->getFinancial()->setVat('NL456');
        $this->source->setParent(new OParent());
        $this->target->setParent(new OParent());
        $otherCountry = new Country();
        $otherCountry->setId(2);
        $this->source->setCountry($otherCountry);

        // Run the merge check
        $errors = $mergeOrganisation()->checkMerge($this->source, $this->target);

        $this->assertEquals(true, in_array('txt-cannot-merge-VAT-NL456-and-NL123', $errors));
        $this->assertEquals(true, in_array('txt-organisations-cant-both-be-parents', $errors));
        $this->assertEquals(true, in_array('txt-organisations-cant-have-different-countries', $errors));
    }

    /**
     * Test the actual merge
     *
     * @covers \Organisation\Controller\Plugin\MergeOrganisation::merge
     * @covers \Organisation\Controller\Plugin\MergeOrganisation::persist
     */
    public function testMerge()
    {
        $mergeOrganisation = new MergeOrganisation($this->setUpEntityManagerMock(), $this->translator);
        $mergeOrganisation->setController($this->createControllerMock());

        $result = $mergeOrganisation()->merge($this->source, $this->target);

        // Basic properties
        $this->assertSame(true, $result['success']);
        $this->assertSame('', $result['errorMessage']);
        $this->assertSame(1, $this->target->getType()->getId());
        $this->assertEquals(new \DateTime('2017-01-01'), $this->target->getDateCreated());
        $this->assertEquals(new \DateTime('2017-01-03'), $this->target->getDateUpdated());
        $this->assertSame(1, $this->target->getDescription()->getId());
        $this->assertSame(1, $this->target->getFinancial()->getId());
        $this->assertSame(1, $this->target->getLogo()->first()->getId());

        // Collections
        /** @var Log $log */
        $log = $this->target->getLog()->first();
        $this->assertInstanceOf(Log::class, $log);
        $this->assertSame(1, $log->getId());

        /** @var Technology $technology */
        $technology = $this->target->getTechnology()->first();
        $this->assertInstanceOf(Technology::class, $technology);
        $this->assertSame(1, $technology->getId());

        /** @var Web $web */
        $web = $this->target->getWeb()->first();
        $this->assertInstanceOf(Web::class, $web);
        $this->assertSame(1, $web->getId());

        /** @var Note $note */
        $note = $this->target->getNote()->first();
        $this->assertInstanceOf(Note::class, $note);
        $this->assertSame(1, $note->getId());

        /** @var Name $name */
        $name = $this->target->getNames()->first();
        $this->assertInstanceOf(Name::class, $name);
        $this->assertSame(1, $name->getId());

        /** @var Affiliation $affiliation */
        $affiliation = $this->target->getAffiliation()->first();
        $this->assertInstanceOf(Affiliation::class, $affiliation);
        $this->assertSame(1, $affiliation->getId());

        /** @var AffiliationFinancial $affiliationFinancial */
        $affiliationFinancial = $this->target->getAffiliationFinancial()->first();
        $this->assertInstanceOf(AffiliationFinancial::class, $affiliationFinancial);
        $this->assertSame(1, $affiliationFinancial->getId());

        /** @var IctOrganisation $ictOrganisation */
        $ictOrganisation = $this->target->getIctOrganisation()->first();
        $this->assertInstanceOf(IctOrganisation::class, $ictOrganisation);
        $this->assertSame(1, $ictOrganisation->getId());

        /** @var Cluster $cluster */
        $cluster = $this->target->getCluster()->first();
        $this->assertInstanceOf(Cluster::class, $cluster);
        $this->assertSame(1, $cluster->getId());

        /** @var Cluster $clusterMember */
        $clusterMember = $this->target->getClusterMember()->first();
        $this->assertInstanceOf(Cluster::class, $clusterMember);
        $this->assertSame(1, $clusterMember->getId());

        /** @var ContactOrganisation $contactOrganisation */
        $contactOrganisation = $this->target->getContactOrganisation()->first();
        $this->assertInstanceOf(ContactOrganisation::class, $contactOrganisation);
        $this->assertSame(1, $contactOrganisation->getId());

        /** @var Booth $booth */
        $booth = $this->target->getOrganisationBooth()->first();
        $this->assertInstanceOf(Booth::class, $booth);
        $this->assertSame(1, $booth->getId());

        /** @var BoothFinancial $boothFinancial */
        $boothFinancial = $this->target->getBoothFinancial()->first();
        $this->assertInstanceOf(BoothFinancial::class, $boothFinancial);
        $this->assertSame(1, $boothFinancial->getId());

        /** @var Partner $ideaPartner */
        $ideaPartner = $this->target->getIdeaPartner()->first();
        $this->assertInstanceOf(Partner::class, $ideaPartner);
        $this->assertSame(1, $ideaPartner->getId());

        /** @var Invoice $invoice */
        $invoice = $this->target->getInvoice()->first();
        $this->assertInstanceOf(Invoice::class, $invoice);
        $this->assertSame(1, $invoice->getId());

        /** @var Journal $journal */
        $journal = $this->target->getJournal()->first();
        $this->assertInstanceOf(Journal::class, $journal);
        $this->assertSame(1, $journal->getId());

        /** @var ProgramDoa $programDoa */
        $programDoa = $this->target->getProgramDoa()->first();
        $this->assertInstanceOf(ProgramDoa::class, $programDoa);
        $this->assertSame(1, $programDoa->getId());

        /** @var CallDoa $callDoa */
        $callDoa = $this->target->getDoa()->first();
        $this->assertInstanceOf(CallDoa::class, $callDoa);
        $this->assertSame(1, $callDoa->getId());

        /** @var Reminder $reminder */
        $reminder = $this->target->getReminder()->first();
        $this->assertInstanceOf(Reminder::class, $reminder);
        $this->assertSame(1, $reminder->getId());

        /** @var Result $result */
        $result = $this->target->getResult()->first();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertSame(1, $result->getId());
    }

    /**
     * Test a failing merge
     *
     * @covers \Organisation\Controller\Plugin\MergeOrganisation::merge
     */
    public function testMergeFail()
    {
        $mergeOrganisation = new MergeOrganisation($this->setUpEntityManagerMock(true), $this->translator);
        $logger = $this->createLoggerMock('err');

        $response = $mergeOrganisation()->merge($this->source, $this->target, $logger);

        $this->assertEquals(false, $response['success']);
        $this->assertEquals('Oops!', $response['errorMessage']);
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

        $logo = new Logo();
        $logo->setId(1);

        $country = new Country();
        $country->setId(1);

        $type = new Type();
        $type->setId(1);

        $financial = new Financial();
        $financial->setId(1);
        $financial->setOrganisation($source);

        $log = new Log();
        $log->setId(1);
        $log->setOrganisation($source);

        $technology = new Technology();
        $technology->setId(1);
        $technology->setOrganisation(new ArrayCollection([$source]));

        $web = new Web();
        $web->setId(1);
        $web->setOrganisation($source);

        $note = new Note();
        $note->setId(1);
        $note->setOrganisation($source);

        $name = new Name();
        $name->setId(1);
        $name->setOrganisation($source);

        $affiliation = new Affiliation();
        $affiliation->setId(1);
        $affiliation->setOrganisation($source);

        $affiliationFinancial = new AffiliationFinancial();
        $affiliationFinancial->setId(1);
        $affiliationFinancial->setOrganisation($source);

        $ictOrganisation = new IctOrganisation();
        $ictOrganisation->setId(1);
        $ictOrganisation->setOrganisation($source);

        $cluster = new Cluster();
        $cluster->setId(1);
        $cluster->setOrganisation($source);
        $cluster->setMember(new ArrayCollection([$source]));

        $contactOrganisation = new ContactOrganisation();
        $contactOrganisation->setId(1);
        $contactOrganisation->setOrganisation($source);

        $booth = new Booth();
        $booth->setId(1);
        $booth->setOrganisation($source);

        $boothFinancial = new BoothFinancial();
        $boothFinancial->setId(1);
        $boothFinancial->setOrganisation($source);

        $ideaPartner = new Partner();
        $ideaPartner->setId(1);
        $ideaPartner->setOrganisation($source);

        $invoice = new Invoice();
        $invoice->setId(1);
        $invoice->setOrganisation($source);

        $journal = new Journal();
        $journal->setId(1);
        $journal->setOrganisation($source);

        $programDoa = new ProgramDoa();
        $programDoa->setId(1);
        $programDoa->setOrganisation($source);

        $callDoa = new CallDoa();
        $callDoa->setId(1);
        $callDoa->setOrganisation($source);

        $reminder = new Reminder();
        $reminder->setId(1);
        $reminder->setOrganisation($source);

        $result = new Result();
        $result->setId(1);
        $result->setOrganisation(new ArrayCollection([$source]));

        $source->setOrganisation('Organisation 1');
        $source->setDateCreated(new \DateTime('2017-01-01'));
        $source->setDateUpdated(new \DateTime('2017-01-03'));
        $source->setCountry($country);
        $source->setType($type);
        $source->setFinancial($financial);
        $source->setDescription($description);
        $source->setLogo(new ArrayCollection([$logo]));
        $source->setLog(new ArrayCollection([$log]));
        $source->setTechnology(new ArrayCollection([$technology]));
        $source->setWeb(new ArrayCollection([$web]));
        $source->setNote(new ArrayCollection([$note]));
        $source->setNames(new ArrayCollection([$name]));
        $source->setAffiliation(new ArrayCollection([$affiliation]));
        $source->setAffiliationFinancial(new ArrayCollection([$affiliationFinancial]));
        $source->setIctOrganisation(new ArrayCollection([$ictOrganisation]));
        $source->setCluster(new ArrayCollection([$cluster]));
        $source->setClusterMember(new ArrayCollection([$cluster]));
        $source->setContactOrganisation(new ArrayCollection([$contactOrganisation]));
        $source->setOrganisationBooth(new ArrayCollection([$booth]));
        $source->setBoothFinancial(new ArrayCollection([$boothFinancial]));
        $source->setIdeaPartner(new ArrayCollection([$ideaPartner]));
        $source->setInvoice(new ArrayCollection([$invoice]));
        $source->setJournal(new ArrayCollection([$journal]));
        $source->setProgramDoa(new ArrayCollection([$programDoa]));
        $source->setDoa(new ArrayCollection([$callDoa]));
        $source->setReminder(new ArrayCollection([$reminder]));
        $source->setResult(new ArrayCollection([$result]));

        return $source;
    }

    /**
     * @return Organisation
     */
    private function createTarget(): Organisation
    {
        $country = new Country();
        $country->setId(1);

        $financial = new Financial();
        $financial->setId(2);

        $target = new Organisation();
        $target->setId(2);
        $target->setOrganisation('Organisation 2');
        $target->setDateCreated(new \DateTime('2017-01-02'));
        $target->setDateUpdated(new \DateTime('2017-01-02'));
        $target->setCountry($country);
        $target->setFinancial($financial);

        return $target;
    }

    /**
     * Get a mocked logger instance
     *
     * @param string $method
     * @return MockObject
     */
    private function createLoggerMock(string $method): MockObject
    {
        /** @var MockObject|Logger $mergeOrganisationMock */
        $loggerMock = $this->getMockBuilder(Logger::class)
            ->setMethods([$method])
            ->getMock();
        $loggerMock->expects($this->once())
            ->method($method)
            ->with($this->isType('string'));

        return $loggerMock;
    }

    /**
     * Create a controller mock object to get dummy authentication info.
     *
     * @return OrganisationAdminController|MockObject
     */
    private function createControllerMock(): MockObject
    {
        $contact = new Contact();
        $contact->setId(1);

        $zfcUserAuthenticationMock = $this->getMockBuilder(ZfcUserAuthentication::class)
            ->setMethods(['getIdentity'])
            ->getMock();
        $zfcUserAuthenticationMock->expects($this->once())
            ->method('getIdentity')
            ->will($this->returnValue($contact));

        $controllerMock = $this->getMockBuilder(OrganisationAdminController::class)
            ->setMethods(['zfcUserAuthentication'])
            ->getMock();
        $controllerMock->expects($this->once())
            ->method('zfcUserAuthentication')
            ->will($this->returnValue($zfcUserAuthenticationMock));

        return $controllerMock;
    }

    /**
     * Set up the translator mock object.
     *
     * @return Translator|MockObject
     */
    private function setUpTranslatorMock(): MockObject
    {
        $translatorMock = $this->getMockBuilder(Translator::class)
            ->setMethods(['translate'])
            ->getMock();

        // Just let the translator return the untranslated string
        $translatorMock->expects($this->any())
            ->method('translate')
            ->will($this->returnArgument(0));

        return $translatorMock;
    }

    /**
     * Set up the entity manager mock object with expectations depending on the chosen merge strategy.
     * @param bool $throwException
     *
     * @return EntityManager|MockObject
     */
    private function setUpEntityManagerMock($throwException = false): MockObject
    {
        $entityManagerMock = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['persist', 'remove', 'flush'])
            ->getMock();

        // Short circuit when an exception should be thrown
        if ($throwException) {
            $exception = new ORMException('Oops!');
            $entityManagerMock->expects($this->any())->method('persist')->will($this->throwException($exception));
            $entityManagerMock->expects($this->any())->method('remove')->will($this->throwException($exception));
            $entityManagerMock->expects($this->any())->method('flush')->will($this->throwException($exception));

            return $entityManagerMock;
        }

        // Setup the parameters depending on merge strategy
        $params = [
            [$this->identicalTo($this->source->getLog()->first())],
            [$this->identicalTo($this->source->getTechnology()->first())],
            [$this->identicalTo($this->source->getWeb()->first())],
            [$this->identicalTo($this->source->getNote()->first())],
            [$this->identicalTo($this->source->getNames()->first())],
            [$this->identicalTo($this->source->getAffiliation()->first())],
            [$this->identicalTo($this->source->getAffiliationFinancial()->first())],
            [$this->identicalTo($this->source->getIctOrganisation()->first())],
            [$this->identicalTo($this->source->getCluster()->first())],
            [$this->identicalTo($this->source->getClusterMember()->first())],
            [$this->identicalTo($this->source->getContactOrganisation()->first())],
            [$this->identicalTo($this->source->getOrganisationBooth()->first())],
            [$this->identicalTo($this->source->getBoothFinancial()->first())],
            [$this->identicalTo($this->source->getIdeaPartner()->first())],
            [$this->identicalTo($this->source->getInvoice()->first())],
            [$this->identicalTo($this->source->getJournal()->first())],
            [$this->identicalTo($this->source->getProgramDoa()->first())],
            [$this->identicalTo($this->source->getDoa()->first())],
            [$this->identicalTo($this->source->getReminder()->first())],
            [$this->identicalTo($this->source->getResult()->first())],
            [$this->identicalTo($this->target)],
            [$this->isInstanceOf(Log::class)]
        ];

        $entityManagerMock->expects($this->exactly(count($params)))->method('persist')->withConsecutive(...$params);
        $entityManagerMock->expects($this->once())->method('remove')->with($this->source);
        $entityManagerMock->expects($this->exactly(2))->method('flush');

        return $entityManagerMock;
    }
}