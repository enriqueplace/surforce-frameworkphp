<?php

/**
 * DBQueryLog
 *
 * Clase que permite mantener un log de todas y aquellas consultas
 * que se hagan a la DB y se quiera tener un registro.
 *
 * Esta clase no debería ser instanciada ya que es la clase base y
 * de esta se desprenden las distintas especificaciones de la clase...
 * Por ejemplo la clase para XML's, o en un archivo de texto plano.
 *
 * @author Dario Ocles <dario.ocles@gmail.com>
 * @author Sergio Flores <sercba@gmail.com>
 *
 * @package    Framework
 * @subpackage DB
 * @version    1.0
 * @abstract
 */
abstract class DBQueryLog {

	/**
	 * Consulta SQL a registrar
	 *
	 * @var string
	 */
	private $query="";
	
	/**
	 * Constructor
	 */
	public function __construct() {
	}

	/**
	 * Establecer query
	 *
	 * @param string $query Consulta SQL a registrar.
	 */	
	public function setQuery( $query ) {
		$this->query = $query;
	}

	/**
	 * Obtener query
	 * 
	 * @return string
	 */		
	public function getQuery() {
		return $this->query;
	}
}

?>
