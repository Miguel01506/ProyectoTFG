<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "postmencion")]
class PostMencion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Post::class, inversedBy: "menciones")]
    #[ORM\JoinColumn(name: "idPost", referencedColumnName: "idPost")]
    private Post $post;

    #[ORM\ManyToOne(targetEntity: Usuario::class)]
    #[ORM\JoinColumn(name: "idUsuario", referencedColumnName: "idUsuario")]
    private Usuario $usuario;
}