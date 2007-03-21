<?php
/**
* Config
*
* Clase para manejar par�metros de Configuraci�n del Sistema
*
* Permite obtener par�metros de configuraci�n desde un archivo XML o INI
* Permite setear nuevos par�metros de configuraci�n o modificar los existentes
* El archivo XML tiene la siguiente extructura:
* <?xml version='1.0' standalone='yes'?>
*	<config>
*		<db_server>localhost</db_server>
*		<db_usuario>usuario</db_usuario>
*		<db_password>password</db_password>
*		<db_base>base</db_base>
*	</config>
* Forma de uso de Config:
*	try {
*		$config = Config::GetInstance('../file.xml');  
*		//obtener configuracion del servidor de vase de datos
*		print $config->getConfig('db_usuario');
*		//Agrega Configuraci�n
*		$config->setConfig('test','hola_test');
*		//obtener configuracion
*		print $config->getConfig('test');
*	}
*	catch(Exception $e){
*		print $e->getMessage();
*	}
*
* @author Andr�s Guzm�n Fontecilla <andresguzf@gmail.com>
* @author AAndres Felipe Gutierrez <gutierrezandresfelipe@gmail.com>
* @author http://tallerphp5.surforce.com/
*
* @package Framework
*/

class Config{

	/**
	* Intancia static de la clase Config
	* @var Object
	* @access private
	*/
    static private $thisInstance = null;
    
	/**
	* array de par�metros de configuraciones
	* @var array
	* @access private
	*/
    private        $SettingsArray = array();
    
	/**
	* string Directorio del archivo de configuraci�n
	* @var string
	* @access private
	*/
    private        $ConfigPath;

	/**
	* Constructor.
	*
	* @access private
	* @param string $file nombre del archivo de configuraci�n
	*/
    private function __construct($file){
        if($file!=''){           
            if(ereg("(.)+\.ini$", $file)){               
                $this->configIni($file);
            }
            if(ereg("(.)+\.xml$", $file)){
                $this->configXml($file);
            }
        }
    }
    
	/**
	* Verifica si una instancia al objeto existe.
	*
	* @access public
	* @return Object
	* @param string $file nombre del archivo de configuraci�n
	*/
    static public function GetInstance($file=''){
        if(self::$thisInstance == null)
        {
            self::$thisInstance = new self($file);
        }
        return self::$thisInstance;

    }

    /**
     * Obtiene un valor de configuraci�n
     *
     * @access public
     * @param string $valueConf
     * @return mixed
     */
    public function getConfig($valueConf){
        return $this->SettingsArray[$valueConf];
    }

    /**
     * Cambia un valor de configuraci�n
     *
     * @access public
     * @param string $key
     * @param mixed $value
     */
    public function setConfig($key, $value){
        //Se debe validar que el parametro ya exista?
        $this->SettingsArray[$key] = $value;
    }

    /**
     * Especifica el PATH donde se encuentran los archivos
     * de configuraci�n
     *
     * @access public
     * @param string $dir
     */
    public function setConfigPath($dir){
        $this->ConfigPath = $dir."/";
    }

    /**
     * Crea los par�metros de configuraci�n desde un archivo .INI $file
     * @access private
     * @param $file
     */
    private function configIni($file){

        if(file_exists($this->ConfigPath.$file)){
            $parseIniArray = parse_ini_file($this->ConfigPath.$file, true);
            $this->createConfig($parseIniArray);
            return true;
        } else {
            throw new Exception('No existe el archivo de configuraci&oacute;n '.$file.' en el configPath');
            return false;
        }
    }
   
    /**
     * Crea los par�metros de configuraci�n desde un archivo .XML
     *
     * @access private
     * @param $file
     */
    private function configXml($file){

        if(file_exists($this->ConfigPath.$file)){
            $parseXmlArray = simplexml_load_file($this->ConfigPath.$file);           
            $this->createConfig($parseXmlArray);
            return true;
        } else {
            throw new Exception('No existe el archivo de configuraci&oacute;n '.$file.' en el configPath');
            return false;
        }
    }
   
    /**
     * Asigna los valores de los parametros de configuraci�n que
     * vienen del archivo INI � XML
     *
     * @access private
     * @param array $arrayConfig
     */
    private function createConfig($arrayConfig){
        if(is_array($arrayConfig)){
            foreach($arrayConfig as $key => $value){   
                $this->SettingsArray[$key] = $value;               
            }
        } else {
            if(is_object($arrayConfig)){                   
                foreach($arrayConfig->children() as $key => $value){       
                    $this->SettingsArray[$key] = (string) $value;
                }               
            } else {           
                throw new Exception('Formato de Configuraci�n Invalido');
            }
        }       
    }
}
?>