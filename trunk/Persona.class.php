<?php
/**
 * La clase Persona contempla la información básica de cualquier persona real.
 * Forma de uso:
 * $unaPer = new Persona("nombre de la persona");
 * Todos los atributos tiene su setter y getter respectivo.
 * El atributo fechaNacimiento debe ser un objeto Fecha:
 * $unaPer->setFechaNacimiento( new Fecha(30,10,2000) ); ver metodo edad()
 * El atributo telefonos es un array y para agregar telefonos esta el metodo addTelefono:
 * $unaPer->addTelefono("Casa","900-00-00");
 * $unaPer->addTelefono("Celular","099-999 999");
 *
 */
require_once( "configuracion.php" );
require_once( "Fecha.class.php" );  // se usa la clase Fecha del Framework para el calculo de la edad.

class Persona {
    private $nombre="";
    private $apellido="";
    private $fechaNacimiento;  // objeto de tipo Fecha
    private $sexo="";
    private $pais="";
    private $ciudad="";
    private $idioma="";     // lengua materna de la persona
    private $direccion="";
    private $codigoPostal="";
    private $telefonos=array();  // array de telefonos con el formato (tipo,nro)
    private $email="";

    public function __construct($nomb){
         $this->nombre=$nomb;
    }

    public function __toString(){
         return $this->nombre." ".$this->apellido;
    }

    /* setter y getter    */
    public function setNombre($n){$this->nombre=$n;}
    public function getNombre(){return $this->nombre;}

    public function setApellido($ap){$this->apellido=$ap;}
    public function getApellido(){return $this->apellido;}

    public function setFechaNacimiento(Fecha $x){$this->fechaNacimiento=$x;}
    public function getFechaNacimiento(){return $this->fechaNacimiento;}

    public function setSexo($x){$this->sexo=$x;}
    public function getSexo(){return $this->sexo;}

    public function setPais($x){$this->pais=$x;}
    public function getPais(){return $this->pais;}

    public function setCiudad($x){$this->ciudad=$x;}
    public function getCiudad(){return $this->ciudad;}

    public function setIdioma($x){$this->idioma=$x;}
    public function getIdioma(){return $this->idioma;}

    public function setDireccion($x){$this->direccion=$x;}
    public function getDireccion(){return $this->direccion;}

    public function setCodigoPostal($x){$this->codigoPostal=$x;}
    public function getCodigoPostal(){return $this->codigoPostal;}

    public function setEmail($x){$this->email=$x;}
    public function getEmail(){return $this->email;}

    public function addTelefono($nro, $tipo){
         $this->telefonos[] = array('tipo'=>$tipo,'nro'=>$nro);
    }
    public function getTelefonos(){return $this->telefonos;}

/**
 * Muestra la lista de telefonos separados por comas
 */
    public function mostrarTelefonos(){
           foreach ($this->telefonos as $item){
                 $lista_tel.= $item['tipo']."_".$item['nro'].", " ;
           }
           echo $lista_tel;
    }

/**
 * Calcula la Edad de la persona, una vez seteada la fecha de nacim.
 * Forma de uso:
 * $xPer = new Persona("Pepito");
 * $xPer->setFechaNacimiento( new Fecha(30,10,2000) );
 * echo $xPer->edad()
 */
    public function getEdad(){
         $fech = $this->getfechaNacimiento();
         return $fech->calcularEdad($fech->getdia(),$fech->getMes(),$fech->getAño());
    }

}

?>
