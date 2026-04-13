<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "usuario")]
class Usuario
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "idUsuario", type: "integer")]
    private ?int $idUsuario = null;

    #[ORM\Column(type: "string", length: 100)]
    private string $nombre;

    #[ORM\Column(type: "string", length: 150, unique: true)]
    private string $email;

    #[ORM\Column(type: "string", length: 255)]
    private string $contrasena;
}