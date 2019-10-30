<?php

namespace App\Fixtures;

use App\Entity\Asset;
use App\Entity\Club;
use App\Entity\Coach;
use App\Entity\Player;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    /**
     * @param ObjectManager $manager
     *
     * @throws \Exception
     */
    public function load(ObjectManager $manager)
    {
        //Create clubs
        for ($i = 0; $i < 10; $i++) {
            $club = new Club();

            $budget = rand(100000, 1000000);
            $club->setBudget($budget);
            $clubName = sprintf('Club-%s', $i);
            $club->setName($clubName);

            $manager->persist($club);

            $asset = new Asset();
            $asset->setPath('/LaLigadeFutbol.jpg');
            $manager->persist($asset);
            $club->setShield($asset);

            $professionalPlayers = rand(0, 5);
            for ($ip = 0; $ip < $professionalPlayers; $ip++) {
                $player = new Player();
                $player->setName(sprintf('%s-Player-Prof-%s', $clubName, $ip));
                $player->setEmail('aythami.sanchez@gmail.es');

                $randomDate = rand(315584535, 946736535);
                $date = new \DateTime();
                $date->setTimestamp($randomDate);
                $player->setBirthday($date);

                $positions = Player::getPositions();
                $player->setPosition($positions[array_rand($positions)]);

                $player->setType(Player::TYPE_PROFESSIONAL);

                $salary = rand(0, $budget);
                $player->setSalary($salary);
                $budget -= $salary;

                $player->setClub($club);
                $club->addPlayer($player);
                $manager->persist($player);
            }

            $juniorPlayers = rand(0, 7);
            for ($ij = 0; $ij < $juniorPlayers; $ij++) {
                $player = new Player();
                $player->setName(sprintf('%s-Player-Junior-%s', $clubName, $ij));
                $player->setEmail('aythami.sanchez@gmail.es');

                $randomDate = rand(915200535, 1262355735);
                $date = new \DateTime();
                $date->setTimestamp($randomDate);
                $player->setBirthday($date);

                $positions = Player::getPositions();
                $player->setPosition($positions[array_rand($positions)]);

                $player->setType(Player::TYPE_JUNIOR);
                $player->setClub($club);

                (bool)random_int(0, 1) ? $salary = rand(0, $budget) : $salary = 0;
                $player->setSalary($salary);
                $budget -= $salary;

                $player->setClub($club);
                $club->addPlayer($player);
                $manager->persist($player);
            }

            $coach = new Coach();
            $coach->setName(sprintf('%s-Player-Junior', $clubName));
            $coach->setSalary($budget);
            $coach->setClub($club);
            $coach->setEmail('aythami.sanchez@acilia.es');

            $club->setCoach($coach);
            $manager->persist($coach);

            $manager->flush();
        }
    }
}