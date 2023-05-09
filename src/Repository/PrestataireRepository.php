<?php

namespace App\Repository;

use App\Entity\Prestataire;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;




/**
 * @extends ServiceEntityRepository<Prestataire>
 *
 * @method Prestataire|null find($id, $lockMode = null, $lockVersion = null)
 * @method Prestataire|null findOneBy(array $criteria, array $orderBy = null)
 * @method Prestataire[]    findAll()
 * @method Prestataire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrestataireRepository extends ServiceEntityRepository
{



    public function __construct(ManagerRegistry $registry,)
    {
        parent::__construct($registry, Prestataire::class);
    }

    public function save(Prestataire $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Prestataire $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    public function findByFilters($nom, $categories, $localite, $codePostal, $commune)
    {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.proposers', 'proposer')
            ->leftJoin('proposer.categorie_service', 'categorie')
            ->leftJoin('p.utilisateur', 'utilisateur')
            ->leftJoin('utilisateur.localite', 'localite')
            ->leftJoin('utilisateur.code_postal', 'code_postal')
            ->leftJoin('utilisateur.commune', 'commune');


        if ($nom) {
            $qb->andWhere('p.nom LIKE :nom')
                ->setParameter('nom', '%' . $nom . '%');
        }

        if ($categories) {
            $qb->andWhere('categorie.id IN (:categories)')
                ->setParameter('categories', $categories);
        }

        if ($localite) {
            $qb->andWhere('localite.id = :localite')
                ->setParameter('localite', $localite);
        }

        if ($codePostal) {
            $qb->andWhere('code_postal.id = :codePostal')
                ->setParameter('codePostal', $codePostal);
        }

        if ($commune) {
            $qb->andWhere('commune.id = :commune')
                ->setParameter('commune', $commune);
        }

        $prestataires = $qb->getQuery()->getResult();
        if (empty($prestataires)) {
            // Si aucun prestataire ne correspond aux critères, on réexécute la requête sans les critères de recherche
            $qb = $this->createQueryBuilder('p')
                ->leftJoin('p.proposers', 'proposer')
                ->leftJoin('proposer.categorie_service', 'categorie')
                ->leftJoin('p.utilisateur', 'utilisateur')
                ->leftJoin('utilisateur.localite', 'localite')
                ->leftJoin('utilisateur.code_postal', 'code_postal')
                ->leftJoin('utilisateur.commune', 'commune');
            $prestataires = $qb->getQuery()->getResult();
        }

        return $prestataires;
    }




    //    /**
    //     * @return Prestataire[] Returns an array of Prestataire objects
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

    //    public function findOneBySomeField($value): ?Prestataire
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
