<?php

namespace App\Controller;

use App\Entity\Proposer;
use App\Model\SearchData;
use App\Entity\Prestataire;
use App\Entity\CategorieDeServices;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ProposerRepository;
use App\Form\SearchFormType;


class CategorieController extends AbstractController

{


  #[Route('/categories', name: 'app_categorie_liste')]
  public function index(EntityManagerInterface $entityManager, Request $request, ProposerRepository $proposerRepository): Response
  {

    $repository = $entityManager->getRepository(CategorieDeServices::class);
    $categories = $repository->findBy(['valide' => 1]);



    return $this->render('categorie/liste.html.twig', [

      'categories' => $categories,


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
