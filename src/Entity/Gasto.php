<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

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

    #[ORM\Column(name: "descripcion", type: "string", length: 255, nullable: true)]
    private ?string $descripcion = null;

    // En PHP usamos float para que coincida con tu DOUBLE de la DB y acepte céntimos
    #[ORM\Column(name: "importeTotal", type: "float")]
    private ?float $importeTotal = null;

    #[ORM\Column(name: "fecha", type: "date")]
    private ?\DateTimeInterface $fecha = null;

    public function __construct()
    {
        // Al crear un "new Gasto()" se pone la fecha de hoy automáticamente
        $this->fecha = new \DateTime();
    }

    // --- Getters y Setters ---

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

    public function setDescripcion(?string $descripcion): self
    {
        $this->descripcion = $descripcion;
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

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;
        return $this;
    }
}