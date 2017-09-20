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
use Contact\Entity\Contact;
use Contact\Entity\ContactOrganisation;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
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
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Program\Entity\Doa;
use Program\Entity\Technology;
use Project\Entity\Idea\Partner;
use Project\Entity\Result\Result;
use Testing\Util\AbstractServiceTest;
use Zend\I18n\Translator\Translator;
use Zend\Log\Logger;
use Zend\Stdlib\DispatchableInterface;
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
        $this->source = $this->createSource();
        $this->target = $this->createTarget();
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
     */
    public function testMerge()
    {
        /** @var DispatchableInterface $controllerMock */
        $controllerMock = $this->setUpControllerMock();
        $mergeOrganisation = new MergeOrganisation($this->setUpEntityManagerMock(), $this->translator);
        $mergeOrganisation->setController($controllerMock);

        $result = $mergeOrganisation()->merge($this->source, $this->target);

        $this->assertEquals(true, $result['success']);
        $this->assertEquals('', $result['errorMessage']);
        $this->assertEquals(1, $this->target->getType()->getId());

        $this->assertEquals($this->source->getDateCreated(), $this->target->getDateCreated());
        $this->assertEquals($this->source->getDateUpdated(), $this->target->getDateUpdated());

        $this->assertEquals(1, $this->target->getDescription()->getId());
        $this->assertEquals($this->target, $this->target->getDescription()->getOrganisation());

        $this->assertEquals(1, $this->target->getFinancial()->getId());
        $this->assertEquals($this->target, $this->target->getFinancial()->getOrganisation());

        $this->assertEquals(1, $this->target->getLogo()->first()->getId());
        $this->assertEquals($this->target, $this->target->getLogo()->first()->getOrganisation());

        $this->assertEquals(1, $this->target->getLog()->first()->getId());
        $this->assertEquals($this->target, $this->target->getLog()->first()->getOrganisation());

        $this->assertEquals(1, $this->target->getTechnology()->first()->getId());
        $this->assertEquals($this->target, $this->target->getTechnology()->first()->getOrganisation()->first());

        $this->assertEquals(1, $this->target->getWeb()->first()->getId());
        $this->assertEquals($this->target, $this->target->getWeb()->first()->getOrganisation());

        $this->assertEquals(
            'Merged organisation Organisation 1 (1) into Organisation 2 (2)',
            $this->target->getNote()->first()->getNote()
        );
        $this->assertEquals(1, $this->target->getNote()->last()->getId());
        $this->assertEquals($this->target, $this->target->getNote()->first()->getOrganisation());

        $this->assertEquals(1, $this->target->getNames()->first()->getId());
        $this->assertEquals($this->target, $this->target->getNames()->first()->getOrganisation());

        $this->assertEquals(1, $this->target->getAffiliation()->first()->getId());
        $this->assertEquals($this->target, $this->target->getAffiliation()->first()->getOrganisation());

        $this->assertEquals(1, $this->target->getAffiliationFinancial()->first()->getId());
        $this->assertEquals($this->target, $this->target->getAffiliationFinancial()->first()->getOrganisation());

        $this->assertEquals(1, $this->target->getParent()->getId());
        $this->assertEquals($this->target, $this->target->getParent()->getOrganisation());

        $this->assertEquals(1, $this->target->getParentFinancial()->first()->getId());
        $this->assertEquals($this->target, $this->target->getParentFinancial()->first()->getOrganisation());

        $this->assertEquals(1, $this->target->getParentOrganisation()->getId());
        $this->assertEquals($this->target, $this->target->getParentOrganisation()->getOrganisation());

        $this->assertEquals(1, $this->target->getIctOrganisation()->first()->getId());
        $this->assertEquals($this->target, $this->target->getIctOrganisation()->first()->getOrganisation());

        $this->assertEquals(1, $this->target->getCluster()->first()->getId());
        $this->assertEquals($this->target, $this->target->getCluster()->first()->getOrganisation());

        $this->assertEquals(2, $this->target->getClusterMember()->first()->getId());
        $this->assertEquals($this->target, $this->target->getClusterMember()->first()->getOrganisation());

        $this->assertEquals(1, $this->target->getContactOrganisation()->first()->getId());
        $this->assertEquals($this->target, $this->target->getContactOrganisation()->first()->getOrganisation());

        $this->assertEquals(1, $this->target->getOrganisationBooth()->first()->getId());
        $this->assertEquals($this->target, $this->target->getOrganisationBooth()->first()->getOrganisation());

        $this->assertEquals(1, $this->target->getBoothFinancial()->first()->getId());
        $this->assertEquals($this->target, $this->target->getBoothFinancial()->first()->getOrganisation());

        $this->assertEquals(1, $this->target->getIdeaPartner()->first()->getId());
        $this->assertEquals($this->target, $this->target->getIdeaPartner()->first()->getOrganisation());

        $this->assertEquals(1, $this->target->getInvoice()->first()->getId());
        $this->assertEquals($this->target, $this->target->getInvoice()->first()->getOrganisation());

        $this->assertEquals(1, $this->target->getJournal()->first()->getId());
        $this->assertEquals($this->target, $this->target->getJournal()->first()->getOrganisation());

        $this->assertEquals(1, $this->target->getProgramDoa()->first()->getId());
        $this->assertEquals($this->target, $this->target->getProgramDoa()->first()->getOrganisation());

        $this->assertEquals(1, $this->target->getDoa()->first()->getId());
        $this->assertEquals($this->target, $this->target->getDoa()->first()->getOrganisation());

        $this->assertEquals(1, $this->target->getReminder()->first()->getId());
        $this->assertEquals($this->target, $this->target->getReminder()->first()->getOrganisation());

        $this->assertEquals(1, $this->target->getResult()->first()->getId());
        $this->assertEquals($this->target, $this->target->getResult()->first()->getOrganisation()->first());
    }

    /**
     * Test a failing merge
     *
     * @covers \Organisation\Controller\Plugin\MergeOrganisation::merge
     */
    public function testMergeFail()
    {
        $entityManagerMock = $this->setUpEntityManagerMock(true);
        $mergeOrganisation = new MergeOrganisation($entityManagerMock, $this->translator);
        $loggerMock = $this->getMockBuilder(Logger::class)
            ->setMethods(['err'])
            ->getMock();

        $loggerMock->expects($this->once())
            ->method('err')
            ->with($this->stringContains('Oops!'))
            ->will($this->returnSelf());

        $responseNoLog = $mergeOrganisation()->merge($this->source, $this->target);
        $responseLog = $mergeOrganisation()->merge($this->source, $this->target, $loggerMock);

        $this->assertEquals(false, $responseNoLog['success']);
        $this->assertEquals('Oops!', $responseNoLog['errorMessage']);
        $this->assertEquals(false, $responseLog['success']);
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

        $technology = new Technology();
        $technology->setId(1);
        $technology->setOrganisation(new ArrayCollection([$source]));

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

        $parent = new OParent();
        $parent->setId(1);
        $parent->setOrganisation($source);

        $parentFinancial = new \Organisation\Entity\Parent\Financial();
        $parentFinancial->setId(1);
        $parentFinancial->setOrganisation($source);

        $parentOrganisation = new \Organisation\Entity\Parent\Organisation();
        $parentOrganisation->setId(1);
        $parentOrganisation->setOrganisation($source);

        $ictOrganisation = new IctOrganisation();
        $ictOrganisation->setId(1);
        $ictOrganisation->setOrganisation($source);

        $cluster = new Cluster();
        $cluster->setId(1);
        $cluster->setOrganisation($source);

        $clusterMember = new Cluster();
        $clusterMember->setId(2);
        $clusterMember->setMember(new ArrayCollection([$source]));

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

        $source->setOrganisation('Organisation 1');
        $source->setDateCreated(new \DateTime('2017-01-01'));
        $source->setDateUpdated(new \DateTime('2017-01-03'));
        $source->setDescription($description);
        $source->setCountry($country);
        $source->setType($type);
        $source->setFinancial($financial);
        $source->setLogo(new ArrayCollection([$logo]));
        $source->setLog(new ArrayCollection([$log]));
        $source->setTechnology(new ArrayCollection([$technology]));
        $source->setWeb(new ArrayCollection([$web]));
        $source->setNote(new ArrayCollection([$note]));
        $source->setNames(new ArrayCollection([$name]));
        $source->setAffiliation(new ArrayCollection([$affiliation]));
        $source->setAffiliationFinancial(new ArrayCollection([$affiliationFinancial]));
        $source->setParent($parent);
        $source->setParentFinancial(new ArrayCollection([$parentFinancial]));
        $source->setParentOrganisation($parentOrganisation);
        $source->setIctOrganisation(new ArrayCollection([$ictOrganisation]));
        $source->setCluster(new ArrayCollection([$cluster]));
        $source->setClusterMember(new ArrayCollection([$clusterMember]));
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
     * Set up the translator mock object.
     *
     * @return Translator|MockObject
     */
    private function setUpControllerMock(): MockObject
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
     * Set up the entity manager mock object with expectations depending on the chosen merge strategy.
     * @param bool $throwException
     *
     * @return EntityManager|MockObject
     */
    private function setUpEntityManagerMock(bool $throwException = false): MockObject
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
            [$this->isInstanceOf(Log::class)],
            [$this->isInstanceOf(Note::class)],
        ];

        $entityManagerMock->expects($this->exactly(count($params)))->method('persist')->withConsecutive(...$params);
        $entityManagerMock->expects($this->once())->method('remove')->with($this->source);
        $entityManagerMock->expects($this->exactly(2))->method('flush');

        return $entityManagerMock;
    }
}