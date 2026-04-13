<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "post")]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "idPost")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Viaje::class, inversedBy: "posts")]
    #[ORM\JoinColumn(name: "idViaje", referencedColumnName: "idViaje")]
    private Viaje $viaje;

    #[ORM\ManyToOne(targetEntity: Usuario::class)]
    #[ORM\JoinColumn(name: "idUsuarioAutor", referencedColumnName: "idUsuario")]
    private Usuario $autor;

    #[ORM\Column(nullable: true)]
    private ?string $texto = null;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $fechaPublicacion;

    
}