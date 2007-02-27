<?php
require_once( "configuracion.php" );

/**
 * La clase Log permitir� hacer un archivo de log, evitando tener que hacer despliegues
 * por pantalla con echo y die, no alterando el funcionamiento del sistema. El problema
 * de este �ltimo caso es que luego de encontrado el error debemos asegurarnos de
 * que todas las l�neas de debug se hayan borrado. Con el archivo de log no sucede esto.
 */
class Log {
	private $nombre_archivo;

	/**
	 * Requiere: nombre de archivo para generar el log, en lo posible, ruta completa (/tmp/archivo.log)
	 */
    public function __construct($n) {
    	if(is_null($n)){
    		die(__FILE__." : el par�metro nombre de archivo no puede estar vac�o");
    	}else{
    		$this->nombre_archivo = $n;
    	}
    }
    /**
     * Requiere: el contenido que ser� grabado en el
     * 	archivo de log
     */
    public function escribir($contenido){
    	// Abre/Crea archivo de log
		 if (!$gestor = fopen($this->nombre_archivo, 'a+')) {
        	 	echo __FILE__.": No se puede abrir el archivo (".$this->nombre_archivo.")";
         		exit;
   			}

		   // Escribir $contenido a nuestro archivo abierto, verifica que
		   // la operaci�n sea exitosa
   			if (fwrite($gestor, $contenido) === FALSE) {
       			echo __FILE__.": No se puede escribir al archivo (".$this->nombre_archivo.")";
       			exit;
   			}
   			fclose($gestor);
    }
}

?>