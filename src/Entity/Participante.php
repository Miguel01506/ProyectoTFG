<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "participante")]
class Participante
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "idParticipante")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Usuario::class, inversedBy: "participaciones")]
    #[ORM\JoinColumn(name: "idUsuario", referencedColumnName: "idUsuario")]
    private Usuario $usuario;

    #[ORM\ManyToOne(targetEntity: Viaje::class, inversedBy: "participantes")]
    #[ORM\JoinColumn(name: "idViaje", referencedColumnName: "idViaje")]
    private Viaje $viaje;
}