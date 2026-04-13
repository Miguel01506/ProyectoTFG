<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "album")]
class Album
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "idAlbum")]
    private ?int $id = null;

    #[ORM\OneToOne(targetEntity: Viaje::class)]
    #[ORM\JoinColumn(name: "idViaje", referencedColumnName: "idViaje")]
    private Viaje $viaje;

    #[ORM\Column(name: "nombre")]
    private string $nombre;

}