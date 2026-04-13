<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "albummedia")]
class AlbumMedia
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id", type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Album::class, inversedBy: "albumMedia")]
    #[ORM\JoinColumn(name: "idAlbum", referencedColumnName: "idAlbum", nullable: false, onDelete: "CASCADE")]
    private ?Album $album = null;

    #[ORM\ManyToOne(targetEntity: Media::class)]
    #[ORM\JoinColumn(name: "idMedia", referencedColumnName: "idMedia", nullable: false, onDelete: "CASCADE")]
    private ?Media $media = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAlbum(): ?Album
    {
        return $this->album;
    }

    public function setAlbum(?Album $album): self
    {
        $this->album = $album;
        return $this;
    }

    public function getMedia(): ?Media
    {
        return $this->media;
    }

    public function setMedia(?Media $media): self
    {
        $this->media = $media;
        return $this;
    }
}