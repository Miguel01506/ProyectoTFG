<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity]
#[ORM\Table(name: "postmedia")]
class PostMedia
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Post::class, inversedBy: "postMedia")]
    #[ORM\JoinColumn(name: "idPost", referencedColumnName: "idPost")]
    private Post $post;

    #[ORM\ManyToOne(targetEntity: Media::class)]
    #[ORM\JoinColumn(name: "idMedia", referencedColumnName: "idMedia")]
    private Media $media;
}