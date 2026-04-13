<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "album")]
class Album
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "idAlbum", type: "integer")]
    private ?int $id = null;

    #[ORM\Column(name: "nombre", type: "string", length: 100)]
    private ?string $nombre = null;

    #[ORM\OneToOne(targetEntity: Viaje::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: "idViaje", referencedColumnName: "idViaje", nullable: false)]
    private ?Viaje $viaje = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getViaje(): ?Viaje
    {
        return $this->viaje;
    }

    public function setViaje(Viaje $viaje): self
    {
        $this->viaje = $viaje;
        return $this;
    }
}