<?php
require_once( "configuracion.php" );
require_once( "Persona.class.php" );

class Usuario extends Persona{
	private $id;
	private $clave;
	private $descripcion;
	private $ingreso;

    public function getId(){return $this->id;}
    public function getIngreso(){return $this->ingreso;}

    public function __construct($id, $nombre, $descripcion, $ingreso){
    	$this->id = $id;
		parent::__construct($nombre);
    	$this->descripcion = $descripcion;
    	$this->ingreso = $ingreso;

    }
    public function __toString(){
    	return $this->nombre." ".$this->descripcion." ".$this->ingreso;
    }
}
?>