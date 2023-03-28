<?php

namespace App\Controller;

use App\Entity\CategorieDeServices;
use App\Entity\Utilisateur;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use App\Security\UtilisateurAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;


class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/inscription', name: 'app_inscription')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UtilisateurAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {

        // On récupère les données pour alimenter les champs de choix
        $json = file_get_contents(__DIR__ . '/../../public/json/villes.json');
        $data = json_decode($json, true);
        // Créer un tableau vide pour stocker les villes, les codes postaux et les régions uniques
        $villes = [];
        $codePostaux = [];
        $regions = [];

        // Parcourir le tableau des données et ajouter les villes, les codes postaux et les régions uniques au tableau correspondant
        foreach ($data as $item) {
            if (!in_array($item['ville'], $villes)) {
                $villes[$item['ville']] = $item['ville'];
            }
            if (!in_array($item['codePostal'], $codePostaux)) {
                $codePostaux[$item['codePostal']] = $item['codePostal'];
            }
            if (!in_array($item['region'], $regions)) {
                $regions[$item['region']] = $item['region'];
            }
        }
        asort($codePostaux, SORT_NUMERIC);
        asort($villes);
        asort($regions);

        $user = new Utilisateur();
        $user->setInscription(new \DateTimeImmutable());
        $user->setRoles(['ROLE_USER']);
        $form = $this->createForm(RegistrationFormType::class, $user, [
            'villes' => $villes,
            'codePostaux' => $codePostaux,
            'regions' => $regions,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $entityManager->persist($user);
            $entityManager->flush();

            $this->emailVerifier->sendEmailConfirmation(
                'app_verify_email',
                $user,
                (new TemplatedEmail())
                    ->from(new Address('pf.meunier@hotmail.com', 'BienEtre Bot'))
                    ->to($user->getEmail())
                    ->subject("Confirmation d'email")
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );


            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),

        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');


        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_inscription');
        }


        $this->addFlash('Bien joué !', 'Votre Email a bien été vérifié');

        return $this->redirectToRoute('app_home');
    }
}
