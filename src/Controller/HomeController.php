<?php

namespace App\Controller;

use App\Entity\CategorieDeServices;
use App\Repository\PrestataireRepository;
use App\Entity\Prestataire;
use App\Form\PrestataireSearchType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;


class HomeController extends AbstractController
{


    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager, Request $request, PaginatorInterface $paginator,  PrestataireRepository $prestataireRepository): Response
    {
        $form = $this->createForm(PrestataireSearchType::class);
        $form->handleRequest($request);

        // Si le formulaire est soumis, exécuter la recherche et afficher les résultats
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $nom = $data['nom'];
            $categories = $data['categories'] ? $data['categories']->getId() : null;
            $localite = $data['localite'] ? $data['localite']->getId() : null;
            $codePostal = $data['codePostal'] ? $data['codePostal']->getId() : null;
            $commune = $data['commune'] ? $data['commune']->getId() : null;

            $prestataires = $prestataireRepository->findByFilters($nom, $categories, $localite, $codePostal, $commune);
            $pagination = $paginator->paginate(
                $prestataires,
                $request->query->getInt('page', 1), // Numéro de page à afficher
                12
            ); // Nombre de résultats par page

            // Afficher les résultats de la recherche
            return $this->render('prestataire/recherche.html.twig', [
                'prestataires' => $prestataires,
                'form' => $form->createView(),
                'pagination' => $pagination
            ]);
        }



        // Les 4 derniers prestaires inscrit
        $prestataires = $entityManager->createQueryBuilder()
            ->select('p')
            ->from(Prestataire::class, 'p')
            ->leftJoin('p.utilisateur', 'u')
            ->orderBy('u.inscription', 'DESC')
            ->setMaxResults(4)
            ->getQuery()
            ->getResult();


        // Récupération des catégories en avant
        $categories = $entityManager->getRepository(CategorieDeServices::class)
            ->findBy(['en_avant' => true]);

        return $this->render('home/index.html.twig', [
            'prestataires' => $prestataires,
            'categories' => $categories,
            'form' => $form->createView()



        ]);
    }
}
