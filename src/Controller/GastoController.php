<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Viaje;
use App\Entity\Gasto;
use App\Entity\Participante;

class GastoController extends AbstractController
{
    #[Route(path: '/gastosViaje/{id}', name: 'ctrl_gastos_viaje')]
    public function gastosViaje(int $id, EntityManagerInterface $em)
    {
        $viaje = $em->getRepository(Viaje::class)->find($id);

        if (!$viaje) {
            return $this->redirectToRoute('ctrl_viajes');
        }

        $gastos = $em->getRepository(Gasto::class)->findBy(['viaje' => $viaje]);
        $participantes = $em->getRepository(Participante::class)->findBy(['viaje' => $viaje]);

        $totalGastos = 0;
        foreach ($gastos as $gasto) {
            $totalGastos += $gasto->getImporteTotal(); 
        }

        $numParticipantes = count($participantes);
        $mediaPorPersona = $numParticipantes > 0 ? $totalGastos / $numParticipantes : 0;

        $balances = [];
        foreach ($participantes as $p) {
            $nombre = $p->getUsuario()->getNombreUsuario();
            $pagadoPorUsuario = 0;

            foreach ($gastos as $gasto) {
                if ($gasto->getPagador()->getIdUsuario() === $p->getUsuario()->getIdUsuario()) {
                    $pagadoPorUsuario += $gasto->getImporteTotal();
                }
            }

            $balances[$nombre] = $pagadoPorUsuario - $mediaPorPersona;
        }

        return $this->render('gastosviaje.html.twig', [
            'viaje' => $viaje,
            'gastos' => $gastos,
            'totalGastos' => $totalGastos,
            'mediaPorPersona' => $mediaPorPersona,
            'balances' => $balances
        ]);
    }

    #[Route(path: '/viaje/{id}/nuevo-gasto', name: 'ctrl_nuevo_gasto', methods: ['POST'])]
    public function nuevoGasto(int $id, Request $request, EntityManagerInterface $em)
    {
        $viaje = $em->getRepository(Viaje::class)->find($id);

        if (!$viaje) {
            return $this->redirectToRoute('ctrl_viajes');
        }

        $descripcion = $request->request->get('descripcion');
        $importeTotal = $request->request->get('importeTotal');

        if ($descripcion && $importeTotal) {
            $gasto = new Gasto();
            $gasto->setViaje($viaje);
            $gasto->setPagador($this->getUser()); 
            $gasto->setDescripcion($descripcion);
            $gasto->setImporteTotal((float) $importeTotal);

            $em->persist($gasto);
            $em->flush();
        }

        return $this->redirectToRoute('ctrl_gastos_viaje', ['id' => $id]);
    }
}