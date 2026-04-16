<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'ctrl_home')]
    public function home()
    {
        return $this->render('home.html.twig');
    }
}