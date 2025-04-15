<?php

namespace App\DataFixtures;

use App\Entity\Channel;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ChannelFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $digiGes = new Channel();
        $digiGes->setName("Digitale Gesellschaft");
        $digiGes->setSort(10);
        $manager->persist($digiGes);

        $science = new Channel();
        $science->setName("Science & Technology");
        $science->setSort(20);
        $manager->persist($science);

        $business = new Channel();
        $business->setName("Business");
        $business->setSort(30);
        $manager->persist($business);

        $sustainability = new Channel();
        $sustainability->setName("Nachhaltigkeit");
        $sustainability->setSort(40);
        $manager->persist($sustainability);

        $manager->flush();

    }
}
