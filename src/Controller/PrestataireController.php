<?php

namespace App\Controller;

use App\Entity\Prestataire;
use App\Entity\Proposer;
use App\Entity\Utilisateur;
use App\Form\PrestataireFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;


class PrestataireController extends AbstractController
{
    #[Route('/devenir-prestataire', name: 'app_prestataire_form')]
    public function devenirPrestataire(Request $request, EntityManagerInterface $entityManager): Response
    {


        $prestataire = new Prestataire();
        $proposer = new Proposer();

        $form = $this->createForm(PrestataireFormType::class, $prestataire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $id = $this->getUser()->getId();
            $repository = $entityManager->getRepository(Utilisateur::class);
            $utilisateur = $repository->find($id);
            $utilisateur->setRoles(['ROLE_PRESTATAIRE']);




            $prestataire->setUtilisateur($this->getUser());

            $entityManager->persist($prestataire);
            $entityManager->flush();




            $this->addFlash('Succès', 'Votre demande pour devenir prestataire a bien été enregistrée.');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('prestataire/inscription.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
