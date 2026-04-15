<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Seguimiento;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Usuario;
use App\Entity\Post;
use App\Entity\Comentario;

class ProfileController extends AbstractController
{
    #[Route(path: '/profile', name: 'ctrl_profile')]
    public function profile(EntityManagerInterface $em)
    {
        $usuarioID = $this->getUser()->getIdUsuario();

        $numSeguidores = $em->getRepository(Seguimiento::class)->count([
            'seguido' => $usuarioID,
            'estado' => 'aceptado'
        ]);

        $numSeguidos = $em->getRepository(Seguimiento::class)->count([
            'seguidor' => $usuarioID,
            'estado' => 'aceptado'
        ]);

        $posts = $em->getRepository(Post::class)->findBy(['autor' => $usuarioID], ['fechaPublicacion' => 'DESC']);

        $numComentarios = [];
        foreach ($posts as $post) {
            $numComentarios[$post->getId()] = $em->getRepository(Comentario::class)->count(['post' => $post->getId()]);
        }

        return $this->render('profile.html.twig', [
            'numSeguidores' => $numSeguidores,
            'numSeguidos' => $numSeguidos,
            'posts' => $posts,
            'numComentarios' => $numComentarios
        ]);
    }

}