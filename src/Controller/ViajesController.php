<?php
namespace App\Controller;

use App\Entity\Actividades;
use App\Entity\Notificacion;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Participante;
use App\Entity\Viaje;
use App\Entity\Album;
use App\Entity\AlbumMedia;
use App\Entity\Usuario;

class ViajesController extends AbstractController
{
    #[Route(path: '/viajes', name: 'ctrl_viajes')]
    public function viajes(EntityManagerInterface $em)
    {
        $participanteActual = $em->getRepository(Participante::class)->findOneBy([
            'usuario' => $this->getUser()->getIdUsuario(),
        ]);

        if (!$participanteActual) {
            return $this->render('viajes.html.twig', [
                'viajes' => [],
                'participantes' => []
            ]);
        }

        $participaciones = $em->getRepository(Participante::class)->findBy([
            'usuario' => $this->getUser()->getIdUsuario(),
        ]);

        $viajes = [];
        foreach ($participaciones as $participacion) {
            $viajes[] = $participacion->getViaje();
        }

        $participantes = $em->getRepository(Participante::class)->findBy([
            'viaje' => $viajes, 
        ]);

        return $this->render('viajes.html.twig', [
            'participanteActual' => $participanteActual,
            'viajes' => $viajes,
            'participantes' => $participantes
        ]);
    }

    #[Route(path: '/crearViaje', name: 'ctrl_crear_viaje')]
    public function crearViaje(Request $request, EntityManagerInterface $em)
    {
        $nombre = $request->request->get('nombre');
        $destino = $request->request->get('destino');
        $fechaInicio = $request->request->get('fechaInicio');
        $fechaFin = $request->request->get('fechaFin');

        if (!$nombre || !$destino || !$fechaInicio || !$fechaFin) {
            $this->addFlash('error', 'Todos los campos son obligatorios.');
            return $this->redirectToRoute('ctrl_viajes');
        }

        if (new \DateTime($fechaInicio) > new \DateTime($fechaFin)) {
            $this->addFlash('error', 'La fecha de inicio debe ser anterior a la fecha de fin.');
            return $this->redirectToRoute('ctrl_viajes');
        }

        // lo dejo así comentado por si el llorón de miguel nos convence alguna vez de que esto tiene sentido
        // claro que tiene sentido retrasado para que vas a querer hacer un viaje en el pasado
        /* if (new \DateTime($fechaInicio) < new \DateTime()) {
            $this->addFlash('error', 'La fecha de inicio no puede ser en el pasado.');
            return $this->redirectToRoute('ctrl_viajes');
        } */
        
        $viaje = new Viaje();
        $viaje->setNombre($nombre);
        $viaje->setDestino($destino);
        $viaje->setFechaInicio(new \DateTime($fechaInicio));
        $viaje->setFechaFin(new \DateTime($fechaFin));

        $participante = new Participante();
        $participante->setUsuario($this->getUser());
        $participante->setViaje($viaje);

        $album = new Album();
        $album->setViaje($viaje);

        $em->persist($viaje);
        $em->persist($participante);
        $em->persist($album);
        $em->flush();

        return $this->redirectToRoute('ctrl_viajes');
    }

    #[Route(path: '/detallesViaje/{id}', name: 'ctrl_detalles_viaje')]
    public function detallesViaje(int $id, EntityManagerInterface $em)
    {

        $viaje = $em->getRepository(Viaje::class)->find($id);

        $esParticipante = $em->getRepository(Participante::class)->findOneBy([
        'usuario' => $this->getUser()->getIdUsuario(),
        'viaje'   => $viaje
        ]);

        if (!$esParticipante) {
            return $this->redirectToRoute('ctrl_viajes');
        }

        if (!$viaje) {
            return $this->redirectToRoute('ctrl_viajes');
        }

        $participantes = $em->getRepository(Participante::class)->findBy([
            'viaje' => $viaje,
        ]);

        $actividades = $em->getRepository(Actividades::class)->findBy([
            'viaje' => $viaje,
        ]);

        $album = $em->getRepository(Album::class)->findOneBy([
            'viaje' => $viaje,
        ]);

        $fotosAlbum = [];
        if ($album) {
            $fotosAlbum = $em->getRepository(AlbumMedia::class)->findBy(
                ['album' => $album],
                ['id' => 'ASC'],
                6
            );
        }

        return $this->render('detallesviaje.html.twig', [
            'viaje'         => $viaje,
            'participantes' => $participantes,
            'actividades'   => $actividades,
            'album'         => $album,
            'fotosAlbum'    => $fotosAlbum,
        ]);
    }

    #[Route(path: '/agregarActividad/{id}', name: 'ctrl_agregar_actividad')]
    public function agregarActividad(int $id, Request $request, EntityManagerInterface $em)
    {
        $viaje = $em->getRepository(Viaje::class)->find($id);

        $esParticipante = $em->getRepository(Participante::class)->findOneBy([
            'usuario' => $this->getUser()->getIdUsuario(),
            'viaje'   => $viaje
        ]);

        if (!$esParticipante) {
            return $this->redirectToRoute('ctrl_viajes');
        }

        if (!$viaje) {
            return $this->redirectToRoute('ctrl_viajes');
        }

        $actividadNombre = $request->request->get('actividad');
        $actividadDescripcion = $request->request->get('descripcion');

        $actividadFechaInicio = $request->request->get('fecha');
        $actividadFechaFin = $request->request->get('fechaFin');

        $fechaInicioActividad = new \DateTime($actividadFechaInicio);
        $fechaFinActividad = new \DateTime($actividadFechaFin);

        if (!$actividadNombre || !$actividadFechaInicio || !$actividadFechaFin) {
            $this->addFlash('error', 'Todos los campos son obligatorios.');
            return $this->redirectToRoute('ctrl_detalles_viaje', ['id' => $id]);
        }
        if ($fechaInicioActividad > $fechaFinActividad) {
            $this->addFlash('error', 'La fecha de inicio debe ser anterior a la fecha de fin.');
            return $this->redirectToRoute('ctrl_detalles_viaje', ['id' => $id]);
        }
        if ($fechaInicioActividad < $viaje->getFechaInicio() || $fechaFinActividad > $viaje->getFechaFin()) {
            $this->addFlash('error', 'Las fechas deben estar dentro del rango del viaje.');
            return $this->redirectToRoute('ctrl_detalles_viaje', ['id' => $id]);
        }

        $actividad = new Actividades();
        $actividad->setNombre($actividadNombre);
        $actividad->setDescripcion($actividadDescripcion);
        $actividad->setFechaInicio($fechaInicioActividad);
        $actividad->setFechaFin($fechaFinActividad);
        $actividad->setViaje($viaje);

        $em->persist($actividad);
        $em->flush();

        return $this->redirectToRoute('ctrl_detalles_viaje', ['id' => $id]);
    }

    #[Route(path: '/eliminarActividad/{id}', name: 'ctrl_eliminar_actividad')]
    public function eliminarActividad(int $id, EntityManagerInterface $em)
    {
        $actividad = $em->getRepository(Actividades::class)->find($id);

        if (!$actividad) {
            return $this->redirectToRoute('ctrl_viajes');
        }

        $viaje = $actividad->getViaje();

        $esParticipante = $em->getRepository(Participante::class)->findOneBy([
            'usuario' => $this->getUser()->getIdUsuario(),
            'viaje'   => $viaje
        ]);

        if (!$esParticipante) {
            return $this->redirectToRoute('ctrl_viajes');
        }

        $em->remove($actividad);
        $em->flush();

        return $this->redirectToRoute('ctrl_detalles_viaje', ['id' => $viaje->getIdViaje()]);
    }

    #[Route('/buscarUsuarios', name: 'buscar_usuarios', methods: ['GET'])]
    public function buscarUsuarios(Request $request, EntityManagerInterface $em): JsonResponse 
    {
        $query = $request->query->get('q', '');

        if (strlen($query) < 1) {
            return new JsonResponse([]);
        }

        $usuarios = $em->getRepository(Usuario::class)->createQueryBuilder('u')
            ->where('u.nombreUsuario LIKE :q')
            ->setParameter('q', '%' . $query . '%')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        $data = [];
        foreach ($usuarios as $user) {
            $data[] = [
                'nombre'       => $user->getNombreUsuario(),
                'nombreUsuario' => $user->getNombreUsuario(),
                'fotoPerfil'   => $user->getFotoPerfil() ?: 'default.jpg',
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/agregarParticipante/{id}', name: 'ctrl_agregar_participante', methods: ['POST'])]
    public function agregarParticipante(int $id, Request $request, EntityManagerInterface $em)
    {
        $viaje = $em->getRepository(Viaje::class)->find($id);
        $nombreUsuario = $request->request->get('nombreUsuario');
        $usuario = $em->getRepository(Usuario::class)->findOneBy(['nombreUsuario' => $nombreUsuario]);

        if (!$usuario) {
            $this->addFlash('error', 'El usuario "' . $nombreUsuario . '" no existe.');
            return $this->redirectToRoute('ctrl_detalles_viaje', ['id' => $id]);
        }

        $existe = $em->getRepository(Participante::class)->findOneBy([
            'viaje' => $viaje,
            'usuario' => $usuario
        ]);

        $nombreViaje = $viaje->getNombre();

        $listaParticipantes = '';
        foreach ($viaje->getParticipantes() as $p) {
            $listaParticipantes .= $p->getUsuario()->getNombreUsuario() . ', ';
        }
        $listaParticipantes = rtrim($listaParticipantes, ', ');

        if (!$existe) {

            $notificacion = new Notificacion();
            $notificacion->setUsuario($usuario);
            $notificacion->setViaje($viaje);
            $notificacion->setTitulo("Solicitud para unirse al viaje");
            $notificacion->setContenido('Se ha solicitado que te unas al viaje: ' . $nombreViaje . ' de ' . $listaParticipantes);
            $notificacion->setFecha(new \DateTime());

            $em->persist($notificacion);

            $em->flush();
        } else {
            $this->addFlash('error', 'Ya está en el viaje.');
        }

        return $this->redirectToRoute('ctrl_detalles_viaje', ['id' => $id]);
    }

}