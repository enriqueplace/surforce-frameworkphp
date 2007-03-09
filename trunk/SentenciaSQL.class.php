<?php
require_once( "configuracion.php" );

/*
 Esta clase tiene la funcionalidad de armar una sentencia con los parametros recibidos
 - Controla que no se carguen items repetidos en cualquier array
 - Da mensaje de sentencia no valida en caso de faltar items en el select o en el from


	// Ejemplo de uso
	$miS = new SentenciaSQL();

	$miS->elegirCampo("id");
	$miS->elegirCampo("nombre");
	$miS->elegirCampo("nombre"); // debería tomar uno solo, no ambos
	$miS->elegirTabla("clientes");
	$miS->elegirCondicion("id = 1");

	echo $miS->generar();

	//Resultado: SELECT id,nombre FROM clientes WHERE id = 1;

 */
class SentenciaSQL {
    private $colSelect;
	private $colFrom;
	private $colWhere;

	public function elegirCampo($select){
		$this->colSelect[]=$select;
	}

	public function elegirTabla($from){
		$this->colFrom[]=$from;
	}

	public function elegirCondicion($where){
		$this->colWhere[]=$where;
	}
	private function eliminarDuplicados(){
		// eliminan duplicados
		$this->colSelect = array_unique($this->colSelect);
		$this->colFrom = array_unique($this->colFrom);
		$this->colWhere = array_unique($this->colWhere);
	}

	private function recorroSelect(){


			// Recorro la lista de items del select
			$res = "SELECT ";
			$cont=1;
			foreach($this->colSelect as $s){
				// Si es la primera vez solo agrego el item
				if ($cont == 1){
					$res.= $s;
				}
				// Si no es la primera vez, tambien se le agrega una coma antes del item
				else{
					$res.= ", ".$s;
				}
				$cont = $cont +1;
			}
				return $res;
			}

	private function recorroFrom(){

			// Recorro la lista de items del from
			$res.= " FROM ";
			$cont=1;
			foreach($this->colFrom as $f){
				// Si es la primera vez solo agrego el item
				if ($cont == 1){
					$res.= $f;
				}
				// Si no es la primera vez, tambien se le agrega una coma antes del item
				else{
					$res.= ", ".$f;
				}
				$cont = $cont +1;
			}
				return $res;
			}

	private function recorroWhere(){

		// Unicamente si hay alguna condicion de where ejecuto el código
			// ya que puede ser que no haya condiciones en la consulta

			if (count($this->colWhere)!=0){


				// Recorro la lista de condiciones del where
				$res.= " WHERE ";
				$cont=1;
				foreach($this->colWhere as $w){
					// Si es la primera vez solo agrego el item
					if ($cont == 1){
						$res.= $w;
					}
					// Si no es la primera vez, tambien se le agrega una coma antes del item
					else{
						$res.= " AND ".$w;
					}
					$cont = $cont +1;
				}
					return $res;
			}

	}


	public function generar(){

		$this->eliminarDuplicados();

		// Chequeo primero que haya algun item en la lista de select y en la de From
		// Si en alguno de estos no hay elementos, retorno un mensaje de
		// Sentencia no valida
		if (count($this->colSelect)!=0 & count($this->colFrom)!=0){

			$res = $this->recorroSelect();
			$res .= $this->recorroFrom();
			$res .= $this->recorroWhere();

			// Finalizo la consulta con un ;
			$res.=";";
		}
		else{
			$res = "Sentencia no valida";
		}
		return $res;
	}

}

?>