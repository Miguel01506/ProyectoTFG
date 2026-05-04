<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'ctrl_login')]
    public function login(AuthenticationUtils $authUtils)
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('ctrl_profile');
        }

        // obtener último email ingresado
        $lastEmail = $authUtils->getLastUsername();

        // obtener error de login si lo hay
        $error = $authUtils->getLastAuthenticationError();

        return $this->render('login.html.twig', [
            'last_email' => $lastEmail,
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'ctrl_logout')]
    public function logout()
    {
    }
}