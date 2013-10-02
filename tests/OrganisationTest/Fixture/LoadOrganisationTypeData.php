<?php
namespace OrganisationTest\Fixture;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

use Organisation\Entity\Type;

class LoadOrganisationTypeData extends AbstractFixture
{
    /**
     * Load the Project
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $organisationType = new Type();
        $organisationType->setType('Test organisation type');
        $organisationType->setDescription('This is the description');
        $organisationType->setInvoice(Type::INVOICE);

        $manager->persist($organisationType);
        $manager->flush();
    }
}
