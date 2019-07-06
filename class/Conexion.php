<?php 

class DATA {

	public static $conn;
    private static $connSql;
    private static $config="";
    private static $configLDAP=[];
    
	private static function ConfiguracionIni(){
        require_once('Globals.php');
        $dir=  __DIR__.DIRECTORY_SEPARATOR.".." .DIRECTORY_SEPARATOR.".." .DIRECTORY_SEPARATOR.'ini'.DIRECTORY_SEPARATOR."config.ini";
        if (file_exists($dir)) {
            $a = self::$config = parse_ini_file($dir,true);
            $b = $a;
        }       
        else throw new Exception('Acceso denegado al Archivo de configuracion.',-1);  
    }  

    private static function Conectar(){
        try {          
            self::ConfiguracionIni();
            if(!isset(self::$conn)) {                                
                self::$conn = new PDO('mysql:host='. self::$config[Globals::app]['host'] .';port='. self::$config[Globals::app]['port'] .';dbname=' . self::$config[Globals::app]['dbname'].';charset=utf8', self::$config[Globals::app]['username'],   self::$config[Globals::app]['password']); 
                return self::$conn;
            }
        } catch (PDOException $e) {
            throw new Exception($e->getMessage(),$e->getCode());
        }
        catch(Exception $e){
            throw new Exception($e->getMessage(),$e->getCode());
        }
    }

    public static function getLDAP_Param() {
        try {
            self::ConfiguracionIni();            
            self::$configLDAP= array(
                    "LDAP_user"=>self::$config[Globals::app]['LDAP_user'],
                    "LDAP_passwd"=>self::$config[Globals::app]['LDAP_passwd'],
                    "LDAP_server"=>self::$config[Globals::app]['LDAP_server'],
                    "LDAP_port"=>self::$config[Globals::app]['LDAP_port'],
                    "LDAP_base_dn"=>self::$config[Globals::app]['LDAP_base_dn']
                );
            return self::$configLDAP;
        }
        catch(Exception $e){
            throw new Exception($e->getMessage(),$e->getCode());
        }
    }
    
    
    // Ejecuta consulta SQL, $fetch = false envía los datos en 'crudo', $fetch=TRUE envía los datos en arreglo (fetchAll).
    public static function Ejecutar($sql, $param=NULL, $fetch=true) {
        try{
            //conecta a BD
            self::Conectar();
            $st=self::$conn->prepare($sql); 
            self::$conn->beginTransaction(); 
            if($st->execute($param))
            {
                self::$conn->commit(); 
                if($fetch)
                    return  $st->fetchAll();
                else return $st;    
            } else {
                throw new Exception('Error al ejecutar.',00);
            }            
        } catch (Exception $e) {
            if(isset(self::$conn))
                self::$conn->rollback(); 
            if(isset($st))
                throw new Exception($st->errorInfo()[2],$st->errorInfo()[1]);
            else throw new Exception($e->getMessage(),$e->getCode());
        }
    }   

    
	private static function Close(){
		mysqli_close(self::$conn);			
	}

    public static function getLastID(){
        return self::$conn->lastInsertId();
    }
}
?>
