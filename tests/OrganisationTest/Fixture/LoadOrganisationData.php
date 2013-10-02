<?php
namespace OrganisationTest\Fixture;


use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Organisation\Entity\Organisation;

class LoadOrganisationData extends AbstractFixture implements DependentFixtureInterface
{
    /**
     * Load the Project
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $organisation = new Organisation();
        $organisation->setOrganisation('This is the organisation');

        $country = $manager->find("General\Entity\Country", 1);
        $type    = $manager->find("Organisation\Entity\Type", 1);

        $organisation->setCountry($country);
        $organisation->setType($type);

        $manager->persist($organisation);
        $manager->flush();
    }

    /**
     * fixture classes fixture is dependent on
     *
     * @return array
     */
    public function getDependencies()
    {
        return array(
            'GeneralTest\Fixture\LoadCountryData',
            'OrganisationTest\Fixture\LoadOrganisationTypeData'
        );
    }
}
