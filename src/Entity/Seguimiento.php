<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Seguimiento
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "idSeguimiento")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Usuario::class)]
    #[ORM\JoinColumn(name: "idSeguidor", referencedColumnName: "idUsuario")]
    private Usuario $seguidor;

    #[ORM\ManyToOne(targetEntity: Usuario::class)]
    #[ORM\JoinColumn(name: "idSeguido", referencedColumnName: "idUsuario")]
    private Usuario $seguido;
}