<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\CategorieDeServices;
use App\Entity\Prestataire;
use App\Entity\Proposer;



class CategorieController extends AbstractController

{


  #[Route('/categories', name: 'app_categorie_liste')]
  public function index(EntityManagerInterface $entityManager): Response
  {

    $repository = $entityManager->getRepository(CategorieDeServices::class);
    $categories = $repository->findBy(['valide' => 1]);

    return $this->render('categorie/liste.html.twig', [
      'categories' => $categories
    ]);
  }

  #[Route('/categories/show/{id}', name: 'app_categorie_detail')]
  public function show(EntityManagerInterface $entityManager, int $id): Response
  {
    // $prestataire = $entityManager->getRepository(Prestataire::class)->find($id);
    $categorie = $entityManager->getRepository(CategorieDeServices::class)
      ->createQueryBuilder('c')
      ->where('c.id = :id')
      ->setParameter('id', $id)
      ->getQuery()
      ->getOneOrNullResult();

    $prestataires = $entityManager->getRepository(Proposer::class)->findPrestataireByCategoriesId($id);

    return $this->render('categorie/detail.html.twig', [
      'prestataires' => $prestataires,
      'categorie' => $categorie,

    ]);
  }
}
