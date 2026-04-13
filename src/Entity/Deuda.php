<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "deuda")]
class Deuda
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "idDeuda", type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Usuario::class)]
    #[ORM\JoinColumn(name: "idUsuarioDeudor", referencedColumnName: "idUsuario", nullable: false)]
    private ?Usuario $deudor = null;

    #[ORM\ManyToOne(targetEntity: Usuario::class)]
    #[ORM\JoinColumn(name: "idUsuarioAcreedor", referencedColumnName: "idUsuario", nullable: false)]
    private ?Usuario $acreedor = null;

    #[ORM\Column(name: "importe", type: "float")]
    private ?float $importe = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDeudor(): ?Usuario
    {
        return $this->deudor;
    }

    public function setDeudor(?Usuario $deudor): self
    {
        $this->deudor = $deudor;
        return $this;
    }

    public function getAcreedor(): ?Usuario
    {
        return $this->acreedor;
    }

    public function setAcreedor(?Usuario $acreedor): self
    {
        $this->acreedor = $acreedor;
        return $this;
    }

    public function getImporte(): ?float
    {
        return $this->importe;
    }

    public function setImporte(float $importe): self
    {
        $this->importe = $importe;
        return $this;
    }

}