<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Seguimiento;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Usuario;
use App\Entity\Post;
use App\Entity\Comentario;
use Symfony\Component\HttpFoundation\Request;

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

    #[Route('/busquedaUsuarios', name: 'ctrl_busquedaUsuarios')]
    public function busquedaUsuarios(EntityManagerInterface $em, Request $request)
    {
        $username = $request->get('username');
        $repo = $em->getRepository(Usuario::class);

        if (empty($username)) {
            $usuarios = $repo->findAll();
        } else {
            $usuarios = $repo->createQueryBuilder('u')
                ->where('u.nombreUsuario LIKE :username')
                ->setParameter('username', '%' . $username . '%')
                ->getQuery()
                ->getResult();
        }

        return $this->render('busquedaUsuarios.html.twig', [
            'username' => $username,
            'usuarios' => $usuarios
        ]);
    }

    #[Route('/profileExterno/{id}', name: 'ctrl_profileExterno')]
    public function profileExterno(EntityManagerInterface $em, int $id)
    {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($this->getUser()->getActivo() == false) {
            return $this->render('registro.html.twig', [
                'error' => 'Tu cuenta no ha sido activada. Por favor, revisa tu correo para activar tu cuenta.',
            ]);
        }

        if ($id == $this->getUser()->getIdUsuario()) {
            return $this->redirectToRoute('ctrl_profile');
        }

        $usuario = $em->getRepository(Usuario::class)->find($id);

        if (!$usuario) {
            return $this->render('profileExterno.html.twig', [
                'error' => 'No se ha encontrado ningun usuario con este id',
                'usuario' => null,
                'numSeguidores' => 0,
                'numSeguidos' => 0,
            ]);
        }
        $numSeguidores = $em->getRepository(Seguimiento::class)->count([
            'seguido' => $id,
            'estado' => 'aceptado'
        ]);

        $numSeguidos = $em->getRepository(Seguimiento::class)->count([
            'seguidor' => $id,
            'estado' => 'aceptado'
        ]);

        $posts = $em->getRepository(Post::class)->findBy(['autor' => $usuario], ['fechaPublicacion' => 'DESC']);

        $numComentarios = [];
        foreach ($posts as $post) {
            $numComentarios[$post->getIdPost()] = $em->getRepository(Comentario::class)->count(['post' => $post->getIdPost()]);
        }

        return $this->render('profileExterno.html.twig', [
            'usuario' => $usuario,
            'numSeguidores' => $numSeguidores,
            'numSeguidos' => $numSeguidos,
            'posts' => $posts,
            'numComentarios' => $numComentarios
        ]);
    }

}