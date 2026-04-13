<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
#[ORM\Table(name: "viaje")]
class Viaje
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "idViaje", type: "integer")]
    private ?int $idViaje = null;

    #[ORM\Column(name: "nombre", type: "string", length: 100)]
    private ?string $nombre = null;

    #[ORM\Column(name: "destino", type: "string", length: 100)]
    private ?string $destino = null;

    #[ORM\Column(name: "fechaInicio", type: "date", nullable: true)]
    private ?\DateTimeInterface $fechaInicio = null;

    #[ORM\Column(name: "fechaFin", type: "date", nullable: true)]
    private ?\DateTimeInterface $fechaFin = null;

    public function getIdViaje(): ?int
    {
        return $this->idViaje;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;
        return $this;
    }

    public function getDestino(): ?string
    {
        return $this->destino;
    }

    public function setDestino(string $destino): self
    {
        $this->destino = $destino;
        return $this;
    }

    public function getFechaInicio(): ?\DateTimeInterface
    {
        return $this->fechaInicio;
    }

    public function setFechaInicio(?\DateTimeInterface $fechaInicio): self
    {
        $this->fechaInicio = $fechaInicio;
        return $this;
    }

    public function getFechaFin(): ?\DateTimeInterface
    {
        return $this->fechaFin;
    }

    public function setFechaFin(?\DateTimeInterface $fechaFin): self
    {
        $this->fechaFin = $fechaFin;
        return $this;
    }
}