<?php

include_once('DBResult.class.php');
include_once('DBQueryLog.class.php');

/**
 * DB
 *
 * Abstracción de acceso a datos
 * 
 * Modo de uso:
 * <code>
 * <?php
 * 
 * include "DB/DB.class.php";
 * 
 * // array de configuración para conexión
 * $config['engine']            = 'mysql';
 * $config['db']                = 'proyectobase';
 * $config['user']              = 'proyectobase';
 * $config['pwd']               = 'proyectobase';
 * #$config['host']             = 'localhost'; // 'localhost' es por por defecto
 *
 * // configuraciones para el log
 * $config['logType']           = 'file'; // tipo de log
 * // configuraciones para el tipo de log especificado,
 * // en el caso de "file", sólo se debe indicar la ruta
 * // del archivo de destino. se crea sino existe.
 * $config['logConfig']['path'] = 'log/db.log';
 * 
 * try {
 * 
 * 	$db =& DB::getInstance($config);
 * 	$db->connect();
 * 	
 *	// consulta: unico registro
 *	$sql = "select * from usuarios where id = '2'";
 *	$query = $db->query($sql);
 *	if($query->numRows() > 0) {
 *		$usuario = $query->row();
 *	}
 *	echo $usuario->descripcion . "<br />";
 *	$query->freeResult();
 *
 *  // consulta: recorrer muchos registros
 * 	$sql = "select * from usuarios";
 * 	$query = $db->query($sql);
 *  // forma tradicional
 * 	foreach($query->result() as $usuario) {
 *		// por defecto devolverá los datos en modo objetos,
 *		// para utilizar arrays, indicarle de la siguiente manera:
 *		// $query->result('array'); en el foreach.
 * 		echo $usuario->descripcion . "<br />";
 * 	}
 *  // variante utilizando el método row()
 *	for($i=1; $i<=$query->numRows(); $i++) {
 *		$usuario = $query->row($i);
 *		#$usuarioArray = $query->row($i, 'array');
 *		echo $usuario->descripcion . "<br />";
 *		#echo $usuario['descripcion'] . "<br />";
 *	}
 *  $primerUsuario = $query->row(1);
 *  $ultimoUsuario = $query->row($query->numRows());
 * 	$query->freeResult();
 * 	
 *  // eliminar
 * 	$sql = "delete from usuarios where edad > '75'";
 * 	if($db->query($sql)) {
 * 		echo "Cantidad de eliminados: " . $db->affectedRows();
 * 	}
 *
 *	// insertar
 *	$sql = "insert into usuarios(nombre,descrip) values('chiche','Chiche Helbrum')";
 *	if($sql->query($sql)) {
 *		$id = $db->insertId();
 *	}
 *
 *	// editar
 *	$sql = "update usuarios set descrip = 'Chiche Gelblum' where nombre = 'chiche'";
 *	if($sql->query($sql)) {
 *		echo "Ahora sí!";
 *	}
 *
 * 	$db->close();
 * 	
 * } catch( Exception $e ) {
 * 
 * 	echo "<p>" . $e->getMessage() . '</p>';
 * 	
 * }
 * 
 * ?>
 * </code>
 *
 * @author Dario Ocles <dario.ocles@gmail.com>
 * @author Sergio Flores <sercba@gmail.com>
 * @author http://tallerphp5.surforce.com/
 *
 * @package    Framework
 * @subpackage DB
 * @version    1.0
 * @abstract
 */
abstract class DB {

	/**
	 * Carpeta en donde estan ubicados los drivers, relativo a la carpeta actual.
	 */	
	const DRIVERS_FOLDER = 'drivers';
	
	/**
	 * Usuario con el cual se idenficará en el motor de bases de datos.
	 *
	 * @var string
	 */
	protected $user;
	
	/**
	 * Constraseña del usuario con el cual se idenficará en el motor de bases de datos.
	 *
	 * @var string
	 */	
	protected $pwd;
	
	/**
	 * Servidor en el que se intenta conectar con el motor de bases de datos.
	 *
	 * @var string
	 */		
	protected $host;
	
	/**
	 * Nombre de la base de datos a seleccionar para su utilización.
	 *
	 * @var string
	 */		
	protected $db;
	
	/**
	 * Enlace de conexión a la base de datos.
	 *
	 * @var integer
	 */		
	protected $conn;
	
	/**
	 * Nombre del motor de bases de datos. Se utiliza para hacer los includes de los 
	 * archivos y la instanciación de las clases.
	 *
	 * @var string
	 */		
	private $engine;
	
	/**
	 * Tipo de log a usar
	 *
	 * @var string
	 */		
	private $logType;
	
	/**
	 * Configuraciones que se pasaran al tipo de log utilizado
	 *
	 * @var array
	 */		
	private $logConfig;
	
	/**
	 * Constructor
	 */			
	public function __construct() {
	}	
	
	/**
	 * Devuelve la cantidad de registros afectados en una consulta de escritura.
	 *
	 * Las consultas de escrituras son aquellas que requieren escribir datos en la 
	 * base de datos, sentencias tales como "insert", "update", "delete", etc
	 *
	 * @abstract
	 * @return integer
	 */		
	abstract public function affectedRows();
	
	/**
	 * Devuelve el último identificador automático generado por una 
	 * consulta de tipo "insert".
	 *
	 * @abstract
	 * @return integer
	 */			
	abstract public function insertId();
	
	/**
	 * Realiza la conexión al motor
	 *
	 * @abstract
	 * @return bool
	 */			
	abstract protected function dbConnect();
	
	/**
	 * Selecciona la base de datos
	 *
	 * @abstract
	 * @return bool
	 */		
	abstract protected function dbSelect();
	
	/**
	 * Ejecuta una consulta
	 *
	 * @abstract
	 * @param  string $sql Consulta SQL a ejecutar
	 * @return bool|resource
	 */		
	abstract protected function dbExecute( $sql );
	
	/**
	 * Determina si el tipo de consulta pasada por parámetro deberia devolver un 
	 * objeto de tipo DBResult_[engine].
	 *
	 * @abstract
	 * @param  string $sql Consulta SQL a ejecutar
	 * @return bool
	 */		
	abstract protected function returnDBResult( $sql );
	
	/**
	 * Devuelve el último error generado en el motor de bases de datos
	 *
	 * @abstract
	 * @return string
	 */		
	abstract protected function dbError();
	
	/**
	 * Cierra la conexión con el motor de bases de datos
	 *
	 * @abstract
	 * @return bool
	 */		
	abstract protected function dbClose();
	
	/**
	 * Genera un objeto DB a partir de las configuraciones ingresadas
	 *
	 * @static
	 * @param array $config Configuraciones para acceder a la base de datos
	 * @return DB
	 * @throw Exception
	 */		
	public static function & getInstance( $config ) {
		if(isset($config['engine']) && strlen($config['engine'])) {
			$engine      = strtolower($config['engine']);
			$classEngine = 'DB_' . ucfirst($engine);
			$fileEngine  = $engine . DIRECTORY_SEPARATOR . $classEngine . '.class.php';
			$pathDrivers = dirname(__FILE__) . DIRECTORY_SEPARATOR . self::DRIVERS_FOLDER . DIRECTORY_SEPARATOR;
			$pathClass   = $pathDrivers . $fileEngine;
			if(file_exists($pathClass)) {
				include_once($pathClass);
				if(class_exists($classEngine)) {
					$instance =& new $classEngine();
					$instance->setConfig($config);
					return $instance;
				} else {
					throw new Exception("La clase '$classEngine' no está definida en el archivo '$pathClass'");
				}
			} else {
				throw new Exception("No se encuentra el archivo del driver '$engine' en la ruta '$pathClass'");
			}
		} else {
			throw new Exception('No se encuentra definido el motor de bases de datos a utilizar en los argumentos recibidos');
		}
	}
	
	/**
	 * Recibe los datos de configuración. 
	 *
	 * El mismo es llamado inicialmente desde DB::getInstance().
	 * Sólo deberia utilizarse para modificar algún valor de configuración y se 
	 * reemplazarán todos, los que no se especificaron volverán a su valor por defecto.
	 *
	 * @param array $config Configuraciones para acceder a la base de datos
	 * @throw Exception
	 */
	public function setConfig( $config ) {
		if(!isset($config['db']) || !strlen($config['db'])) {
			throw new Exception('No se especificó la base de datos a utilizar en los argumentos recibidos');
		}
		if(!isset($config['user']) || !strlen($config['user'])) {
			throw new Exception('No se especificó el usuario para ingresar al motor de base de datos');
		}
		$this->db        = $config['db'];
		$this->user      = $config['user'];
		$this->engine    = strtolower($config['engine']);
		$this->pwd       = (isset($config['pwd'])) ? $config['pwd'] : NULL;
		$this->host      = (isset($config['host'])) ? $config['host'] : 'localhost';
		$this->logType   = (isset($config['logType'])) ? $config['logType'] : NULL;
		$this->logConfig = (isset($config['logConfig'])) ? $config['logConfig'] : NULL;		
	}
	
	/**
	 * Realiza la conexión al motor y selecciona la base de datos especificada.
	 *
	 * @return bool
	 * @throw Exception
	 */	
	public function connect() {
		if($this->dbConnect()) {
			if(!$this->dbSelect()) {
				$errorMsg = $this->dbError();
				throw new Exception("No se puede seleccionar la base de datos. Mensaje: $errorMsg");
			}
		} else {
			$errorMsg = $this->dbError();
			throw new Exception("No se puede conectar a la base de datos. Mensaje: $errorMsg");
		}
		return true;
	}
	
	/**
	 * Cierra la conexión al motor en caso que haya alguna abierta.
	 *
	 * @return bool
	 */
	public function close() {
		$ret = false;
		if($this->conn) {
			$ret = $this->dbClose();
		}
		return $ret;
	}
	
	/**
	 * Ejecuta una consulta SQL.
	 *
	 * Si la consulta es de tipo escritura (insert, update, delete, etc.) devuelve 
	 * true en caso de éxito o lanza una excepción en caso de fallo.
	 * Si la consulta contiene resultados de selección, devuelve un objeto DBResult 
	 * para manipular dichos resultados.
	 * En caso que se quiera registrar el log en forma particular para esta consulta, 
	 * se deberá pasar un parámetro adicional, el cual deberá ser un array 
	 * con la siguiente estructura y forma de uso:
	 * <code>
	 * <?php
	 * //...
	 * $logConfig['logType']           = 'file';
	 * $logConfig['logConfig']['path'] = 'archivo_especifico.log';
	 * $query = $db->query( 'select * from tabla', $logConfig );
	 * //...
	 * ?>
	 * </code>
	 *
	 * @param string $sql Consulta SQL a ejecutar
	 * @param array $log optional Configuraciones de log particulares para esta consulta
	 * @return bool|DBResult
	 * @throw Exception
	 */
	public function query( $sql, $log=null ) {
		$ret = true;
		if(!$this->conn) {
			$this->connect();
		}
		// log
		$logType   = null;
		$logConfig = array();
		if($log) {
			$logType   = $log['logType'];
			$logConfig = $log['logConfig'];
		} elseif($this->logType) {
			$logType   = $this->logType;
			$logConfig = $this->logConfig;			
		}
		if($logType) {
			$classLog = 'DBQueryLog' . ucfirst($logType);
			$fileLog = dirname(__FILE__) . DIRECTORY_SEPARATOR. $classLog . '.class.php';
			if(file_exists($fileLog)) {
				include_once($fileLog);
				$log =& new $classLog();
				$log->setConfig($logConfig);
				$log->setQuery($sql);
				$log->store();
			} else {
				throw new Exception("No se encuentra el archivo '$fileLog'");
			}
		}
		// consulta
		$query = $this->dbExecute($sql);
		if(!$query) {
			$errorMsg = $this->dbError();
			throw new Exception("Consulta inválida. Mensaje: $errorMsg");
		} else {
			if($this->returnDBResult($sql)) {
				$engine      = $this->engine;
				$classResult = 'DBResult_' . ucfirst($engine);
				$fileResult  = $engine . DIRECTORY_SEPARATOR . $classResult . '.class.php';
				$pathDrivers = dirname(__FILE__) . DIRECTORY_SEPARATOR . self::DRIVERS_FOLDER . DIRECTORY_SEPARATOR;
				$pathClass   = $pathDrivers . $fileResult;
				if(file_exists($pathClass)) {
					include_once($pathClass);
					if(class_exists($classResult)) {
						$result =& new $classResult($this->conn, $query);
						$result->setConnection($this->conn);
						$result->setResource($query);
						return $result;
					} else {
						throw new Exception("La clase '$classResult' no está definida en el archivo '$fileResult'");
					}
				} else {
					throw new Exception("No se encuentra el archivo del driver '$engine' en la ruta '$fileResult'");
				}				
			}
		}
		return $ret;
	}
}

?>