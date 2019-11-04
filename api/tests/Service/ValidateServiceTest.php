<?php

namespace App\Tests\Service;

use App\Entity\Club;
use App\Repository\ClubRepository;
use App\Repository\PlayerRepository;
use App\Service\ValidateService;
use Doctrine\ORM\NonUniqueResultException as NonUniqueResultExceptionAlias;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

class ValidateServiceTest extends TestCase
{
    /**
     * @throws NonUniqueResultExceptionAlias
     */
    public function testValidateSalariesOk()
    {
        $validateService = new ValidateService($this->getMockedPlayerRepository(800000), $this->getMockedClubRepository());

        $club = $this->createClub();

        $result = $validateService->validateSalaries($club, 100000);
        $this->assertEmpty($result);
    }

    /**
     * @throws NonUniqueResultExceptionAlias
     */
    public function testValidateSalariesFail()
    {
        $validateService = new ValidateService($this->getMockedPlayerRepository(800000), $this->getMockedClubRepository());

        $club = $this->createClub();

        $result = $validateService->validateSalaries($club, 300000);
        $this->assertNotEmpty($result);
    }

    private function getMockedPlayerRepository(float $salary)
    {
        $repositoryMock = $this->getMockBuilder(PlayerRepository::class)->disableOriginalConstructor()->getMock();
        $repositoryMock->expects($this->once())->method('getTotalSalaries')->willReturn($salary);
        return $repositoryMock;
    }

    private function getMockedClubRepository()
    {
        $repositoryMock = $this->getMockBuilder(ClubRepository::class)->disableOriginalConstructor()->getMock();
        return $repositoryMock;
    }

    private function createClub()
    {
        $club = new Club();
        $club->setName('Real Madrid Club de Fútbol');
        $club->setBudget(1000000);

        return $club;
    }
}