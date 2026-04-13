<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "reaccion")]
class Reaccion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "idReaccion", type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Post::class, inversedBy: "reacciones")]
    #[ORM\JoinColumn(name: "idPost", referencedColumnName: "idPost", nullable: false)]
    private ?Post $post = null;

    #[ORM\ManyToOne(targetEntity: Usuario::class, inversedBy: "reacciones")]
    #[ORM\JoinColumn(name: "idUsuario", referencedColumnName: "idUsuario", nullable: false)]
    private ?Usuario $usuario = null;

    #[ORM\Column(name: "tipo", type: "string", length: 20)]
    private ?string $tipo = null;

    #[ORM\Column(name: "fechaReaccion", type: "datetime")]
    private ?\DateTimeInterface $fechaReaccion = null;

    public function __construct()
    {
        $this->fechaReaccion = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self
    {
        $this->post = $post;
        return $this;
    }

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuario $usuario): self
    {
        $this->usuario = $usuario;
        return $this;
    }

    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(string $tipo): self
    {
        $this->tipo = $tipo;
        return $this;
    }

    public function getFechaReaccion(): ?\DateTimeInterface
    {
        return $this->fechaReaccion;
    }

    public function setFechaReaccion(\DateTimeInterface $fechaReaccion): self
    {
        $this->fechaReaccion = $fechaReaccion;
        return $this;
    }
}