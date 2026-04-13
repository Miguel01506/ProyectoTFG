<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "media")]
class Media
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "idMedia", type: "integer")]
    private ?int $id = null;

    #[ORM\Column(name: "tipo", type: "string", length: 10)]
    private ?string $tipo = null;

    #[ORM\Column(name: "rutaArchivo", type: "string", length: 255)]
    private ?string $rutaArchivo = null;

    #[ORM\Column(name: "descripcion", type: "string", length: 255, nullable: true)]
    private ?string $descripcion = null;

    #[ORM\Column(name: "fechaSubida", type: "datetime")]
    private ?\DateTimeInterface $fechaSubida = null;

    #[ORM\ManyToOne(targetEntity: Usuario::class)]
    #[ORM\JoinColumn(name: "idUsuario", referencedColumnName: "idUsuario", nullable: false)]
    private ?Usuario $usuario = null;

    public function __construct()
    {
        $this->fechaSubida = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getRutaArchivo(): ?string
    {
        return $this->rutaArchivo;
    }

    public function setRutaArchivo(string $rutaArchivo): self
    {
        $this->rutaArchivo = $rutaArchivo;
        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): self
    {
        $this->descripcion = $descripcion;
        return $this;
    }

    public function getFechaSubida(): ?\DateTimeInterface
    {
        return $this->fechaSubida;
    }

    public function setFechaSubida(\DateTimeInterface $fechaSubida): self
    {
        $this->fechaSubida = $fechaSubida;
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
}