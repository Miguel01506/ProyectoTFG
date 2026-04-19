<?php
namespace App\Controller;

use App\Entity\AlbumMedia;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Participante;
use App\Entity\Album;
use App\Entity\Media;
use App\Entity\Viaje;

class AlbumController extends AbstractController
{
    #[Route(path: '/album/{id}', name: 'ctrl_album')]
    public function album(EntityManagerInterface $em, int $id)
    {
        $album = $em->getRepository(Album::class)->findOneBy([
            'viaje' => $id,
        ]);

        $participanteActual = $em->getRepository(Participante::class)->findOneBy([
            'usuario' => $this->getUser(),
        ]);

        $medias = $em->createQuery('
            SELECT m
            FROM App\Entity\Media m
            JOIN App\Entity\AlbumMedia am WITH am.media = m
            WHERE am.album = :idAlbum
            ORDER BY m.fechaSubida DESC
        ')
            ->setParameter('idAlbum', $album->getId())
            ->getResult();


        return $this->render('albumviaje.html.twig', [
            'album' => $album,
            'participanteActual' => $participanteActual,
            'contenidoAlbum' => $medias

        ]);
    }

    #[Route(path: '/subir_contenido/{id}', name: 'ctrl_subir_contenido')]
    public function subirContenido(Request $request, EntityManagerInterface $em, int $id)
    {
        $album = $em->getRepository(Album::class)->findOneBy(['viaje' => $id]);

        if (!$album) {
            return $this->render('albumviaje.html.twig', [
                'album' => null,
                'participanteActual' => null,
                'contenidoAlbum' => [],
                'error' => 'Álbum no encontrado'
            ]);

        }

        $archivos = $request->files->get('archivos');

        if (!$archivos) {
            return $this->redirectToRoute('ctrl_album', ['id' => $id]);
        }

        foreach ($archivos as $archivo) {

            if (!$archivo->isValid()) {
                continue;
            }

            $mimeType = $archivo->getMimeType();

            if (!str_starts_with($mimeType, 'image/') && !str_starts_with($mimeType, 'video/')) {
                continue;
            }  

            $dirDestino = $this->getParameter('kernel.project_dir') 
                . '/public/uploads/album_' . $album->getId() . '/';

            if (!is_dir($dirDestino)) {
                mkdir($dirDestino, 0755, true);
            }

            $nombreArchivo = uniqid() . '.' . $archivo->guessExtension();
            $archivo->move($dirDestino, $nombreArchivo);

            $media = new Media();
            $media->setRutaArchivo('album_' . $album->getId() . '/' . $nombreArchivo);
            $media->setFechaSubida(new \DateTime());
            $media->setUsuario($this->getUser());

            $esVideo = str_starts_with($mimeType, 'video/');

            if ($esVideo){
                $media->setTipo('video');
            } else {
                $media->setTipo('foto');
            }

            $em->persist($media);

            $albumMedia = new AlbumMedia();
            $albumMedia->setAlbum($album);
            $albumMedia->setMedia($media);

            $em->persist($albumMedia);
        }

        $em->flush();

        return $this->redirectToRoute('ctrl_album', ['id' => $id]);
    }

    #[Route(path: '/eliminar_contenido/{id}', name: 'ctrl_eliminar_contenido')]
    public function eliminarContenido(EntityManagerInterface $em, int $id)
    {
        $media = $em->getRepository(Media::class)->find($id);

        if (!$media) {
            return $this->redirectToRoute('ctrl_viajes');
        }

        $albumMedia = $em->getRepository(AlbumMedia::class)->findOneBy(['media' => $media]);

        if ($albumMedia) {
            $em->remove($albumMedia);
        }

        $rutaArchivo = $this->getParameter('kernel.project_dir') . '/public/uploads/' . $media->getRutaArchivo();

        if (file_exists($rutaArchivo)) {
            unlink($rutaArchivo);
        }

        $em->remove($media);
        $em->flush();

        return $this->redirectToRoute('ctrl_album', ['id' => $albumMedia->getAlbum()->getViaje()->getIdViaje()]);
    }
}