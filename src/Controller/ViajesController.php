<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
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
}