<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity]
#[ORM\Table(name: 'usuario')]
class Usuario implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', name: 'idUsuario')]
    private $idUsuario;

    #[ORM\Column(type: 'string', length: 100, name: 'email')]
    private $email;

    #[ORM\Column(type: 'string', length: 255, name: 'clave')]
    private $clave;

    #[ORM\Column(type: 'string', length: 100, name: 'nombreUsuario')]
    private $nombreUsuario;

    #[ORM\Column(type: 'string', length: 255, name: 'fotoPerfil', nullable: true)]
    private $fotoPerfil;

    #[ORM\Column(type: 'date', name: 'fechaNacimiento', nullable: true)]
    private $fechaNacimiento;

    #[ORM\Column(type: 'string', length: 100, name: 'ciudad', nullable: true)]
    private $ciudad;

    #[ORM\Column(type: 'text', name: 'biografia', nullable: true)]
    private $biografia;

    #[ORM\Column(type: 'string', columnDefinition: "ENUM('usuario','admin')", nullable: true, options: ['default' => 'usuario'], name: 'rol')]
    private $rol;

    #[ORM\Column(type: 'string', length: 255, name: 'token', nullable: true)]
    private $token;

    #[ORM\Column(type: 'datetime', name: 'token_expiracion', nullable: true)]
    private $tokenExpiracion;

    #[ORM\Column(type: 'boolean', name: 'activo', nullable: true, options: ['default' => 0])]
    private $activo;

    #[ORM\Column(type: 'datetime', name: 'fechaRegistro', nullable: true)]
    private $fechaRegistro;

    /* ID */
    public function getIdUsuario()
    {
        return $this->idUsuario;
    }

    /* CORREO */
    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    /* CLAVE */
    public function getClave()
    {
        return $this->clave;
    }

    public function setClave($clave)
    {
        $this->clave = $clave;
    }

    /* NOMBRE USUARIO */
    public function getNombreUsuario()
    {
        return $this->nombreUsuario;
    }

    public function setNombreUsuario($nombreUsuario)
    {
        $this->nombreUsuario = $nombreUsuario;
    }

    /* FOTO PERFIL */
    public function getFotoPerfil()
    {
        return $this->fotoPerfil;
    }

    public function setFotoPerfil($fotoPerfil)
    {
        $this->fotoPerfil = $fotoPerfil;
    }

    /* FECHA NACIMIENTO */
    public function getFechaNacimiento()
    {
        return $this->fechaNacimiento;
    }

    public function setFechaNacimiento($fechaNacimiento)
    {
        $this->fechaNacimiento = $fechaNacimiento;
    }

    /* CIUDAD */
    public function getCiudad()
    {
        return $this->ciudad;
    }

    public function setCiudad($ciudad)
    {
        $this->ciudad = $ciudad;
    }

    /* BIOGRAFIA */
    public function getBiografia()
    {
        return $this->biografia;
    }

    public function setBiografia($biografia)
    {
        $this->biografia = $biografia;
    }

    /* ROL */
    public function getRol()
    {
        return $this->rol;
    }

    public function setRol($rol)
    {
        $this->rol = $rol;
    }

    /* TOKEN */
    public function getToken()
    {
        return $this->token;
    }

    public function setToken($token)
    {
        $this->token = $token;
    }

    /* TOKEN EXPIRACION */
    public function getTokenExpiracion()
    {
        return $this->tokenExpiracion;
    }

    public function setTokenExpiracion($tokenExpiracion)
    {
        $this->tokenExpiracion = $tokenExpiracion;
    }

    /* ACTIVO */
    public function getActivo()
    {
        return $this->activo;
    }

    public function setActivo($activo)
    {
        $this->activo = $activo;
    }

    /* FECHA REGISTRO */
    public function getFechaRegistro()
    {
        return $this->fechaRegistro;
    }

    public function setFechaRegistro($fechaRegistro)
    {
        $this->fechaRegistro = $fechaRegistro;
    }


    
    public function getRoles(): array
    {
        if ($this->getRol() == "admin")
            return ['ROLE_ADMIN'];
        else
		return ['ROLE_USER']; 
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

}
