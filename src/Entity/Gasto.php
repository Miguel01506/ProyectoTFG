<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Gasto
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "idGasto")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Viaje::class, inversedBy: "gastos")]
    #[ORM\JoinColumn(name: "idViaje", referencedColumnName: "idViaje")]
    private Viaje $viaje;

    #[ORM\ManyToOne(targetEntity: Usuario::class)]
    #[ORM\JoinColumn(name: "idUsuarioPagador", referencedColumnName: "idUsuario")]
    private Usuario $pagador;

    #[ORM\Column(nullable: true)]
    private ?string $descripcion = null;

    #[ORM\Column]
    private float $importeTotal;

    #[ORM\Column(type: "date")]
    private \DateTimeInterface $fecha;

}