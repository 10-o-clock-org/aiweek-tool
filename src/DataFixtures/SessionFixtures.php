<?php

namespace App\DataFixtures;

use App\Entity\Channel;
use App\Entity\Location;
use App\Entity\Session;
use App\Entity\SessionDetail;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SessionFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        /** @var User $reporter1 */
        $reporter1 = $this->getReference(UserFixture::REPORTER1_USER_REF, User::class);

        $manager->persist($this->createSessionNichtFreigegeben($reporter1));
        $manager->persist($this->createSessionFreigegeben($reporter1, 'Freigegebene Session o. Ä.'));
        $manager->persist($this->createSessionFreigegebenUndGeaendert($reporter1));
        $manager->persist($this->createSessionCancelled($reporter1));

        /** @var User $reporter2 */
        $reporter2 = $this->getReference(UserFixture::REPORTER2_USER_REF, User::class);

        for ($i = 1; $i <= 40; $i ++) {
            $manager->persist($this->createSessionFreigegeben($reporter2, 'Freigegebene Session ' . $i));
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [UserFixture::class, ChannelFixture::class];
    }

    private function createSessionNichtFreigegeben(User $reporter): Session
    {
        $session = (new Session())
            ->setCancelled(false)
            ->setOrganization($reporter->getOrganizations()->first());

        $detail = $session
            ->getDraftDetails()
            ->setTitle('Nicht freigegeben')
            ->setShortDescription('Kurzbeschreibung nicht freigegebener Session')
            ->setLongDescription('Die nicht freigegebene Session hat auch eine Langbeschreibung')
            ->setOnlineOnly(false)
            ->setLocation(
                (new Location())
                    ->setName('Nicht-Freigegeben-Office')
                    ->setStreetNo('Nicht-Freigegeben-Straße 17a')
                    ->setZipcode('97072')
                    ->setCity('Würzburg')
                    ->setIsAccessible(false)
            )
            ->setLink('http://wueww.de/session/nicht/freigegeben');

        $this->randomizeStartDateTime($detail);
        $this->randomizeChannel($detail);

        $session->propose();

        return $session;
    }

    private function createSessionFreigegeben(User $reporter, string $title): Session
    {
        $location = new Location();

        $onlineOnly = mt_rand(0, 100) < 20;
        if (!$onlineOnly) {
            $location
                ->setName('Freigegeben-Office')
                ->setStreetNo('Freigegeben-Straße 17a')
                ->setZipcode('97072')
                ->setCity('Würzburg')
                ->setIsAccessible(false);
        }

        $detail = (new SessionDetail())
            ->setTitle($title)
            ->setShortDescription('Kurzbeschreibung einer freigegebenen Session')
            ->setLongDescription('Die freigegebene Session hat natürlich auch eine Langbeschreibung')
            ->setOnlineOnly($onlineOnly)
            ->setLocation($location)
            ->setLink('http://wueww.de/session/freigegeben');

        $session = (new Session())
            ->setCancelled(false)
            ->setOrganization($reporter->getOrganizations()->first())
            ->setDraftDetails($detail);

        $this->randomizeStartDateTime($detail);
        $this->randomizeChannel($detail);

        $session->propose();
        $session->accept();

        return $session;
    }

    private function createSessionFreigegebenUndGeaendert(User $reporter): Session
    {
        $detailAccepted = (new SessionDetail())
            ->setTitle('Freigegebene Session')
            ->setShortDescription('Kurzbeschreibung einer freigegebenen Session')
            ->setLongDescription('Die freigegebene Session hat natürlich auch eine Langbeschreibung')
            ->setOnlineOnly(false)
            ->setLocation(
                (new Location())
                    ->setName('Freigegeben-Office')
                    ->setStreetNo('Freigegeben-Straße 17a')
                    ->setZipcode('97072')
                    ->setCity('Würzburg')
                    ->setIsAccessible(false)
            )
            ->setLink('http://wueww.de/session/freigegeben');

        $detailNewDraft = (new SessionDetail())
            ->setTitle('Freigegebene Session geändert')
            ->setShortDescription('geänderte Kurzbeschreibung einer freigegebenen Session')
            ->setLongDescription('natürlich darf sich auch die Langbeschreibung ändern')
            ->setOnlineOnly(false)
            ->setLocation(
                (new Location())
                    ->setName('Freigegeben-Office')
                    ->setStreetNo('Freigegeben-Straße 17a')
                    ->setZipcode('97072')
                    ->setCity('Würzburg')
                    ->setIsAccessible(false)
            )
            ->setLink('http://wueww.de/session/freigegeben');

        $session = (new Session())
            ->setCancelled(false)
            ->setOrganization($reporter->getOrganizations()->first())
            ->setDraftDetails($detailAccepted);

        $this->randomizeStartDateTime($detailAccepted);
        $this->randomizeChannel($detailAccepted);

        $session->propose();
        $session->accept();

        $this->randomizeStartDateTime($detailNewDraft);
        $this->randomizeChannel($detailNewDraft);

        $session->setDraftDetails($detailNewDraft);
        $session->propose();

        return $session;
    }

    private function createSessionCancelled(User $reporter): Session
    {
        $detail = (new SessionDetail())
            ->setTitle('abgesagte Session')
            ->setShortDescription('Kurzbeschreibung einer abgesagten Session')
            ->setLongDescription('Die abgesagte Session hat natürlich auch eine Langbeschreibung')
            ->setOnlineOnly(false)
            ->setLocation(
                (new Location())
                    ->setName('Freigegeben-Office')
                    ->setStreetNo('Freigegeben-Straße 17a')
                    ->setZipcode('97072')
                    ->setCity('Würzburg')
                    ->setIsAccessible(false)
            )
            ->setLink('http://wueww.de/session/freigegeben');

        $session = (new Session())
            ->setOrganization($reporter->getOrganizations()->first())
            ->setDraftDetails($detail);

        $this->randomizeStartDateTime($detail);
        $this->randomizeChannel($detail);

        $session->propose();
        $session->accept();
        $session->setCancelled(true);

        return $session;
    }

    private function randomizeStartDateTime(SessionDetail $detail)
    {
        $detail->setStart1($this->randomizeDateTime());
        $detail->setStart2($this->randomizeDateTime());

        if (mt_rand(0, 100) < 50) {
            $detail->setStart3($this->randomizeDateTime());
        } else {
            $detail->setStart3(null);
        }

        $durationDistribution = [30, 45, 60, 60, 60, 90, 120];
        $detail->setDuration($durationDistribution[array_rand($durationDistribution)]);
    }

    private function randomizeDateTime(): \DateTimeImmutable
    {
        // Base date range between 2025-06-30 and 2025-07-03
        $startDate = new \DateTime('2025-06-30');
        $endDate = new \DateTime('2025-07-03');

        // Random day within range
        $dayDiff = $endDate->diff($startDate)->days;
        $randomDays = random_int(0, $dayDiff);
        $date = clone $startDate;
        $date->modify("+$randomDays days");

        // Time constraints (9:30 - 20:00)
        // Evening hours more likely (15:00-20:00 has higher probability)
        $hourDistribution = [];
        // Morning/afternoon (9-15)
        for ($h = 9; $h < 15; $h++) {
            $hourDistribution[] = $h;
        }
        // Evening (15-20) - add multiple times to increase probability
        for ($h = 15; $h < 20; $h++) {
            $hourDistribution[] = $h;
            $hourDistribution[] = $h; // add twice to make evening more likely
        }

        // Select random hour from weighted distribution
        $hour = $hourDistribution[array_rand($hourDistribution)];

        // First hour needs to be at least 9:30
        if ($hour === 9) {
            $minutes = 30;
        } else {
            // Only 00, 15, 30, 45 with 00 and 30 being three times more likely
            $minutesDistribution = [0, 0, 0, 15, 30, 30, 30, 45];
            $minutes = $minutesDistribution[array_rand($minutesDistribution)];
        }

        $date->setTime($hour, $minutes);

        return \DateTimeImmutable::createFromMutable($date);
    }

    private function randomizeChannel(SessionDetail $detail)
    {
        $channelName = ChannelFixture::CHANNEL_NAMES[array_rand(ChannelFixture::CHANNEL_NAMES)];
        $channel = $this->getReference($channelName, Channel::class);

        $detail->setChannel($channel);
    }


}
