<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Post;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'ctrl_home')]
    public function home(EntityManagerInterface $em)
    {
        $posts = $em->getRepository(Post::class)->findBy([], ['fechaPublicacion' => 'DESC']);

        return $this->render('home.html.twig', [
            'posts' => $posts
        ]);
    }
}