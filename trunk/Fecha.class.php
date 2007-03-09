<?php
require_once( "configuracion.php" );

class Fecha {
	private $dia;
	private $mes;
	private $a�o;

	/**
	 * Par�metros: la fecha que deseo usar para aplicar las operaciones
	 */
	public function __construct($dia="", $mes="", $a�o=""){
		$dia =="" ? $this->dia = date(j) : $this->dia = $dia;
		$mes =="" ? $this->dia = date(n) : $this->mes = $mes;
		$a�o =="" ? $this->dia = date(Y) : $this->a�o = $a�o;
	}

	/* getter/setter */
	public function setDia($dia){$this->dia = $dia;}
	public function getDia(){return $this->dia;}

	public function setMes($mes){$this->mes = $mes;}
	public function getMes(){return $this->mes;}

	public function setA�o($a�o){$this->a�o = $a�o;}
	public function getA�o(){return $this->a�o;}

	/**
	 * Retorna: la fecha de "ahora" (now)
	 */
	 public function ahora(){
	 	return date(j)."/".date(n)."/".date(Y);
	 }
	/**
	 * Recibe:  fecha de nacimiento: dia, mes, a�o
	 * Retorna: un n�mero entero con la edad
	 * Soluci�n basada en Wikibooks.org sobre PHP
	 * Ejemplo de uso:
	 *
	 * 	$unaF = new Fecha();
	 * 	echo $unaF->calcularEdad(5, 8, 1973); // retorna 33
	 *
	 */
    public function calcularEdad($diaNacimiento=null, $mesNacimiento=null, $a�oNacimiento=null){

		// Validaci�n de par�metros
		if($diaNacimiento == null || $mesNacimiento == null || $a�oNacimiento ==null ){
				die(__FILE__.": calcularEdad : la fecha no puede ser vac�a ");
		}

		list($dia, $mes, $a�o) = explode("/", $this->ahora());

		// si el mes es el mismo pero el dia inferior aun
		// no ha cumplido a�os, le quitaremos un a�o al actual
		if (($mesNacimiento == $mes) && ($diaNacimiento > $dia)) {
			$a�o = ( $a�oNacimiento - 1 );
		}
		//	si el mes es superior al actual tampoco habra
		// cumplido a�os, por eso le quitamos un a�o al actual

		if ($mesNacimiento > $mes) {
			$a�o = ( $a�oNacimiento - 1 );
		}
		//	ya no habria mas condiciones, ahora simplemente
		// restamos los a�os y mostramos el resultado como su edad
		$edad = ($a�o - $a�oNacimiento);

		return $edad;
	}

}

?>