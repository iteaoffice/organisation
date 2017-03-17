<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

namespace OrganisationTest\Controller\Plugin;

use Affiliation\Entity\Affiliation;
use Contact\Entity\ContactOrganisation;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use General\Entity\Country;
use Organisation\Controller\Plugin\MergeOrganisation;
use Organisation\Entity\Booth;
use Organisation\Entity\Financial;
use Organisation\Entity\IctOrganisation;
use Organisation\Entity\Log;
use Organisation\Entity\Name;
use Organisation\Entity\Note;
use Organisation\Entity\OParent;
use Organisation\Entity\Organisation;
use Organisation\Entity\Type;
use Organisation\Entity\Web;
use Program\Entity\Technology;
use Testing\Util\AbstractServiceTest;
use Zend\I18n\Translator\Translator;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

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
        $mergeOrganisation = new MergeOrganisation($this->setUpEntityManagerMock(), $this->translator);

        $result = $mergeOrganisation()->merge($this->source, $this->target);

        $this->assertEquals(true, $result['success']);
        $this->assertEquals('', $result['errorMessage']);
        $this->assertEquals($this->source->getDateCreated(), $this->target->getDateCreated());
        $this->assertEquals($this->source->getDateUpdated(), $this->target->getDateUpdated());

        // MORE ASSERTIONS HERE
    }

    /**
     * Test a failing merge
     *
     * @covers \Organisation\Controller\Plugin\MergeOrganisation::merge
     */
    public function testMergeFail()
    {
        $entityManagerMock = $this->setUpEntityManagerMock(true);

        /** @var MockObject|MergeOrganisation $mergeOrganisationMock */
        $mergeOrganisationMock = $this->getMockBuilder(MergeOrganisation::class)
            ->setConstructorArgs([$entityManagerMock, $this->translator])
            ->setMethods(['logException'])
            ->getMock();
        $mergeOrganisationMock->expects($this->once())
            ->method('logException')
            ->with($this->isInstanceOf(ORMException::class));

        $response = $mergeOrganisationMock()->merge($this->source, $this->target);

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

        $country = new Country();
        $country->setId(1);

        $type = new Type();
        $type->setId(1);

        $financial = new Financial();
        $financial->setId(1);

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

        $ictOrganisation = new IctOrganisation();
        $ictOrganisation->setId(1);
        $ictOrganisation->setOrganisation($source);

        $contactOrganisation = new ContactOrganisation();
        $contactOrganisation->setId(1);
        $contactOrganisation->setOrganisation($source);

        $booth = new Booth();
        $booth->setId(1);
        $booth->setOrganisation($source);

        $source->setOrganisation('Organisation 1');
        $source->setDateCreated(new \DateTime('2017-01-01'));
        $source->setDateUpdated(new \DateTime('2017-01-03'));
        $source->setCountry($country);
        $source->setType($type);
        $source->setFinancial($financial);
        $source->setLog(new ArrayCollection([$log]));
        $source->setTechnology(new ArrayCollection([$technology]));
        $source->setWeb(new ArrayCollection([$web]));
        $source->setNote(new ArrayCollection([$note]));
        $source->setNames(new ArrayCollection([$name]));
        $source->setAffiliation(new ArrayCollection([$affiliation]));
        $source->setIctOrganisation(new ArrayCollection([$ictOrganisation]));
        $source->setContactOrganisation(new ArrayCollection([$contactOrganisation]));
        $source->setOrganisationBooth(new ArrayCollection([$booth]));

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
            [$this->identicalTo($this->source->getIctOrganisation()->first())],
            [$this->identicalTo($this->source->getContactOrganisation()->first())],
            [$this->identicalTo($this->source->getOrganisationBooth()->first())],
            [$this->identicalTo($this->target)]
        ];

        $entityManagerMock->expects($this->exactly(count($params)))->method('persist')->withConsecutive(...$params);
        $entityManagerMock->expects($this->once())->method('remove')->with($this->source);
        $entityManagerMock->expects($this->once())->method('flush');

        return $entityManagerMock;
    }
}