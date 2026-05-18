<?php

namespace App\Controller;

use App\Entity\Viaje;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Notificacion; 
use App\Entity\Participante;


class NotisController extends AbstractController
{
    #[Route('/notificaciones', name: 'ctrl_notificaciones')]
    public function notificaciones(EntityManagerInterface $em)
    {
        $usuario = $this->getUser();

        if (!$usuario) {
            return $this->redirectToRoute('app_login'); 
        }

        $notificaciones = $em->getRepository(Notificacion::class)->findBy(
            ['usuario' => $usuario],
            ['fecha' => 'DESC']
        );

        return $this->render('notificaciones.html.twig', [
            'notificaciones' => $notificaciones
        ]);
    }

    #[Route('/notificacion/{id}/{idViaje}/{accion}', name: 'ctrl_responder_notificacion')]
    public function responderNotificacion(int $id, int $idViaje, string $accion, EntityManagerInterface $em) 
    {
        $usuarioLogueado = $this->getUser();

        if (!$usuarioLogueado) {
            return $this->redirectToRoute('app_login');
        }

        $notif = $em->getRepository(Notificacion::class)->find($id);

        if (!$notif || $notif->getLeido() || $notif->getUsuario() !== $usuarioLogueado) {
            $this->addFlash('error', 'Notificación no válida.');
            return $this->redirectToRoute('ctrl_notificaciones');
        }

        $viaje = $em->getRepository(Viaje::class)->find($idViaje);
        
        if (!$viaje && $accion === 'aceptar') {
            $this->addFlash('error', 'El viaje especificado no existe.');
            return $this->redirectToRoute('ctrl_notificaciones');
        }

        if ($accion === 'aceptar') {
            $participante = new Participante();
            $participante->setViaje($viaje);
            
            $participante->setUsuario($usuarioLogueado); 

            $em->persist($participante);
            $this->addFlash('mensaje', '¡Solicitud aceptada correctamente!');
        } else {
            $this->addFlash('mensaje', 'Solicitud rechazada.');
        }
        
        $em->remove($notif);
        $em->flush();

        return $this->redirectToRoute('ctrl_notificaciones');
    }

   
}