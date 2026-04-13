<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "comentario")]
class Comentario
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "idComentario")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Post::class, inversedBy: "comentarios")]
    #[ORM\JoinColumn(name: "idPost", referencedColumnName: "idPost", nullable: false, onDelete: "CASCADE")]
    private ?Post $post = null;

    #[ORM\ManyToOne(targetEntity: Usuario::class, inversedBy: "comentarios")]
    #[ORM\JoinColumn(name: "idUsuario", referencedColumnName: "idUsuario", nullable: false)]
    private ?Usuario $usuario = null;

    #[ORM\Column(type: "string", length: 255, name: "texto")]
    private ?string $texto = null;

    #[ORM\Column(name: "fechaComentario", type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    private ?\DateTimeInterface $fechaComentario = null;

    public function __construct()
    {
        $this->fechaComentario = new \DateTime();
    }

    // --- Getters y Setters ---

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

    public function getTexto(): ?string
    {
        return $this->texto;
    }

    public function setTexto(string $texto): self
    {
        $this->texto = $texto;
        return $this;
    }

    public function getFechaComentario(): ?\DateTimeInterface
    {
        return $this->fechaComentario;
    }

    public function setFechaComentario(\DateTimeInterface $fechaComentario): self
    {
        $this->fechaComentario = $fechaComentario;
        return $this;
    }
}