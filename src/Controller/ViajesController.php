<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Participante;
use App\Entity\Viaje;

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

        $em->persist($viaje);
        $em->persist($participante);
        $em->flush();

        return $this->redirectToRoute('ctrl_viajes');
    }

}