<?php

namespace App\DataFixtures;

use App\Entity\Asset;
use App\Entity\Club;
use App\Entity\Coach;
use App\Entity\Player;
use App\Exception\DataBadFormmatedException;
use App\Exception\ServiceNotAvailableException;
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

                $birthday = DateTime::createFromFormat('d-m-Y', $playerElement['birthday']);
                if (empty($birthday)) {
                    throw new DataBadFormmatedException('Birthday cannot be null');
                }

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
                                'birthday' => '7-1-1991',
                                'salary' => 1500000,
                                'type' => Player::TYPE_PROFESSIONAL,
                                'position' => Player::POSITION_FORWARD

                            ],
                            [
                                'name' => 'James Rodríguez',
                                'email' => 'testcllfp@gmail.com',
                                'birthday' => '12-6-1991',
                                'salary' => 2000000,
                                'type' => Player::TYPE_PROFESSIONAL,
                                'position' => Player::POSITION_MIDFIELD

                            ],
                            [
                                'name' => 'Gareth Bale',
                                'email' => 'testcllfp@gmail.com',
                                'birthday' => '16-7-1989',
                                'salary' => 3000000,
                                'type' => Player::TYPE_PROFESSIONAL,
                                'position' => Player::POSITION_MIDFIELD

                            ],
                            [
                                'name' => 'Karim Benzema',
                                'email' => 'testcllfp@gmail.com',
                                'birthday' => '19-12-1987',
                                'salary' => 2000000,
                                'type' => Player::TYPE_PROFESSIONAL,
                                'position' => Player::POSITION_FORWARD

                            ],
                            [
                                'name' => 'Tony Fuidias',
                                'email' => 'testcllfp@gmail.com',
                                'birthday' => '15-4-2001',
                                'salary' => 0,
                                'type' => Player::TYPE_JUNIOR,
                                'position' => Player::POSITION_GOALKEEPER

                            ],
                            [
                                'name' => 'Santos',
                                'email' => 'testcllfp@gmail.com',
                                'birthday' => '3-1-2001',
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
                                'birthday' => '24-1-1987',
                                'salary' => 5000000,
                                'type' => Player::TYPE_PROFESSIONAL,
                                'position' => Player::POSITION_FORWARD

                            ],
                            [
                                'name' => 'Ivan Rakitić',
                                'email' => 'testcllfp@gmail.com',
                                'birthday' => '10-3-1988',
                                'salary' => 2000000,
                                'type' => Player::TYPE_PROFESSIONAL,
                                'position' => Player::POSITION_MIDFIELD

                            ],
                            [
                                'name' => 'Gerard Piqué',
                                'email' => 'testcllfp@gmail.com',
                                'birthday' => '2-2-1988',
                                'salary' => 4000000,
                                'type' => Player::TYPE_PROFESSIONAL,
                                'position' => Player::POSITION_MIDFIELD

                            ],
                            [
                                'name' => 'Sergi Puig',
                                'email' => 'testcllfp@gmail.com',
                                'birthday' => '19-11-1998',
                                'salary' => 0,
                                'type' => Player::TYPE_JUNIOR,
                                'position' => Player::POSITION_GOALKEEPER

                            ],
                            [
                                'name' => 'Van Beijnen',
                                'email' => 'testcllfp@gmail.com',
                                'birthday' => '7-1-1999',
                                'salary' => 0,
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
                                'birthday' => '30-1-1995',
                                'salary' => 900000,
                                'type' => Player::TYPE_PROFESSIONAL,
                                'position' => Player::POSITION_MIDFIELD

                            ],
                            [
                                'name' => 'Jan Oblak',
                                'email' => 'testcllfp@gmail.com',
                                'birthday' => '7-1-1993',
                                'salary' => 1000000,
                                'type' => Player::TYPE_PROFESSIONAL,
                                'position' => Player::POSITION_GOALKEEPER

                            ],
                            [
                                'name' => 'Santiago Arias',
                                'email' => 'testcllfp@gmail.com',
                                'birthday' => '13-1-1992',
                                'salary' => 800000,
                                'type' => Player::TYPE_PROFESSIONAL,
                                'position' => Player::POSITION_DEFENDER

                            ],
                            [
                                'name' => 'Vitolo Machín',
                                'email' => 'testcllfp@gmail.com',
                                'birthday' => '11-2-1989',
                                'salary' => 1500000,
                                'type' => Player::TYPE_PROFESSIONAL,
                                'position' => Player::POSITION_FORWARD

                            ],
                            [
                                'name' => 'Manuel Sánchez',
                                'email' => 'testcllfp@gmail.com',
                                'birthday' => '20-8-2000',
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
