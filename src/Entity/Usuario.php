<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity]
#[ORM\Table(name: 'usuario')]
class Usuario implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'idUsuario', type: 'integer')]
    private ?int $idUsuario = null;

    #[ORM\Column(name: 'email', type: 'string', length: 100, unique: true)]
    private ?string $email = null;

    #[ORM\Column(name: 'clave', type: 'string', length: 255)]
    private ?string $clave = null;

    #[ORM\Column(name: 'nombreUsuario', type: 'string', length: 100)]
    private ?string $nombreUsuario = null;

    #[ORM\Column(name: 'fotoPerfil', type: 'string', length: 255, nullable: true)]
    private ?string $fotoPerfil = null;

    #[ORM\Column(name: 'fechaNacimiento', type: 'date', nullable: true)]
    private ?\DateTimeInterface $fechaNacimiento = null;

    #[ORM\Column(name: 'ciudad', type: 'string', length: 100, nullable: true)]
    private ?string $ciudad = null;

    #[ORM\Column(name: 'biografia', type: 'text', nullable: true)]
    private ?string $biografia = null;

    #[ORM\Column(name: 'rol', type: 'string', columnDefinition: "ENUM('usuario','admin')", nullable: true, options: ['default' => 'usuario'])]
    private ?string $rol = null;

    #[ORM\Column(name: 'token', type: 'string', length: 255, nullable: true)]
    private ?string $token = null;

    #[ORM\Column(name: 'tokenExpiracion', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $tokenExpiracion = null;

    #[ORM\Column(name: 'activo', type: 'boolean', nullable: true, options: ['default' => 0])]
    private ?bool $activo = null;

    #[ORM\Column(name: 'fechaRegistro', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $fechaRegistro = null;

    #[ORM\OneToMany(mappedBy: 'usuario', targetEntity: Comentario::class)]
    private Collection $comentarios;

    #[ORM\OneToMany(mappedBy: 'usuario', targetEntity: Participante::class)]
    private Collection $participaciones;

    #[ORM\OneToMany(mappedBy: 'usuario', targetEntity: Reaccion::class)]
    private Collection $reacciones;

    #[ORM\OneToMany(mappedBy: 'seguidor', targetEntity: Seguimiento::class)]
    private Collection $siguiendo;

    #[ORM\OneToMany(mappedBy: 'seguido', targetEntity: Seguimiento::class)]
    private Collection $seguidores;

    public function __construct()
    {
        $this->comentarios = new ArrayCollection();
        $this->participaciones = new ArrayCollection();
        $this->reacciones = new ArrayCollection();
        $this->siguiendo = new ArrayCollection();
        $this->seguidores = new ArrayCollection();
        $this->fechaRegistro = new \DateTime();
    }

    public function getIdUsuario(): ?int
    {
        return $this->idUsuario;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getClave(): ?string
    {
        return $this->clave;
    }

    public function setClave(string $clave): self
    {
        $this->clave = $clave;
        return $this;
    }

    public function getNombreUsuario(): ?string
    {
        return $this->nombreUsuario;
    }

    public function setNombreUsuario(string $nombreUsuario): self
    {
        $this->nombreUsuario = $nombreUsuario;
        return $this;
    }

    public function getFotoPerfil(): ?string
    {
        return $this->fotoPerfil;
    }

    public function setFotoPerfil(?string $fotoPerfil): self
    {
        $this->fotoPerfil = $fotoPerfil;
        return $this;
    }

    public function getFechaNacimiento(): ?\DateTimeInterface
    {
        return $this->fechaNacimiento;
    }

    public function setFechaNacimiento(?\DateTimeInterface $fechaNacimiento): self
    {
        $this->fechaNacimiento = $fechaNacimiento;
        return $this;
    }

    public function getCiudad(): ?string
    {
        return $this->ciudad;
    }

    public function setCiudad(?string $ciudad): self
    {
        $this->ciudad = $ciudad;
        return $this;
    }

    public function getBiografia(): ?string
    {
        return $this->biografia;
    }

    public function setBiografia(?string $biografia): self
    {
        $this->biografia = $biografia;
        return $this;
    }

    public function getRol(): ?string
    {
        return $this->rol;
    }

    public function setRol(?string $rol): self
    {
        $this->rol = $rol;
        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;
        return $this;
    }

    public function getTokenExpiracion(): ?\DateTimeInterface
    {
        return $this->tokenExpiracion;
    }

    public function setTokenExpiracion(?\DateTimeInterface $tokenExpiracion): self
    {
        $this->tokenExpiracion = $tokenExpiracion;
        return $this;
    }

    public function getActivo(): ?bool
    {
        return $this->activo;
    }

    public function setActivo(?bool $activo): self
    {
        $this->activo = $activo;
        return $this;
    }

    public function getFechaRegistro(): ?\DateTimeInterface
    {
        return $this->fechaRegistro;
    }

    public function setFechaRegistro(?\DateTimeInterface $fechaRegistro): self
    {
        $this->fechaRegistro = $fechaRegistro;
        return $this;
    }

    public function getRoles(): array
    {
        $roles = [];
        if ($this->rol === 'admin') {
            $roles[] = 'ROLE_ADMIN';
        }
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getPassword(): string
    {
        return (string) $this->clave;
    }

    public function eraseCredentials(): void
    {
    }

    public function getComentarios(): Collection
    {
        return $this->comentarios;
    }

    public function getParticipaciones(): Collection
    {
        return $this->participaciones;
    }

    public function getReacciones(): Collection
    {
        return $this->reacciones;
    }

    public function getSiguiendo(): Collection
    {
        return $this->siguiendo;
    }

    public function getSeguidores(): Collection
    {
        return $this->seguidores;
    }
}