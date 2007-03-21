<?php

/**
 * DBQueryLogFile (extiende a DBQueryLog)
 *
 * Esta clase es una extención a la clase DBQueryLog. Aquí se extiende
 * la funcionalidad a un archivo de texto plano.
 * Tambien se guarda la fecha y la hora en ISO 8601 (que es un formato
 * estandar). La fecha y la hora se toman del servidor y es tomada en
 * el momento que se ejecuta DBQueryLogFile->store().
 *
 * Ejemplo de uso:
 * <code>
 * $log = new DBQueryLogFile();
 * $logConfig = array( 'path' => 'log.txt' );
 * $log->setConfig($logConfig);
 * $log->setQuery('CONSULTA SQL A REGISTRAR');
 *
 * try{
 * 	$log->store() === FALSE;
 * }catch (Exception $e){
 * 	echo $e->getMessage();
 * }
 * </code>
 *
 * @author Dario Ocles <dario.ocles@gmail.com>
 * @author Sergio Flores <sercba@gmail.com>
 *
 * @package    Framework
 * @subpackage DB
 * @version    1.0
 */
class DBQueryLogFile extends DBQueryLog {

    /**
	 * Path del archivo que se creara/modificara
	 *
	 * @var string
	 * @access private
	 */
	private $path="";

    /**
	 * Se setea las configuraciones necesarias.
	 *
	 * @access public
	 * @param array $logConfig
	 */
	public function setConfig($logConfig){
		$this->path = $logConfig['path'];
	}

    /**
     * Se registra la consulta
     *
     * @access public
     * @return bool
     */
	public function store(){
		$fp = @fopen($this->path, 'a+');

		if(!$fp){
			throw new Exception('No se pudo abrir/crear el archivo '.$this->path);
			return FALSE;
		}

		$contenido = date('c', time())."\t".$this->getQuery()."\r\n";

		if(@fwrite($fp, $contenido) === FALSE){
			throw new Exception('No se pudo escribir en el archivo '.$this->path);
			return FALSE;
		}

		fclose($fp);

		return TRUE;
	}
	
}

?>
