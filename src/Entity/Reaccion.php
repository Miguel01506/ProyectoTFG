<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Reaccion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "idReaccion")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Post::class, inversedBy: "reacciones")]
    #[ORM\JoinColumn(name: "idPost", referencedColumnName: "idPost")]
    private Post $post;

    #[ORM\ManyToOne(targetEntity: Usuario::class, inversedBy: "reacciones")]
    #[ORM\JoinColumn(name: "idUsuario", referencedColumnName: "idUsuario")]
    private Usuario $usuario;

    #[ORM\Column(length: 20)]
    private string $tipo;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $fechaReaccion;

    
}