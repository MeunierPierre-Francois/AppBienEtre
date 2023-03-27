<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\CategorieDeServices;
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


    #[Route('/categories/{id}', name: 'app_categorie_detail')]
    public function show(int $id, EntityManagerInterface $entityManager): Response
    {


        $categorie = $entityManager->getRepository(CategorieDeServices::class)->find($id);
        $prestataires = $entityManager->getRepository(Proposer::class)->findPrestatairesByCategorie($id);


        return $this->render('categorie/show.html.twig', [
            'categorie' => $categorie,
            'prestataires' => $prestataires,
        ]);
    }
}
