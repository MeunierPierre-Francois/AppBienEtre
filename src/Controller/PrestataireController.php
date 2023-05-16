<?php

namespace App\Controller;

use App\Repository\PrestataireRepository;
use Knp\Component\Pager\PaginatorInterface;
use App\Entity\Images;
use App\Entity\Proposer;
use App\Entity\Prestataire;
use App\Entity\Promotion;
use App\Entity\Stage;
use App\Entity\Utilisateur;
use App\Service\PictureService;
use App\Form\PrestataireFormType;
use App\Form\PrestataireSearchType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;





class PrestataireController extends AbstractController
{



    #[Route('/devenir-prestataire', name: 'app_prestataire_form')]
    public function devenirPrestataire(Request $request, EntityManagerInterface $entityManager, PictureService $pictureService): Response
    {

        $prestataire = new Prestataire();
        $form = $this->createForm(PrestataireFormType::class, $prestataire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //On récupère les images
            $images = $form->get('images')->getData();
            foreach ($images as $image) {
                //On définit le dossier de destination
                $folder = 'prestataires';
                //On appelle le service d'ajout
                $fichier = $pictureService->add($image, $folder, 300, 300);

                $img = new Images();
                $img->setNom($fichier);
                $prestataire->addImage($img);
            }

            //changer le rôle de l'utilisateur
            $id = $this->getUser()->getId();
            $repository = $entityManager->getRepository(Utilisateur::class);
            $utilisateur = $repository->find($id);
            $utilisateur->setRoles(['ROLE_PRESTATAIRE']);

            $prestataire->setUtilisateur($this->getUser());

            // Récupérer les catégories sélectionnées dans le formulaire
            $categories = $form->get('categories')->getData();

            // Mettre à jour la table proposer pour lier les catégories au prestataire
            foreach ($categories as $category) {
                $proposer = new Proposer();
                $proposer->setPrestataire($prestataire);
                $proposer->setCategorieService($category);
                $entityManager->persist($proposer);
            }

            $entityManager->persist($prestataire);
            $entityManager->flush();

            $this->addFlash('Succès', 'Votre demande pour devenir prestataire a bien été enregistrée.');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('prestataire/inscription.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/prestataires', name: 'app_prestataire_liste')]
    public function index(EntityManagerInterface $entityManager, Request $request, PrestataireRepository $prestataireRepository, PaginatorInterface $paginator): Response
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

        $repository = $entityManager->getRepository(Prestataire::class);
        $prestataires = $repository->findBy([]);

        return $this->render('prestataire/liste.html.twig', [
            'prestataires' => $prestataires,
            'form' => $form->createView()



        ]);;
    }

    #[Route('/prestataires/profil/{id}', name: 'app_prestataire_detail')]
    public function show(EntityManagerInterface $entityManager, int $id, PrestataireRepository $prestataireRepository): Response
    {

        $prestataire = $entityManager->getRepository(Prestataire::class)
            ->createQueryBuilder('p')
            ->leftJoin('p.utilisateur', 'u')
            ->addSelect('u')
            ->leftJoin('u.commune', 'c')
            ->addSelect('c')
            ->leftJoin('u.localite', 'l')
            ->addSelect('l')
            ->leftJoin('u.code_postal', 'cp')
            ->addSelect('cp')
            ->where('p.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();

        $categories = $entityManager->getRepository(Proposer::class)->findCategoriesByPrestataireId($id);

        $currentDate = new \DateTime();

        $promotions = $entityManager->getRepository(Promotion::class)
            ->createQueryBuilder('p')
            ->leftJoin('p.categorie_service', 'cs')
            ->addSelect('cs')
            ->where('p.prestataire = :prestataire_id')
            ->andWhere('p.affichage_de <= :current_date')
            ->andWhere('p.affichage_jusque >= :current_date')
            ->setParameter('prestataire_id', $id)
            ->setParameter('current_date', $currentDate)
            ->getQuery()
            ->getResult();



        $stages = $entityManager->getRepository(Stage::class)
            ->createQueryBuilder('s')
            ->where('s.prestataire = :prestataire_id')
            ->andWhere('s.affichage_de <= :current_date')
            ->andWhere('s.affichage_jusque >= :current_date')
            ->setParameter('prestataire_id', $id)
            ->setParameter('current_date', $currentDate)
            ->getQuery()
            ->getResult();


        $images = [
            'image1.jpg',
            'image2.jpg',
            'image3.jpg',
            'image4.jpg',
            'image5.jpg',
            'image6.jpg',

        ];

        $prestatairesSimilaires = $prestataireRepository->findPrestatairesSimilaires($prestataire);




        return $this->render('prestataire/detail.html.twig', [
            'prestataire' => $prestataire,
            'categories' => $categories,
            'stages' => $stages,
            'promotions' => $promotions,
            'images' => $images,
            'prestatairesSimilaires' => $prestatairesSimilaires,


        ]);
    }

    #[Route('/prestataires/promotion', name: 'app_promotion_telechargement')]
    public function telechargement(string $pdf): BinaryFileResponse
    {
        $cheminFichier = $this->getParameter('chemin_dossier_promotions') . '/' . $pdf;

        $response = new BinaryFileResponse($cheminFichier);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $pdf);

        return $response;
    }



    /*#[Route('/prestataires/recherche', name: 'app_prestataire_recherche')]
    public function search(Request $request, PrestataireRepository $prestataireRepository, PaginatorInterface $paginator): Response
    {
        // Créer un formulaire de recherche de prestataires
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
            return $this->render('prestataire/liste.html.twig', [
                'prestataires' => $prestataires,
                'form' => $form->createView(),
                'pagination' => $pagination
            ]);
        }

        // Afficher le formulaire de recherche
        return $this->render('components/_search_presta.html.twig', [
            'form' => $form->createView()
        ]);
    }*/
}
