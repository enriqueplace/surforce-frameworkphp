<?php

/**
 * DBResult
 *
 * Resultado de consultas
 *
 * @package    Framework
 * @subpackage DB
 * @version    1.0
 * @abstract
 */
abstract class DBResult {
	
	/**
	 * Enlace de conexión al motor de bases de datos
	 *
	 * @var integer
	 */
	protected $conn;
	
	/**
	 * Recurso de resultados de consultas sql
	 *
	 * @var resource
	 */	
	protected $res;
	
	/**
	 * Resultados de la consulta en modo "array"
	 *
	 * @var array
	 */	
	private $resultArray;
	
	/**
	 * Resultados de la consulta en modo "object"
	 *
	 * @var array
	 */		
	private $resultObject;

	/**
	 * Constructor
	 */
	public function __construct() {
	}
	
	/**
	 * Devuelve la cantidad de registros que generó la consulta
	 *
	 * @abstract
	 * @return integer
	 */		
	abstract public function numRows();
	
	/**
	 * Libera el recurso de la consulta en el motor
	 *
	 * @abstract
	 * @return bool
	 */	
	abstract public function freeResult();
	
	/**
	 * Devuelve un registro en forma de matriz asociativa y mueve el puntero hacia adelante
	 *
	 * @abstract
	 * @return array
	 */		
	abstract protected function fetchAssoc();
	
	/**
	 * Devuelve un registro en forma de objeto y mueve el puntero hacia adelante
	 *
	 * @abstract
	 * @return object
	 */			
	abstract protected function fetchObject();
	
	/**
	 * Establecer conexión
	 *
	 * @param integer $conn Enlace de conexión
	 */		
	public function setConnection( $conn ) {
		$this->conn = $conn;
	}

	/**
	 * Establecer recurso
	 *
	 * @param resource $res Recurso de consulta
	 */			
	public function setResource( $res ) {
		$this->res = $res;
	}
	
	/**
	 * Devuelve un array con todos los resultados de la consulta, 
	 * variando el formato según el argumento $type.
	 *
	 * @param string $type object|array
	 * @return array
	 */	
	public function result( $type='object' ) {
		$type = ucfirst(strtolower($type));
		if($type !== 'Object') {
			$type = 'Assoc';
		}
		$resultProperty = "result" . $type;
		$fetchMethod   = "fetch"  . $type;
		if(!is_array($this->$resultProperty)) {
			$tmp = array();
			while($row = $this->$fetchMethod()) {
				$tmp[] = $row;
			}
			$this->$resultProperty = $tmp;
		}
		return $this->$resultProperty;
	}
	
	/**
	 * Devuelve el registro de la posición determinada por $pos 
	 * en el formato establecido por $type.
	 *
	 * @param integer $pos Posición del registro a devolver. Comienza en 1.
	 * @param string $type object|array
	 * @return object|array
	 */
	public function row( $pos=1, $type='object' ) {
		$resultArray = $this->result($type);
		$arrayPos = $pos;
		$arrayPos--;
		if(isset($resultArray[$arrayPos])) {
			return $resultArray[$arrayPos];
		} else {
			throw new Exception("No existe la posición $pos");
		}
	}
	
}

?>