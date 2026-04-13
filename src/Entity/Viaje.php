<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "viaje")]
class Viaje
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "idViaje", type: "integer")]
    private ?int $idViaje = null;

    #[ORM\Column(type: "string", length: 100)]
    private string $nombre;

    #[ORM\Column(type: "string", length: 100)]
    private string $destino;

    #[ORM\Column(type: "date", nullable: true)]
    private ?\DateTimeInterface $fechaInicio = null;

    #[ORM\Column(type: "date", nullable: true)]
    private ?\DateTimeInterface $fechaFin = null;
}