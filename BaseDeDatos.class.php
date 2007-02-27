<?php
require_once( "configuracion.php" );

    /**
    * La clase "BaseDeDatos" es una capa de abstracción que
    * sirve para trabajar contra un motor de base de datos.
    *
    * Forma de uso
    *
    * [FALTA]
    *
    */

class BaseDeDatos {

    /**
     * Usuario para acceder al servidor
     */
    private $usuario;
    /**
     * Clave de usuario para acceder al servidor
     */
    private $clave;
    /**
     * Servidor donde esta alojado el gestor de base de datos
     */
    private $servidor;
    /**
     * Base de Datos con la que se va a trabajar
     */
    private $base;
    /**
     * Recurso de conexión
     */
    private $conexion;
    /**
     * Recurso resultado
     */
    private $resultado;
	private $archivoConfiguracion;

	// Getter / Setter

 	public function getConexion(){ return $this->conexion; }

	// Métodos

	public function __construct( $archivoConfiguracion ) {
        $this->archivoConfiguracion = $archivoConfiguracion;
    }
	private function getConfiguracion(){
		if( file_exists( $this->archivoConfiguracion ) ){
			$arch = fopen( "$this->archivoConfiguracion","r" );

			$t = fscanf($arch, "usuario = %s\n");
			$this->usuario = $t[0];
			$t = fscanf($arch, "clave = %s\n");
			$this->clave = $t[0];
		    $t = fscanf($arch, "host = %s\n");
		    $this->servidor = $t[0];
		    $t = fscanf($arch, "base = %s\n");
		    $this->base = $t[0];

			fclose($arch);
		}else{
			throw new Exception("archivo de configuración no existe: " . $this->archivoConfiguracion );
		}
	}
    /**
     * Establece la conexion y selecciona la Base de Datos
     */
    public function conectar(){
    	$this->getConfiguracion();
		$this->getInstancia();
		if( $this->conexion == NULL ){
			throw new Exception("No se pudo establecer la conexión");
		}
        if( !mysql_select_db($this->base) ){
        	throw new Exception("No se pudo seleccionar la Base de Datos");
        }
    }
	/**
	 * Singleton
	 */
	private function getInstancia(){
    	if( $this->conexion == NULL ){
    		$this->conexion = mysql_connect($this->servidor,$this->usuario,$this->clave);
    	}
	 }
    /**
     * Recibe un string con la sentencia SQL y la ejecuta
     */
    public function ejecutarSQL( $sentencia, $parametros="" ){
    	if( $parametros != ""){
			$this->procesarParametrosSentencia( $sentencia, $parametros);
    	}
    	if ($this->resultado){
	   		@mysql_free_result($this->resultado);
    	}
    	$this->resultado = mysql_query($sentencia);
    	if( !$this->resultado ){
    		throw new Exception("No se pudo ejecutar la consulta");
    	}
    }
	private function procesarParametrosSentencia(&$sql, $parametros){
		foreach( $parametros as $key => $value ){
			$sql = ereg_replace( $key, $this->quote($value), $sql);
		}
	}
	/**
	 * Verifica los datos que se reciben por parámetros no tengan código
	 * dañino (sql injection, etc)
	 */
	private function quote( $var ){
		if( get_magic_quotes_gpc() ){
			$var = stripslashes( $var );
		}
		if( function_exists( "mysql_real_escape_string" )){
			$var = mysql_real_escape_string( $var, $this->conexion );
		}else{
			$var = addslashes( $var );
		}
		return $var;
	}
    /**
     * Retorna un entero con la cantidad de filas afectadas
     */
    public function filasAfectadas(){
        return mysql_affected_rows();
    }
    /**
     * Retorna un array asociativo con una linea de la consulta
     */
    public function traerLinea(){
        return mysql_fetch_array($this->resultado,MYSQL_ASSOC);
    }
    /**
     * Retorna un array asociativo con todo el contenido de la consulta
     */
    public function traerTodo(){
		$todo= array();
	    while ($row = mysql_fetch_array($this->resultado, MYSQL_ASSOC)){
	    	$todo[] = $row;
	    }
        return $todo;
    }
	/**
     * Cierra la conexion con el servidor y libera la menoria pedida para al ejecutar la consulta
     */
    public function desconectar(){
        @mysql_free_result($this->resultado);
        mysql_close($this->conexion);
    }
}
?>