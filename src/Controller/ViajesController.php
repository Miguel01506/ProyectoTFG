<?php
namespace App\Controller;

use App\Entity\Actividades;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Participante;
use App\Entity\Viaje;
use App\Entity\Album;

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
            return $this->redirectToRoute('ctrl_viajes');
        }

        if (new \DateTime($fechaInicio) > new \DateTime($fechaFin)) {
            return $this->redirectToRoute('ctrl_viajes');
        }

        if (new \DateTime($fechaInicio) < new \DateTime()) {
            return $this->redirectToRoute('ctrl_viajes');
        }
        
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

        $album = $viaje->getAlbum();

        return $this->render('detallesviaje.html.twig', [
            'viaje' => $viaje,
            'participantes' => $participantes,
            'actividades' => $actividades,
            'album' => $album
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
            return $this->redirectToRoute('ctrl_detalles_viaje', ['id' => $id, 'error' => 'missing_fields']);
        }
        if ($fechaInicioActividad > $fechaFinActividad) {
            return $this->redirectToRoute('ctrl_detalles_viaje', ['id' => $id, 'error' => 'invalid_dates']);
        }
        if ($fechaInicioActividad < new \DateTime() || $fechaFinActividad < new \DateTime()) {
            return $this->redirectToRoute('ctrl_detalles_viaje', ['id' => $id]);
        }
        if ($fechaInicioActividad < $viaje->getFechaInicio() || $fechaFinActividad > $viaje->getFechaFin()) {
            return $this->redirectToRoute('ctrl_detalles_viaje', ['id' => $id, 'error' => 'invalid_dates']);
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

}