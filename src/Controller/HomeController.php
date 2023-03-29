<?php

namespace App\Controller;

use App\Entity\CategorieDeServices;
use App\Entity\Prestataire;
use App\Entity\Proposer;
use App\Repository\ProposerRepository;
use App\Form\SearchFormType;
use App\Model\SearchData;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager, Request $request, PaginatorInterface $paginator, ProposerRepository $proposerRepository): Response
    {
        // Les 4 derniers prestaires inscrit
        $prestataires = $entityManager->createQueryBuilder()
            ->select('p')
            ->from(Prestataire::class, 'p')
            ->leftJoin('p.utilisateur', 'u')
            ->orderBy('u.inscription', 'ASC')
            ->setMaxResults(4)
            ->getQuery()
            ->getResult();


        // Récupération des catégories en avant
        $categories = $entityManager->getRepository(CategorieDeServices::class)
            ->findBy(['en_avant' => true]);


        $searchData = new SearchData();
        $form = $this->createForm(SearchFormType::class, $searchData);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->page = $request->query->getInt('page', 1);
            $proposers = $proposerRepository->findBySearch($searchData);
            dd($searchData);
            return $this->render('prestataire/recherche.html.twig', [
                'form' => $form->createView(),
                'proposers' => $proposers
            ]);
        }

        return $this->render('home/index.html.twig', [
            'prestataires' => $prestataires,
            'categories' => $categories,
            'form' => $form->createView(),

        ]);
    }
}
