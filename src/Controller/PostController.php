<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Entity\Media;
use App\Entity\PostMedia;
use App\Entity\Post;
use App\Entity\Viaje;
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
}