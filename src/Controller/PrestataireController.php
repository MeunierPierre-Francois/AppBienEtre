<?php

namespace App\Controller;

use App\Entity\Prestataire;
use App\Entity\Images;
use App\Entity\Proposer;
use App\Entity\Utilisateur;
use App\Form\PrestataireFormType;
use App\Service\PictureService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DomCrawler\Image;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;



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
    public function index(EntityManagerInterface $entityManager): Response
    {

        $repository = $entityManager->getRepository(Prestataire::class);
        $prestataires = $repository->findBy([]);

        return $this->render('prestataire/liste.html.twig', [
            'prestataires' => $prestataires
        ]);
    }
}
