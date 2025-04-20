<?php

namespace App\DataFixtures;

use App\Entity\Channel;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ChannelFixture extends Fixture
{
    const CHANNEL_NAMES = [
        'Tech & Science',
        'Digitale Gesellschaft & Nachhaltigkeit',
        'Business & New Work',
        'Gründen/Start-up',
        'Kultur',
];

    public function load(ObjectManager $manager): void
    {
        $sort = 10;

        foreach (self::CHANNEL_NAMES as $channelName) {
            $channel = new Channel();
            $channel->setName($channelName);
            $channel->setSort($sort);
            $manager->persist($channel);

            $this->addReference($channelName, $channel);

            $sort += 10;
        }

        $manager->flush();
    }
}
