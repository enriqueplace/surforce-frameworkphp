<?php
require_once( "configuracion.php" );

abstract class Sesion {

	public function cargarPost2Sesion(){
		if( $_POST ){
			foreach( $_POST as $hash => $valor ){
				$_SESSION[$hash] = $valor;
			}
		}
	}

	public function destruirSesion(){
		session_unset();
		session_destroy();
	}

}
?>