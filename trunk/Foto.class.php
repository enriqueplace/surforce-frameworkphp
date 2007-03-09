<?php

/**
 * Fixme: clase candidata a refactoring (EP)
 */
require_once("configuracion.php");

class Foto{
	const MIN_ALTO	= "80";	// MINIMO en Alto
	const MIN_ANCHO	= "80";	// MINIMO en Ancho
	const MIN_SIZE 	= "0"; 	// MINIMO en Bytes

	const MAX_ALTO 	= "2048";		// MAXIMO en Alto
	const MAX_ANCHO = "2048";		// MAXIMO en Ancho
	const MAX_SIZE 	= "716800";	// MAXIMO en Bytes

	private $nombreArchivo;
	private $mensaje;
	private $retorno = array(); // [error][mensaje]

	/* Getter */
	public function getNombreArchivo(){return $this->nombreArchivo;}

	/*
	 *  Guardar foto
 	*/
	public function guardarFoto($nombre, $archivo){

		if(is_uploaded_file($archivo)){
			$fotoTemp = $archivo;
		}else{
			$this->mensaje = "No se subi� la im�gen";
			$this->retorno = array("error" => true, "mensaje" =>$this->mensaje);
			return $this->retorno;
		}

		// Verifica el tama�o de la imagen (en Bytes)

		// Tama�o maximo
		if(filesize($fotoTemp) > self::MAX_SIZE){
			$this->mensaje = "La im�gen es muy grande :(";
			$this->retorno = array("error" => true, "mensaje" =>$this->mensaje);
			return $this->retorno;
		}

		// Tama�o minimo
		if(filesize($fotoTemp) <= self::MIN_SIZE){
			$this->mensaje = "La im�gen es muy peque�a :(";
			$this->retorno = array("error" => true, "mensaje" =>$this->mensaje);
			return $this->retorno;
		}

		// Verifica las dimensiones
		$foto_info = getimagesize($fotoTemp);

		// Ancho Maximo
		if($foto_info[0] > self::MAX_ANCHO){
			$this->mensaje = "La im�gen es muy grande :(";
			$this->retorno = array("error" => true, "mensaje" =>$this->mensaje);
			return $this->retorno;
		}

		// Ancho Minimo
		if($foto_info[0] < self::MIN_ANCHO){
			$this->mensaje = "La im�gen es muy peque�a :(";
			$this->retorno = array("error" => true, "mensaje" =>$this->mensaje);
			return $this->retorno;
		}

		// Alto Maximo
		if($foto_info[1] > self::MAX_ALTO){
			$this->mensaje = "La im�gen es muy grande :(";
			$this->retorno = array("error" => true, "mensaje" =>$this->mensaje);
			return $this->retorno;
		}

		// Alto Minimo
		if($foto_info[1] < self::MIN_ALTO){
			$this->mensaje = "La im�gen es muy peque�a :(";
			$this->retorno = array("error" => true, "mensaje" =>$this->mensaje);
			return $this->retorno;
		}

		// Formato de im�gen
		if($foto_info[2] == 1){
			$extension = ".gif";
		}elseif($foto_info[2] == 2){
			$extension = ".jpg";
		}elseif ($foto_info[2] == 3){
			$extension = ".png";
		}else{
			$this->mensaje = "Formato de im�gen no v�lido :(. Los tipos admitidos son: jpg, gif y png.";
			$this->retorno = array("error" => true, "mensaje" =>$this->mensaje);
			return $this->retorno;
		}

		$this->nombreArchivo = $nombre.$extension;

		if(!copy($fotoTemp, FOTOS."/".$this->nombreArchivo)){
			$this->mensaje = "No se pudo almacenar la im�gen en el directorio ".FOTOS;
			$this->retorno = array("error" => true, "mensaje" =>$this->mensaje);
			return $this->retorno;
		}

		$this->crearFotoMiniatura(FOTOS."/".$this->nombreArchivo, $_SESSION['gmail'], '100', '100');

		$this->retorno = array("error" => false, "mensaje"=>$this->getNombreArchivo());

		return $this->retorno;
	}

	/*
	 * Crea una im�gen miniatura
	 */
	public function crearFotoMiniatura($imagen, $nombre, $ancho, $alto, $tipo_pic = ""){

		$formato = strtolower(substr(strrchr($imagen,"."),1));

		switch($formato){
		   	case 'jpeg' :
			   	$tipo ="jpg";
			   	$img = imagecreatefromjpeg($imagen);
			  	break;
		   	case 'jpg' :
			   	$tipo ="jpg";
			   	$img = imagecreatefromjpeg($imagen);
			  	break;
		   	case 'gif' :
			   	$tipo ="gif";
			   	$img = imagecreatefromgif($imagen);
			   	break;
			case 'png' :
			   	$tipo ="png";
			  	$img = imagecreatefrompng($imagen);
			   	break;
			case 'jpg' :
				$tipo ="jpg";
				$img = imagecreatefromjpeg($imagen);
				break;
			default:
				$this->mensaje = "Formato de im�gen no v�lido (Miniatura) :(. Los tipos admitidos son: jpg, jpeg, gif y png.";
				$this->retorno = array("error" => true, "mensaje" =>$this->mensaje);
				return $this->retorno;
				break;
		}

		list($ancho_original, $alto_original) = getimagesize($imagen);
		$xoffset = 0;
		$yoffset = 0;
		if ($tipo_pic == 'recortar'){
			if ($ancho_original / $ancho > $alto_original/ $alto){
				$xtmp = $ancho_original;
				$xratio = 1-((($ancho_original/$alto_original)-($ancho/$alto))/2);
				$ancho_original = $ancho_original * $xratio;
				$xoffset = ($xtmp - $ancho_original)/2;
			}else{
				if ($alto_original/ $alto > $ancho_original / $ancho){
					$ytmp = $alto_original;
					$yratio = 1-((($ancho/$alto)-($ancho_original/$alto_original))/2);
					$alto_original = $alto_original * $yratio;
					$yoffset = ($ytmp - $alto_original)/2;
				}
			}
		}else{
			 $xtmp = $ancho_original/$ancho;
			 $nuevo_ancho = $ancho;
			 $nuevo_alto = $alto_original/$xtmp;
			 if ($nuevo_alto > $alto){
				$ytmp = $alto_original/$alto;
				$nuevo_alto = $alto;
				$nuevo_ancho = $ancho_original/$ytmp;
			}
			$ancho = round($nuevo_ancho);
			$alto = round($nuevo_alto);
		}

		$img_n=imagecreatetruecolor ($ancho, $alto);
		imagecopyresampled($img_n, $img, 0, 0, $xoffset, $yoffset, $ancho, $alto, $ancho_original, $alto_original);

		if($tipo=="gif"){
			imagegif($img_n, FOTOS.'/'.$nombre.'_sm.'.$tipo);
		}elseif($tipo=="jpg"){
			  imagejpeg($img_n, FOTOS.'/'.$nombre.'_sm.'.$tipo);
		}elseif($tipo=="png"){
			  imagepng($img_n, FOTOS.'/'.$nombre.'_sm.'.$tipo);
		}
		return true;

	}
}
?>