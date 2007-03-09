<?php
require_once( "configuracion.php" );

class Fecha {
	private $dia;
	private $mes;
	private $año;

	/**
	 * Parámetros: la fecha que deseo usar para aplicar las operaciones
	 */
	public function __construct($dia="", $mes="", $año=""){
		$dia =="" ? $this->dia = date(j) : $this->dia = $dia;
		$mes =="" ? $this->dia = date(n) : $this->mes = $mes;
		$año =="" ? $this->dia = date(Y) : $this->año = $año;
	}

	/* getter/setter */
	public function setDia($dia){$this->dia = $dia;}
	public function getDia(){return $this->dia;}

	public function setMes($mes){$this->mes = $mes;}
	public function getMes(){return $this->mes;}

	public function setAño($año){$this->año = $año;}
	public function getAño(){return $this->año;}

	/**
	 * Retorna: la fecha de "ahora" (now)
	 */
	 public function ahora(){
	 	return date(j)."/".date(n)."/".date(Y);
	 }
	/**
	 * Recibe:  fecha de nacimiento: dia, mes, año
	 * Retorna: un número entero con la edad
	 * Solución basada en Wikibooks.org sobre PHP
	 * Ejemplo de uso:
	 *
	 * 	$unaF = new Fecha();
	 * 	echo $unaF->calcularEdad(5, 8, 1973); // retorna 33
	 *
	 */
    public function calcularEdad($diaNacimiento=null, $mesNacimiento=null, $añoNacimiento=null){

		// Validación de parámetros
		if($diaNacimiento == null || $mesNacimiento == null || $añoNacimiento ==null ){
				die(__FILE__.": calcularEdad : la fecha no puede ser vacía ");
		}

		list($dia, $mes, $año) = explode("/", $this->ahora());

		// si el mes es el mismo pero el dia inferior aun
		// no ha cumplido años, le quitaremos un año al actual
		if (($mesNacimiento == $mes) && ($diaNacimiento > $dia)) {
			$año = ( $añoNacimiento - 1 );
		}
		//	si el mes es superior al actual tampoco habra
		// cumplido años, por eso le quitamos un año al actual

		if ($mesNacimiento > $mes) {
			$año = ( $añoNacimiento - 1 );
		}
		//	ya no habria mas condiciones, ahora simplemente
		// restamos los años y mostramos el resultado como su edad
		$edad = ($año - $añoNacimiento);

		return $edad;
	}

}

?>