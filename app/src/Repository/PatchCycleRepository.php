<?php

namespace App\Repository;

use App\Entity\PatchCycle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PatchCycle>
 *
 * @method PatchCycle|null find($id, $lockMode = null, $lockVersion = null)
 * @method PatchCycle|null findOneBy(array $criteria, array $orderBy = null)
 * @method PatchCycle[]    findAll()
 * @method PatchCycle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PatchCycleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PatchCycle::class);
    }

    public function save(PatchCycle $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PatchCycle $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
