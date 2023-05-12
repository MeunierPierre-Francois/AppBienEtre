<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/connexion', name: 'app_connexion')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $user = $this->getUser();
        if ($user) {
            // Vérifier si l'email de l'utilisateur est vérifié
            if (!$user->isVerified()) {
                // Si l'email n'a pas été vérifié, renvoyer l'utilisateur à la page de confirmation
                return $this->redirectToRoute('app_verify_email');
            }

            // Authentifier l'utilisateur
            $authenticator = $this->get('app.authenticator');
            $token = $authenticator->authenticateUser($user, 'main');

            // Connecter l'utilisateur
            if ($user->isVerified()) {
                $this->get('security.token_storage')->setToken($token);
                $this->get('session')->set('_security_main', serialize($token));
            }

            // Rediriger l'utilisateur vers la page d'accueil
            return $this->redirectToRoute('app_home');
        }


        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/deconnexion', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
