<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "albummedia")]
class AlbumMedia
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Album::class, inversedBy: "albumMedia")]
    #[ORM\JoinColumn(name: "idAlbum", referencedColumnName: "idAlbum")]
    private Album $album;

    #[ORM\ManyToOne(targetEntity: Media::class)]
    #[ORM\JoinColumn(name: "idMedia", referencedColumnName: "idMedia")]
    private Media $media;
}