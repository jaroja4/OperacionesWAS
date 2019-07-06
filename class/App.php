<?php
//ACTION
if( isset($_POST["action"])){        
    $opt= $_POST["action"];
    unset($_POST['action']);    
    // Classes
    require_once("Conexion.php");
    // 
    // Instance
    $app = new App();
    switch($opt){
        case "ReadAll":
            echo json_encode($app->ReadAll());
            break;
        case "Create":
            echo json_encode($app->Create());
            break;
        case "Delete":
            echo json_encode($app->Delete());
            break;
        case "ReadAppByid":
            echo json_encode($app->ReadAppByid());
            break;
        case "Update":
            echo json_encode($app->Update());
            break;
    }
}

class App{
    public $id="";   
    public $nombre="";   
    public $idResponsable="";   
    public $ambiente="";   
    public $plataforma="";
    public $variables = [];


    function __construct(){

        if(isset($_POST["id"])){
            $this->id= $_POST["id"];
        }
        
        if(isset($_POST["obj"])){
            $obj= json_decode($_POST["obj"],true);
            $this->variables = $obj["variables"];
            // foreach ($obj["variables"] as $itemVariable) {
            // }
            $this->id=$obj["id"] ?? NULL;   
            $this->nombre=$obj["nombre"] ?? NULL;   
            $this->idResponsable=$obj["idResponsable"] ?? NULL;   
            $this->ambiente=$obj["ambiente"] ?? NULL; 
            $this->plataforma=$obj["plataforma"] ?? NULL;
        }
    }

    function ReadAll(){
        try {
            $sql="SELECT ap.id, ap.idResponsable, ap.nombre, ap.ambiente, ap.plataforma, re.nombre responsable
                FROM apps ap
                INNER JOIN responsable re
                ON re.id = ap.idResponsable;";        
            $data= DATA::Ejecutar($sql);
            
            if($data){
                return $data;
            }
            else {
                return false;
            }

        }     
        catch(Exception $e) {
            error_log("[ERROR]  (".$e->getCode()."): ". $e->getMessage());
            header('HTTP/1.0 400 Bad error');
            die(json_encode(array(
                'code' => $e->getCode() ,
                'msg' => 'Error al cargar la lista'))
            );
        }
    }

    function Update(){
        try {            
            $sql='UPDATE aplicaciones_was.apps
            SET idResponsable=:idResponsable, 
            nombre=:nombre,
            ambiente=:ambiente,
            plataforma=:plataforma
            WHERE id=:id;';
            $param= array(':id'=>$this->id, ':nombre'=>$this->nombre,':ambiente'=>$this->ambiente,':plataforma'=>$this->plataforma,
                ':idResponsable'=>$this->idResponsable);  
            $data = DATA::Ejecutar($sql,$param);   
            
            if($data){
                return $data;
            }
            else {
                return false;
            }

        }     
        catch(Exception $e) {
            error_log("[ERROR]  (".$e->getCode()."): ". $e->getMessage());
            header('HTTP/1.0 400 Bad error');
            die(json_encode(array(
                'code' => $e->getCode() ,
                'msg' => 'Error al cargar la lista'))
            );
        }
    }

    function Create(){
        try {
            $this->idResponsable = "4470ce0a-8c08-11e9-966e-005056b91333";
            
            $sql='INSERT INTO aplicaciones_was.apps (id, idResponsable, nombre, ambiente, plataforma) 
                VALUES (UUID(), :idResponsable, :nombre, :ambiente, :plataforma);';
            $param= array(':nombre'=>$this->nombre,':ambiente'=>$this->ambiente,':plataforma'=>$this->plataforma,
                ':idResponsable'=>$this->idResponsable);  
            $data = DATA::Ejecutar($sql,$param);   
            
            if($data){
                return $data;
            }
            else {
                return false;
            }

        }     
        catch(Exception $e) {
            error_log("[ERROR]  (".$e->getCode()."): ". $e->getMessage());
            header('HTTP/1.0 400 Bad error');
            die(json_encode(array(
                'code' => $e->getCode() ,
                'msg' => 'Error al cargar la lista'))
            );
        }
    }
    
    function Delete(){
        try {            
            $sql='DELETE FROM aplicaciones_was.apps
            WHERE id = :id;';
            $param= array(':id'=>$this->id);  
            $data = DATA::Ejecutar($sql,$param);
        }     
        catch(Exception $e) {
            error_log("[ERROR]  (".$e->getCode()."): ". $e->getMessage());
            header('HTTP/1.0 400 Bad error');
            die(json_encode(array(
                'code' => $e->getCode() ,
                'msg' => 'Error al cargar la lista'))
            );
        }
    }

    function ReadAppByid(){
        try {            
            $sql='SELECT id, idResponsable, nombre, ambiente, plataforma FROM aplicaciones_was.apps
            WHERE id = :id;';
            $param= array(':id'=>$this->id);  
            $data = DATA::Ejecutar($sql,$param);
            return $data;
        }     
        catch(Exception $e) {
            error_log("[ERROR]  (".$e->getCode()."): ". $e->getMessage());
            header('HTTP/1.0 400 Bad error');
            die(json_encode(array(
                'code' => $e->getCode() ,
                'msg' => 'Error al cargar la lista'))
            );
        }
    }
    
    

}

?>