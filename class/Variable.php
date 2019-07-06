<?php
//ACTION
if( isset($_POST["action"])){        
    $opt= $_POST["action"];
    unset($_POST['action']);    
    // Classes
    require_once("Conexion.php");
    // 
    // Instance
    $c_Variable = new C_Variable();
    switch($opt){
        case "ReadAll":
            echo json_encode($c_Variable->ReadAll());
            break;
        case "CargarVariablesbyIDApp":
            echo json_encode($c_Variable->CargarVariablesbyIDApp());
            break;
        case "UpdateVariables":
            echo $c_Variable->UpdateVariables();
            break;
    }
}

class C_Variable{
    public $id="";   
    public $nombre="";   
    public $valor="";   
    public $tipo=""; 
    public $descripcion="";


    function __construct(){

        if(isset($_POST["id"])){
            $this->id= $_POST["id"];
        }
        
        if(isset($_POST["obj"])){
            $obj= json_decode($_POST["obj"],true);
            $this->id=$obj["id"] ?? NULL;   
            $this->variables = $obj["variables"];
            $this->nombre=$obj["nombre"] ?? NULL;   
            $this->valor=$obj["valor"] ?? NULL;   
            $this->tipo=$obj["tipo"] ?? NULL;  
            $this->descripcion=$obj["descripcion"] ?? NULL;
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

    function CargarVariablesbyIDApp(){
        try {
            $sql="SELECT va.id, va.idAPP, ap.nombre nombreApp, va.nombre, va.valor, va.tipo, va.descripcion from variableXAPP va
            INNER JOIN apps ap
            ON ap.id = va.idAPP
            WHERE idAPP = :idApp;";
            $param= array(':idApp'=>$this->id);  
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

    function UpdateVariables(){
        try {
            $sql="DELETE FROM aplicaciones_was.variableXAPP WHERE idAPP = :idAPP;";
            $param= array(':idAPP'=>$this->id);  
            $data = DATA::Ejecutar($sql,$param); 
            
            foreach ( $this->variables as $itemVariable) {
                $sql="INSERT INTO aplicaciones_was.variableXAPP (id, idAPP, nombre, valor, tipo, descripcion)
                    VALUES (UUID(), :idAPP, :nombre, :valor, :tipo, :descripcion);";
                $param= array(':idAPP'=>$this->id,':nombre'=>$itemVariable["nombre"],
                    ':valor'=>$itemVariable["valor"], ':tipo'=>$itemVariable["tipo"],
                    ':descripcion'=>$itemVariable["descripcion"]);  
                $data = DATA::Ejecutar($sql,$param); 
            }
            return true;

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