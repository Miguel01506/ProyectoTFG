<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Deuda
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "idDeuda")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Usuario::class)]
    #[ORM\JoinColumn(name: "idUsuarioDeudor", referencedColumnName: "idUsuario")]
    private Usuario $deudor;

    #[ORM\ManyToOne(targetEntity: Usuario::class)]
    #[ORM\JoinColumn(name: "idUsuarioAcreedor", referencedColumnName: "idUsuario")]
    private Usuario $acreedor;

    #[ORM\Column(name: "importe")]
    private float $importe;

}