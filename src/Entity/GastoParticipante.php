<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class GastoParticipante
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Gasto::class, inversedBy: "participantes")]
    #[ORM\JoinColumn(name: "idGasto", referencedColumnName: "idGasto")]
    private Gasto $gasto;

    #[ORM\ManyToOne(targetEntity: Usuario::class)]
    #[ORM\JoinColumn(name: "idUsuario", referencedColumnName: "idUsuario")]
    private Usuario $usuario;

    #[ORM\Column]
    private float $importeIndividual;
}