<?php

namespace App\Repository;

use App\Entity\Club;
use App\Entity\Player;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bridge\Doctrine\RegistryInterface;
use function Symfony\Component\DependencyInjection\Loader\Configurator\expr;

/**
 * @method Player|null find($id, $lockMode = null, $lockVersion = null)
 * @method Player|null findOneBy(array $criteria, array $orderBy = null)
 * @method Player[]    findAll()
 * @method Player[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlayerRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Player::class);
    }
    // /**
    //  * @return Player[] Returns an array of Player objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
    /*
    public function findOneBySomeField($value): ?Player
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * @param Club $club
     *
     * @return mixed
     *
     * @throws NonUniqueResultException
     */
    public function getTotalSalaries(Club $club)
    {
            return $this->createQueryBuilder('p')
                ->select('SUM(p.salary)')
                ->andWhere('p.club = :club')
                ->setParameter('club', $club)
                ->getQuery()
                ->getSingleScalarResult();
    }

    /**
     * @param Club $club
     * @param string $type
     *
     * @return mixed
     *
     * @throws NonUniqueResultException
     */
    public function getTotalPlayers(Club $club, string $type)
    {
        $qb = $this->createQueryBuilder('p');
        return $qb
            ->select('COUNT(p.id)')
            ->andWhere(
                $qb->expr()->andX(
                    'p.club = :club',
                    'p.type = :type'
                )
            )
            ->setParameter('club', $club)
            ->setParameter('type', $type)
            ->getQuery()
            ->getSingleScalarResult();
    }
}