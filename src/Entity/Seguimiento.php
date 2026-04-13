<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "seguimientos")]
class Seguimiento
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Usuario::class, inversedBy: "siguiendo")]
    #[ORM\JoinColumn(name:"idSeguidor", referencedColumnName:"idUsuario", nullable:false)]
    private $seguidor;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Usuario::class, inversedBy: "seguidores")]
    #[ORM\JoinColumn(name:"idSeguido", referencedColumnName:"idUsuario", nullable:false)]
    private $seguido;

    #[ORM\Column(type:"string", length:20, name:"estado", nullable:true, options:["default"=>"pendiente"])]
    private $estado;

    #[ORM\Column(type:"datetime", name:"fechaSolicitud", nullable:true)]
    private $fechaSolicitud;

    /* SEGUIDOR */
    public function getSeguidor(){
        return $this->seguidor;
    }

    public function setSeguidor($seguidor){
        $this->seguidor = $seguidor;
    }

    /* SEGUIDO */
    public function getSeguido(){
        return $this->seguido;
    }

    public function setSeguido($seguido){
        $this->seguido = $seguido;
    }

    /* ESTADO */
    public function getEstado(){
        return $this->estado;
    }

    public function setEstado($estado){
        $this->estado = $estado;
    }

    /* FECHA SOLICITUD */
    public function getFechaSolicitud(){
        return $this->fechaSolicitud;
    }

    public function setFechaSolicitud($fechaSolicitud){
        $this->fechaSolicitud = $fechaSolicitud;
    }
}
