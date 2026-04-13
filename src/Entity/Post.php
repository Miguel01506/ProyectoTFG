<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
#[ORM\Table(name: "post")]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "idPost", type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Viaje::class, inversedBy: "posts")]
    #[ORM\JoinColumn(name: "idViaje", referencedColumnName: "idViaje", nullable: false)]
    private ?Viaje $viaje = null;

    #[ORM\ManyToOne(targetEntity: Usuario::class)]
    #[ORM\JoinColumn(name: "idUsuarioAutor", referencedColumnName: "idUsuario", nullable: false)]
    private ?Usuario $autor = null;

    #[ORM\Column(name: "texto", type: "text", nullable: true)]
    private ?string $texto = null;

    #[ORM\Column(name: "fechaPublicacion", type: "datetime")]
    private ?\DateTimeInterface $fechaPublicacion = null;

    #[ORM\OneToMany(mappedBy: "post", targetEntity: Comentario::class)]
    private Collection $comentarios;

    #[ORM\OneToMany(mappedBy: "post", targetEntity: PostMedia::class)]
    private Collection $postMedia;

    #[ORM\OneToMany(mappedBy: "post", targetEntity: PostMencion::class)]
    private Collection $menciones;

    #[ORM\OneToMany(mappedBy: "post", targetEntity: Reaccion::class)]
    private Collection $reacciones;

    public function __construct()
    {
        $this->comentarios = new ArrayCollection();
        $this->postMedia = new ArrayCollection();
        $this->menciones = new ArrayCollection();
        $this->reacciones = new ArrayCollection();
        $this->fechaPublicacion = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getViaje(): ?Viaje
    {
        return $this->viaje;
    }

    public function setViaje(?Viaje $viaje): self
    {
        $this->viaje = $viaje;
        return $this;
    }

    public function getAutor(): ?Usuario
    {
        return $this->autor;
    }

    public function setAutor(?Usuario $autor): self
    {
        $this->autor = $autor;
        return $this;
    }

    public function getTexto(): ?string
    {
        return $this->texto;
    }

    public function setTexto(?string $texto): self
    {
        $this->texto = $texto;
        return $this;
    }

    public function getFechaPublicacion(): ?\DateTimeInterface
    {
        return $this->fechaPublicacion;
    }

    public function setFechaPublicacion(\DateTimeInterface $fechaPublicacion): self
    {
        $this->fechaPublicacion = $fechaPublicacion;
        return $this;
    }

    public function getComentarios(): Collection
    {
        return $this->comentarios;
    }

    public function getPostMedia(): Collection
    {
        return $this->postMedia;
    }

    public function getMenciones(): Collection
    {
        return $this->menciones;
    }

    public function getReacciones(): Collection
    {
        return $this->reacciones;
    }
}