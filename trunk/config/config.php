<?php
class Config
{

    //Atributos
    static private $thisInstance = null;
    private        $Settings_array = array();
    private        $Config_path;

    /************************************************/
    /*<SINGLETON>                                   */
    /************************************************/
    static public function GetInstance($file='')
    {
        if(self::$thisInstance == null)
        {
            self::$thisInstance = new self($file);
        }
        return self::$thisInstance;

    }
    //fin getInstance
    /************************************************/
    /*</SINGLETON>                                    */
    /************************************************/

    /**
     * Constructor
     *
     */
    private function __construct($file)
    {
        if($file!=''){           
            if(ereg("(.)+\.ini$", $file)){               
                $this->config_ini($file);
            }
            if(ereg("(.)+\.xml$", $file)){
                $this->config_xml($file);
            }
        }
    }
   

    /**
     * Obtiene un valor de configuración
     *
     * @param string $value_conf
     * @return mixed
     */
    public function get_config($value_conf)
    {
        return $this->Settings_array[$value_conf];
    }

    /**
     * Cambia un valor de configuración
     *
     * @param string $key
     * @param mixed $value
     */
    public function set_config($key, $value)
    {
        //Se debe validar que el parametro ya exista?
        $this->Settings_array[$key] = $value;
    }

    /**
     * Especifica el PATH donde se encuentran los archivos
     * de configuración
     *
     * @param string $dir
     */
    public function set_config_path($dir){
        $this->Config_path = $dir."/";
    }

    /**
     * Crea los parámetros de configuración desde un archivo .INI $file
     * @param $file
     */
    public function config_ini($file){

        if(file_exists($this->Config_path.$file)){
            $parse_ini_array = parse_ini_file($this->Config_path.$file, true);
            $this->create_config($parse_ini_array);
            return true;
        } else {
            throw new Exception('No existe el archivo de configuraci&oacute;n '.$file.' en el config_path');
            return false;
        }
    }
   
    /**
     * Crea los parámetros de configuración desde un archivo .XML
     * El archivo tiene un formato como:
     * <?xml version='1.0'?>
     * <config>
     *    <parametro>valor 0</parametro>
     *    <parametro2>valor 1</parametro2>
     *    <parametro2>valor 2</parametro2>
     * </config>
     *
     * @param $file
     */
    public function config_xml($file){

        if(file_exists($this->Config_path.$file)){
            $parse_ini_array = simplexml_load_file($this->Config_path.$file);           
            $this->create_config($parse_ini_array);
            return true;
        } else {
            throw new Exception('No existe el archivo de configuraci&oacute;n '.$file.' en el config_path');
            return false;
        }
    }
   
    /**
     * Asigna los valores de los parametros de configuración que
     * vienen del archivo INI ó XML
     *
     * @param array $array_config
     */
    private function create_config($array_config){
        if(is_array($array_config)){
            foreach($array_config as $key => $value){   
                $this->Settings_array[$key] = $value;               
            }
        } else {
            if(is_object($array_config)){                   
                foreach($array_config->children() as $key => $value){       
                    $this->Settings_array[$key] = (string) $value;
                }               
            } else {           
                throw new Exception('Formato de Configuración Invalido');
            }
        }       
        //print_r($this->Settings_array);
    }
   


}

try {
    //Sugiero pasar el nombre del archivo xml o ini que va a ser utilizado en la configuración
    $config = Config::GetInstance('file.xml');  
    //obtener configuracion del servidor de vase de datos
	print $config->get_config('db_usuario');
	
	$config->set_config('test','hola_test');
	print $config->get_config('test');
}
catch(Exception $e){
    print $e->getMessage();
}
?>