<?php

namespace App\Controller;

use App\Entity\Proposer;
use App\Entity\CategorieDeServices;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PrestataireRepository;
use Knp\Component\Pager\PaginatorInterface;
use App\Form\PrestataireSearchType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;




class CategorieController extends AbstractController

{
  #[Route('/categories', name: 'app_categorie_liste')]
  public function index(EntityManagerInterface $entityManager, Request $request, PaginatorInterface $paginator,  PrestataireRepository $prestataireRepository): Response
  {
    $form = $this->createForm(PrestataireSearchType::class);
    $form->handleRequest($request);

    // Si le formulaire est soumis, exécuter la recherche et afficher les résultats
    if ($form->isSubmitted() && $form->isValid()) {
      $data = $form->getData();

      $nom = $data['nom'];
      $categories = $data['categories']->toArray();
      $localite = $data['localite'] ? $data['localite']->getId() : null;
      $codePostal = $data['codePostal'] ? $data['codePostal']->getId() : null;
      $commune = $data['commune'] ? $data['commune']->getId() : null;

      $prestataires = $prestataireRepository->findByFilters($nom, $categories, $localite, $codePostal, $commune);
      $pagination = $paginator->paginate(
        $prestataires,
        $request->query->getInt('page', 1), // Numéro de page à afficher
        10
      ); // Nombre de résultats par page

      // Afficher les résultats de la recherche
      return $this->render('prestataire/recherche.html.twig', [
        'prestataires' => $prestataires,
        'form' => $form->createView(),
        'pagination' => $pagination
      ]);
    }

    $repository = $entityManager->getRepository(CategorieDeServices::class);
    $categories = $repository->findBy(['valide' => 1]);

    return $this->render('categorie/liste.html.twig', [
      'categories' => $categories,
      'form' => $form->createView()
    ]);
  }

  #[Route('/categories/show/{id}', name: 'app_categorie_detail')]
  public function show(EntityManagerInterface $entityManager, int $id): Response
  {

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
