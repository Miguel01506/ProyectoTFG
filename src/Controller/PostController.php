<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Entity\Media;
use App\Entity\PostMedia;
use App\Entity\Post;
use App\Entity\Viaje;
use App\Entity\Comentario;
use App\Entity\Usuario;
use App\Entity\Reaccion;
use DateTime;

class PostController extends AbstractController
{
    #[Route('/addPost/{idViaje}', name: 'ctrl_addPost', methods: ['POST'])]
    public function addPost(int $idViaje, Request $request, EntityManagerInterface $em)
    {
        $viaje = $em->getRepository(Viaje::class)->find($idViaje);

        if (!$viaje) {
            $this->addFlash('error', 'Viaje no encontrado.');
            return $this->redirectToRoute('ctrl_viajes');
        }

        $contenido = $request->request->get('contenido');

        if (!$contenido = $request->request->get('contenido')) {
            $this->addFlash('error', 'El contenido del post no puede estar vacío.');
            return $this->redirectToRoute('ctrl_detalles_viaje', ['id' => $idViaje]);
        }

        $archivos = $request->files->get('files');
        $usuario = $this->getUser();

        if (count($archivos) > 5) {
            $this->addFlash('error', 'No puedes subir más de 5 archivos.');
            return $this->redirectToRoute('ctrl_detalles_viaje', ['id' => $idViaje]);
        }

        // controllar que sean o imagen o video
        $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'mov', 'webp', 'mp4', 'avi'];
        foreach ($archivos as $archivo) {
            if ($archivo) {
                $extension = strtolower($archivo->getClientOriginalExtension());
                if (!in_array($extension, $extensionesPermitidas)) {
                    $this->addFlash('error', 'Archivo no permitido, solo imagenes o videos: ' . $archivo->getClientOriginalName());
                    return $this->redirectToRoute('ctrl_detalles_viaje', ['id' => $idViaje]);
                }
            }
        }

        $post = new Post();
        $post->setViaje($viaje);
        $post->setAutor($usuario);
        $post->setTexto($contenido);
        $post->setFechaPublicacion(new DateTime());

        $em->persist($post);

        foreach ($archivos as $archivo) {
            if ($archivo) {
                // esto es para que si se sube un archivo con el mismo nombre que no se sobreescriba el otro
                // el uniqid genera un id creo que basándose en la fecha actual, así que sería mala suerte que se sobreescriban
                $nombreArchivo = uniqid() . '_' . $archivo->getClientOriginalName();
                $archivo->move($this->getParameter('posts_directory'), $nombreArchivo);

                $extension = strtolower($archivo->getClientOriginalExtension());

                $media = new Media();
                $media->setTipo($extension);
                $media->setRutaArchivo($nombreArchivo);
                $media->setUsuario($usuario);
                $media->setFechaSubida(new DateTime());

                $em->persist($media);

                $postMedia = new PostMedia();
                $postMedia->setPost($post);
                $postMedia->setMedia($media);

                $em->persist($postMedia);
            }
        }

        $em->flush();

        $this->addFlash('mensaje', 'Post publicado con éxito.');
        return $this->redirectToRoute('ctrl_detalles_viaje', ['id' => $idViaje]);
    }

    #[Route('/post/{id}', name: 'ctrl_postDetalle')]
    public function postDetalle(int $id, EntityManagerInterface $em): Response
    {
        $post = $em->getRepository(Post::class)->find($id);

        if (!$post) {
            return $this->redirectToRoute('ctrl_home');
        }

        return $this->render('postDetalle.html.twig', [
            'post' => $post,
            'viaje' => $post->getViaje(),
        ]);
    }

    #[Route('/post/{id}/comentar', name: 'ctrl_addComentario', methods: ['POST'])]
    public function addComentario(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $post = $em->getRepository(Post::class)->find($id);

        if (!$post) {
            return $this->redirectToRoute('ctrl_home');
        }

        $texto = $request->request->get('texto_comentario');
        $usuario = $this->getUser();

        if (empty(trim($texto))) {
            $this->addFlash('error', 'El comentario no puede estar vacío.');
            return $this->redirectToRoute('ctrl_postDetalle', ['id' => $id]);
        }

        $comentario = new Comentario();
        $comentario->setTexto($texto);
        $comentario->setPost($post);
        $comentario->setUsuario($usuario);
        $comentario->setFechaComentario(new DateTime());

        $em->persist($comentario);
        $em->flush();

        return $this->redirectToRoute('ctrl_postDetalle', ['id' => $id]);
    }

    #[Route('/post/{id}/reaccionar/{tipo}', name: 'ctrl_reaccionar', methods: ['GET'])]
    public function reaccionar(int $id, string $tipo, EntityManagerInterface $em, Request $req): Response
    {
        if (!in_array($tipo, ['like', 'dislike'])) {
            return $this->redirect($req->headers->get('referer'));
        }

        $post = $em->getRepository(Post::class)->find($id);
        // no pongo mensaje de error porque considero que a veces no hace falta, si no existe el post 
        // será porque el usuario habrá metido el ID mal en la URL y punto
        if (!$post) {
            return $this->redirectToRoute('ctrl_home');
        }

        $usuario = $this->getUser();

        $reaccion = $em->getRepository(Reaccion::class)->findOneBy(['post' => $post, 'usuario' => $usuario]);

        if (!$reaccion) {
            $reaccion = new Reaccion();
            $reaccion->setPost($post);
            $reaccion->setUsuario($usuario);
            $reaccion->setTipo($tipo);
            $reaccion->setFechaReaccion(new DateTime());

            $em->persist($reaccion);
        } else {
            if ($reaccion->getTipo() == $tipo) {
                $em->remove($reaccion);             
            } else {
                $reaccion->setTipo($tipo);
                $reaccion->setFechaReaccion(new DateTime());

                $em->persist($reaccion);
            }
        }

        $em->flush();

        $totalLikes = $em->getRepository(Reaccion::class)->count(['post' => $post, 'tipo' => 'like']);
        $totalDislikes = $em->getRepository(Reaccion::class)->count(['post' => $post, 'tipo' => 'dislike']);

        // esto lo que hace es obtener el referer, que es la página desde donde se envia
        // porque como hay postDetalle y feed lo pongo así, para que dirija de donde venga y no tener
        // que hacer dos controladores o algo
        // return $this->redirect($req->headers->get('referer')); lo comento que voy a intentar hacerlo con json

        return new JsonResponse([
            'success' => true,
            'totalLikes' => $totalLikes,
            'totalDislikes' => $totalDislikes,
        ]);
    }
}