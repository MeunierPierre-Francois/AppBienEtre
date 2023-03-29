<?php

namespace App\Repository;

use App\Entity\Proposer;
use App\Model\SearchData;
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

    public function findPrestataireByCategoriesId($categorieId)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT p
        FROM App\Entity\Prestataire p
        JOIN App\Entity\Proposer pr
        WITH p.id = pr.prestataire
        WHERE pr.categorie_service = :categorieId'
        )->setParameter('categorieId', $categorieId);

        return $query->getResult();
    }

    /**  
     *
     *requete pour la  recherche
     *
     * @param SearchData $searchData
     * @return PaginationInterface
     * 
     */
    public function findBySearch(SearchData $searchData): array
    {
        $data = $this->createQueryBuilder('pr');
        if (!empty($searchData->prestataire)) {
            $data = $data
                ->join('pr.prestataire', 'p')
                ->andWhere('p.nom LIKE :prestataire')
                ->setParameter('prestataire', "%{$searchData->prestataire}%");
        }

        if (!empty($searchData->categorie_service)) {
            $data = $data
                ->join('pr.categorie_service', 'c')
                ->andWhere('c.nom IN (:categorie_service)')
                ->setParameter('categorie_service', $searchData->categorie_service);
        }


        $data = $data
            ->getQuery()
            ->getResult();

        $proposers = $data;
        return $proposers;
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
