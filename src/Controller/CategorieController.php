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

    /*

    #[Route('/categories/{id}/prestataires', name: 'app_categories_prestataires')]
public function prestatairesByCategorie(EntityManagerInterface $entityManager, int $id): Response
{
    $categorie = $entityManager->getRepository(Categorie::class)->find($id);

    if (!$categorie) {
        throw $this->createNotFoundException('Cette catégorie n\'existe pas');
    }

    $prestataires = $entityManager->getRepository(Prestataire::class)
        ->createQueryBuilder('p')
        ->innerJoin('p.proposers', 'pr')
        ->innerJoin('pr.categorie', 'c')
        ->where('c.id = :id')
        ->setParameter('id', $id)
        ->getQuery()
        ->getResult();

    return $this->render('categorie/prestataires.html.twig', [
        'categorie' => $categorie,
        'prestataires' => $prestataires,
    ]);
}
////POur le twig////

{% extends 'base.html.twig' %}

{% block title %}Prestataires proposant la catégorie {{ categorie.nom }}{% endblock %}

{% block body %}
  <h1>Prestataires proposant la catégorie {{ categorie.nom }}</h1>

  {% if prestataires is empty %}
    <p>Aucun prestataire ne propose cette catégorie pour le moment.</p>
  {% else %}
    <ul>
      {% for prestataire in prestataires %}
        <li>
          <h2>{{ prestataire.nom }}</h2>
          <p>{{ prestataire.description }}</p>
          <p><strong>Adresse :</strong> {{ prestataire.adresse }}</p>
          <p><strong>Téléphone :</strong> {{ prestataire.telephone }}</p>
          <p><strong>Email :</strong> {{ prestataire.email }}</p>
          <p><strong>Site web :</strong> {{ prestataire.siteWeb }}</p>
        </li>
      {% endfor %}
    </ul>
  {% endif %}
{% endblock %}

    */
}
