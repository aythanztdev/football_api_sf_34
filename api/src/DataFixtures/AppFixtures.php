<?php

namespace App\DataFixtures;

use App\Entity\Asset;
use App\Entity\Club;
use App\Entity\Coach;
use App\Entity\Player;
use DateTime;
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
        $data = $this->getData();
        foreach ($data as $clubElement) {
            $asset = new Asset();
            $asset->setPath($clubElement['shield']);

            $manager->persist($asset);

            $club = new Club();
            $club->setName($clubElement['name']);
            $club->setShield($asset);
            $club->setBudget($clubElement['budget']);

            $manager->persist($club);

            $coach = new Coach();
            $coach->setClub($club);
            $coach->setName($clubElement['coach']['name']);
            $coach->setEmail($clubElement['coach']['email']);
            $coach->setSalary($clubElement['coach']['salary']);
            $manager->persist($coach);

            foreach ($clubElement['players'] as $playerElement) {
                $player = new Player();
                $player->setClub($club);
                $player->setName($playerElement['name']);
                $player->setEmail($playerElement['email']);
                $player->setSalary($playerElement['salary']);
                $player->setType($playerElement['type']);
                $player->setPosition($playerElement['position']);

                $birthday = DateTime::createFromFormat( 'Y-m-d', $playerElement['birthday']);
                $player->setBirthday($birthday);

                $manager->persist($player);
            }

            $manager->flush();
        }
    }

    private function getData()
    {
        $data = [
                    [
                        'name' => 'Real Madrid Club de Fútbol',
                        'budget' => 15000000,
                        'shield' => '/LaLigadeFutbol.jpg',
                        'players' => [
                            [
                                'name' => 'Eden Hazard',
                                'email' => 'testcllfp@gmail.com',
                                'birthday' => '1991-1-7',
                                'salary' => 1500000,
                                'type' => Player::TYPE_PROFESSIONAL,
                                'position' => Player::POSITION_FORWARD

                            ],
                            [
                                'name' => 'James Rodríguez',
                                'email' => 'testcllfp@gmail.com',
                                'birthday' => '1991-6-12',
                                'salary' => 2000000,
                                'type' => Player::TYPE_PROFESSIONAL,
                                'position' => Player::POSITION_MIDFIELD

                            ],
                            [
                                'name' => 'Gareth Bale',
                                'email' => 'testcllfp@gmail.com',
                                'birthday' => '1989-7-16',
                                'salary' => 3000000,
                                'type' => Player::TYPE_PROFESSIONAL,
                                'position' => Player::POSITION_MIDFIELD

                            ],
                            [
                                'name' => 'Karim Benzema',
                                'email' => 'testcllfp@gmail.com',
                                'birthday' => '1987-12-19',
                                'salary' => 2000000,
                                'type' => Player::TYPE_PROFESSIONAL,
                                'position' => Player::POSITION_FORWARD

                            ],
                            [
                                'name' => 'Tony Fuidias',
                                'email' => 'testcllfp@gmail.com',
                                'birthday' => '2001-4-15',
                                'salary' => 15000,
                                'type' => Player::TYPE_JUNIOR,
                                'position' => Player::POSITION_GOALKEEPER

                            ],
                            [
                                'name' => 'Santos',
                                'email' => 'testcllfp@gmail.com',
                                'birthday' => '2001-1-3',
                                'salary' => 0,
                                'type' => Player::TYPE_JUNIOR,
                                'position' => Player::POSITION_DEFENDER

                            ],
                        ],
                        'coach' => [
                            'name' => 'Zinedine Zidane',
                            'email' => 'testcllfp@gmail.com',
                            'salary' => 1000000

                        ]
                    ],
                    [
                        'name' => 'Fútbol Club Barcelona',
                        'budget' => 20000000,
                        'shield' => '/LaLigadeFutbol.jpg',
                        'players' => [
                            [
                                'name' => 'Lionel Messi',
                                'email' => 'testcllfp@gmail.com',
                                'birthday' => '1987-1-24',
                                'salary' => 5000000,
                                'type' => Player::TYPE_PROFESSIONAL,
                                'position' => Player::POSITION_FORWARD

                            ],
                            [
                                'name' => 'Ivan Rakitić',
                                'email' => 'testcllfp@gmail.com',
                                'birthday' => '1988-3-10',
                                'salary' => 2000000,
                                'type' => Player::TYPE_PROFESSIONAL,
                                'position' => Player::POSITION_MIDFIELD

                            ],
                            [
                                'name' => 'Gerard Piqué',
                                'email' => 'testcllfp@gmail.com',
                                'birthday' => '1988-2-2',
                                'salary' => 4000000,
                                'type' => Player::TYPE_PROFESSIONAL,
                                'position' => Player::POSITION_MIDFIELD

                            ],
                            [
                                'name' => 'Sergi Puig',
                                'email' => 'testcllfp@gmail.com',
                                'birthday' => '1998-11-19',
                                'salary' => 200000,
                                'type' => Player::TYPE_JUNIOR,
                                'position' => Player::POSITION_GOALKEEPER

                            ],
                            [
                                'name' => 'Van Beijnen',
                                'email' => 'testcllfp@gmail.com',
                                'birthday' => '1999-1-7',
                                'salary' => 150000,
                                'type' => Player::TYPE_JUNIOR,
                                'position' => Player::POSITION_DEFENDER

                            ],
                        ],
                        'coach' => [
                            'name' => 'Ernesto Valverde',
                            'email' => 'testcllfp@gmail.com',
                            'salary' => 1000000
                        ]
                    ],
                    [
                        'name' => 'Club Atlético de Madrid',
                        'budget' => 8000000,
                        'shield' => '/LaLigadeFutbol.jpg',
                        'players' => [
                            [
                                'name' => 'Marcos Llorente',
                                'email' => 'testcllfp@gmail.com',
                                'birthday' => '1995-1-30',
                                'salary' => 900000,
                                'type' => Player::TYPE_PROFESSIONAL,
                                'position' => Player::POSITION_MIDFIELD

                            ],
                            [
                                'name' => 'Jan Oblak',
                                'email' => 'testcllfp@gmail.com',
                                'birthday' => '1993-1-7',
                                'salary' => 1000000,
                                'type' => Player::TYPE_PROFESSIONAL,
                                'position' => Player::POSITION_GOALKEEPER

                            ],
                            [
                                'name' => 'Santiago Arias',
                                'email' => 'testcllfp@gmail.com',
                                'birthday' => '1992-1-13',
                                'salary' => 800000,
                                'type' => Player::TYPE_PROFESSIONAL,
                                'position' => Player::POSITION_DEFENDER

                            ],
                            [
                                'name' => 'Vitolo Machín',
                                'email' => 'testcllfp@gmail.com',
                                'birthday' => '1989-2-11',
                                'salary' => 1500000,
                                'type' => Player::TYPE_PROFESSIONAL,
                                'position' => Player::POSITION_FORWARD

                            ],
                            [
                                'name' => 'Manuel Sánchez',
                                'email' => 'testcllfp@gmail.com',
                                'birthday' => '2000-8-20',
                                'salary' => 1000000,
                                'type' => Player::TYPE_PROFESSIONAL,
                                'position' => Player::POSITION_DEFENDER

                            ],
                        ],
                        'coach' => [
                            'name' => 'Diego Simeone',
                            'email' => 'testcllfp@gmail.com',
                            'salary' => 2000000
                        ]
                    ]
                ];

        return $data;
    }
}