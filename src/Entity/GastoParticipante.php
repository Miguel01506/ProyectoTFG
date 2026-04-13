<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "gastoparticipante")]
class GastoParticipante
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id", type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Gasto::class, inversedBy: "participantes")]
    #[ORM\JoinColumn(name: "idGasto", referencedColumnName: "idGasto", nullable: false, onDelete: "CASCADE")]
    private ?Gasto $gasto = null;

    #[ORM\ManyToOne(targetEntity: Usuario::class)]
    #[ORM\JoinColumn(name: "idUsuario", referencedColumnName: "idUsuario", nullable: false)]
    private ?Usuario $usuario = null;

    #[ORM\Column(name: "importeIndividual", type: "float")]
    private ?float $importeIndividual = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGasto(): ?Gasto
    {
        return $this->gasto;
    }

    public function setGasto(?Gasto $gasto): self
    {
        $this->gasto = $gasto;
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

    public function getImporteIndividual(): ?float
    {
        return $this->importeIndividual;
    }

    public function setImporteIndividual(float $importeIndividual): self
    {
        $this->importeIndividual = $importeIndividual;
        return $this;
    }
}