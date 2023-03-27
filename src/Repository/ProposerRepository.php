<?php

namespace App\Repository;

use App\Entity\Proposer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Proposer>
 *
 * @method Proposer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Proposer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Proposer[]    findAll()
 * @method Proposer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProposerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Proposer::class);
    }

    public function save(Proposer $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Proposer $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findCategoriesByPrestataireId($prestataireId)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT c
        FROM App\Entity\CategorieDeServices c
        JOIN App\Entity\Proposer p
        WITH c.id = p.categorie_service
        WHERE p.prestataire = :prestataireId
        AND c.valide = 1'
        )->setParameter('prestataireId', $prestataireId);

        return $query->getResult();
    }

    public function findPrestatairesByCategorie($categorieServiceId)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT pr
            FROM App\Entity\Proposer pr
            JOIN pr.categorie_service c
            JOIN pr.prestataire p
            WHERE c.id = :categorieServiceId'
        )->setParameter('categorieServiceId', $categorieServiceId);

        return $query->getResult();
    }


    //    /**
    //     * @return Proposer[] Returns an array of Proposer objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Proposer
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
