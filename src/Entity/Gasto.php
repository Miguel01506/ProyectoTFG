<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
#[ORM\Table(name: "gasto")]
class Gasto
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "idGasto", type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Viaje::class, inversedBy: "gastos")]
    #[ORM\JoinColumn(name: "idViaje", referencedColumnName: "idViaje", nullable: false)]
    private ?Viaje $viaje = null;

    #[ORM\ManyToOne(targetEntity: Usuario::class)]
    #[ORM\JoinColumn(name: "idUsuarioPagador", referencedColumnName: "idUsuario", nullable: false)]
    private ?Usuario $pagador = null;

    #[ORM\Column(name: "descripcion", type: "string", length: 100)]
    private ?string $descripcion = null;

    #[ORM\Column(name: "importeTotal", type: "float")]
    private ?float $importeTotal = null;

    #[ORM\Column(name: "fecha", type: "datetime")]
    private ?\DateTimeInterface $fechaGasto = null;

    #[ORM\OneToMany(mappedBy: "gasto", targetEntity: GastoParticipante::class)]
    private Collection $participantes;

    public function __construct()
    {
        $this->participantes = new ArrayCollection();
        $this->fechaGasto = new \DateTime();
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

    public function getPagador(): ?Usuario
    {
        return $this->pagador;
    }

    public function setPagador(?Usuario $pagador): self
    {
        $this->pagador = $pagador;
        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $concepto): self
    {
        $this->descripcion = $concepto;
        return $this;
    }

    public function getImporteTotal(): ?float
    {
        return $this->importeTotal;
    }

    public function setImporteTotal(float $importeTotal): self
    {
        $this->importeTotal = $importeTotal;
        return $this;
    }

    public function getFechaGasto(): ?\DateTimeInterface
    {
        return $this->fechaGasto;
    }

    public function setFechaGasto(\DateTimeInterface $fechaGasto): self
    {
        $this->fechaGasto = $fechaGasto;
        return $this;
    }

    public function getParticipantes(): Collection
    {
        return $this->participantes;
    }
}