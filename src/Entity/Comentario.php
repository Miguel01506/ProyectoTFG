<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Comentario
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "idComentario")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Post::class, inversedBy: "comentarios")]
    #[ORM\JoinColumn(name: "idPost", referencedColumnName: "idPost")]
    private Post $post;

    #[ORM\ManyToOne(targetEntity: Usuario::class, inversedBy: "comentarios")]
    #[ORM\JoinColumn(name: "idUsuario", referencedColumnName: "idUsuario")]
    private Usuario $usuario;

    #[ORM\Column(length: 255)]
    private string $texto;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $fechaComentario;
}