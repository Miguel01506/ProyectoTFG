<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "media")]
class Media
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "idMedia")]
    private ?int $id = null;

    #[ORM\Column(length: 10)]
    private string $tipo;

    #[ORM\Column(name: "rutaArchivo", length: 255)]
    private string $rutaArchivo;

    #[ORM\Column(nullable: true)]
    private ?string $descripcion = null;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $fechaSubida;

    #[ORM\ManyToOne(targetEntity: Usuario::class)]
    #[ORM\JoinColumn(name: "idUsuario", referencedColumnName: "idUsuario")]
    private Usuario $usuario;
}