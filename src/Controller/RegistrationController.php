<?php

namespace App\Controller;


use App\Entity\Commune;
use App\Entity\CodePostal;
use App\Entity\Localite;
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
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher,  EntityManagerInterface $entityManager): Response
    {

        // On récupère les données pour alimenter les champs de choix
        $json = file_get_contents(__DIR__ . '/../../public/json/villes.json');
        $data = json_decode($json, true);

        $communes = [];
        $localites = [];
        $codesPostaux = [];

        foreach ($data as $item) {
            // Récupérer les données de l'item
            $communeData = $item['region'];
            $localiteData = $item['ville'];
            $codePostalData = $item['codePostal'];

            //Vérifier si la commune existe déjà
            $commune = $entityManager->getRepository(Commune::class)->findOneBy(['commune' => $communeData]);
            if (!$commune) {
                // Créer l'objet Commune
                $commune = new Commune();
                $commune->setCommune($communeData);

                // Persister la commune
                $entityManager->persist($commune);
            }

            // Enregistrer la commune dans le tableau pour éviter les doublons
            $communes[$communeData] = $commune;

            // Vérifier si la localité existe déjà
            $localite = $entityManager->getRepository(Localite::class)->findOneBy(['localite' => $localiteData]);
            if (!$localite) {
                // Créer l'objet Localite
                $localite = new Localite();
                $localite->setLocalite($localiteData);

                // Persister la localité
                $entityManager->persist($localite);
            }

            // Enregistrer la localité dans le tableau pour éviter les doublons
            $localites[$localiteData] = $localite;

            // Vérifier si le code postal existe déjà
            $codePostal = $entityManager->getRepository(CodePostal::class)->findOneBy(['code_postal' => $codePostalData]);
            if (!$codePostal) {
                // Créer l'objet CodePostal
                $codePostal = new CodePostal();
                $codePostal->setCodePostal($codePostalData);

                // Persister le code postal
                $entityManager->persist($codePostal);
            }

            // Enregistrer le code postal dans le tableau pour éviter les doublons
            $codesPostaux[$codePostalData] = $codePostal;
        }

        // Enregistrer les changements dans la base de données
        $entityManager->flush();

        $user = new Utilisateur();
        $user->setInscription(new \DateTimeImmutable());
        $user->setRoles(['ROLE_USER']);
        $form = $this->createForm(RegistrationFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $datas = $form->getData();
            // Récupérer les entités correspondantes à la région, la ville et le code postal choisis par l'utilisateur
            $commune = $entityManager->getRepository(Commune::class)->find($datas->getCommune());
            $localite = $entityManager->getRepository(Localite::class)->find($datas->getLocalite());
            $codePostal = $entityManager->getRepository(CodePostal::class)->find($datas->getCodePostal());

            // Associer les entités Region, Ville et CodePostal à l'entité Utilisateur
            $user->setCommune($commune);
            $user->setLocalite($localite);
            $user->setCodePostal($codePostal);

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
            $this->addFlash('success', 'Un email de confirmation vous a été envoyé. Veuillez vérifier votre adresse email pour activer votre compte.');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),

        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator, UserAuthenticatorInterface $userAuthenticator, UtilisateurAuthenticator $authenticator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');


        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_inscription');
        }
        $userAuthenticator->authenticateUser(
            $this->getUser(),
            $authenticator,
            $request
        );

        $this->addFlash('success', 'Votre adresse email a été vérifiée. Votre compte est maintenant activé.');
        return $this->redirectToRoute('app_home');
    }
}
