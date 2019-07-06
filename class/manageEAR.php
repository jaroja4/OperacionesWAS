<?php
//ACTION
if( isset($_GET["action"])){        
    $opt= $_POST["action"];
    unset($_POST['action']);
    // Classes
    require_once("Conexion.php");
    // 
    // Instance
    $makeEar = new MakeEar();
    switch($opt){
        case "Create":
            echo json_encode($makeEar->Create());
            break;
    }
}

$makeEar = new MakeEar();
require_once("Conexion.php");
// $makeEar->unzipEar["fileName"] = "GECOGESTIONcer.ear";
// $makeEar->Create();

class makeEar{

    public $unzipEar = array(
        "fileName" => "",
        "extension" => "",
        "baseName" => "",
        "path" => "",
        "toPath" => ""
    );

    public $unzipWar = array(
        "fileName" => "",
        "extension" => "",
        "baseName" => "",
        "path" => "",
        "toPath" => "",
        "web_xml" => "",
    );

    function __construct(){

        if(isset($_POST["fileName"])){
            $this->unzipEar["fileName"];
        }
        if(isset($_POST["id"])){
            $this->id= $_POST["id"];
        }
        if(isset($_POST["obj"])){
            $obj= json_decode($_POST["obj"],true);
            $this->id=$obj["id"] ?? NULL;   
        }
        if (isset($_FILES['files'])) {
            $path = '..'.DIRECTORY_SEPARATOR.'ear'.DIRECTORY_SEPARATOR;
    
            $file_name = $_FILES['files']['name'][0];
            $file_tmp = $_FILES['files']['tmp_name'][0];
            $file_type = $_FILES['files']['type'][0];
            $file_size = $_FILES['files']['size'][0];
            $file_ext = explode('.', $_FILES['files']['name'][0]);
            $file_ext = strtolower(end($file_ext));
    
            $file = $path . $file_name;
    
            if ($file_size > 3007152) {
                echo 'File size exceeds limit: ' . $file_name . ' ' . $file_type;
            }
            move_uploaded_file($file_tmp, $file);
        }
    }

    function Create(){
        try {
            $this->unzipEar["extension"] = pathinfo($this->unzipEar["fileName"], PATHINFO_EXTENSION);
            $this->unzipEar["baseName"] = basename($this->unzipEar["fileName"], '.'.$this->unzipEar["extension"]);  
            $this->unzipEar["path"] = "..".DIRECTORY_SEPARATOR."ear".DIRECTORY_SEPARATOR;
            $this->unzipEar["pathFile"] = $this->unzipEar["path"].$this->unzipEar["fileName"];
            $this->unzipEar["toPath"] = __DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."ear".DIRECTORY_SEPARATOR.$this->unzipEar["baseName"];

            $this->unZipEar();
            // $this->unZip();
            $this->delete_env();
            $this->add_env();
            $this->updateWar();
            // $this->zipWar();
            $this->zipEar();

            // $this->unZip();
            // $this->deleteDirectory($this->unzipEar["path"].$this->unzipEar["baseName"]);
            // "..\ear\ARCOcer\ArcoWeb-1.0.3"
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

    function unZip(){
        try {
            $xmlZip = new ZipArchive();
            if ($xmlZip->open($this->unzipEar["pathFile"]) === true) {
                $xmlZip->extractTo($this->unzipEar["toPath"]);
        
                for ($i = 0; $i < $xmlZip->numFiles; $i++) {
                    $entry = $xmlZip->getNameIndex($i);
                    $file_type = substr($entry, strrpos($entry, '.') - strlen($entry) + 1);
                    if ("war" == $file_type) {
                        echo "El archivo encontrado es: ".$entry."<br>";
                        $fileBaseName = basename($entry, '.war'); 
                        if (!file_exists($this->unzipEar["toPath"].DIRECTORY_SEPARATOR.$fileBaseName)) {
                            mkdir($this->unzipEar["toPath"].DIRECTORY_SEPARATOR.$fileBaseName, 0777, true);
                        }
                        $this->unzipWar["path"] = $this->unzipEar["toPath"].DIRECTORY_SEPARATOR.$fileBaseName;
                        $this->unzipWar["baseName"] = $fileBaseName;

                        $zip = new ZipArchive;
                        if ($zip->open($this->unzipEar["toPath"].DIRECTORY_SEPARATOR.$entry) === TRUE) {

                            $zip->deleteName('WEB-INF/web.xml');

                            $zip->addFile($this->unzipEar["path"].'test.txt', 'WEB-INF/web.xml');
                            
                            echo 'ok';
                            $zip->close();
                        } else {
                            echo 'failed';
                        }
                    }
                }
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

    function unZipEar(){
        try {
            $xmlZip = new ZipArchive();
            if ($xmlZip->open($this->unzipEar["pathFile"]) === true) {
                $xmlZip->extractTo($this->unzipEar["toPath"]);
        
                for ($i = 0; $i < $xmlZip->numFiles; $i++) {
                    $entry = $xmlZip->getNameIndex($i);
                    $file_type = substr($entry, strrpos($entry, '.') - strlen($entry) + 1);
                    if ("war" == $file_type) {
                        echo "El archivo encontrado es: ".$entry."<br>";
                        $fileBaseName = basename($entry, '.war'); 
                        if (!file_exists($this->unzipEar["toPath"].DIRECTORY_SEPARATOR.$fileBaseName)) {
                            mkdir($this->unzipEar["toPath"].DIRECTORY_SEPARATOR.$fileBaseName, 0777, true);
                        }
                        $this->unzipWar["path"] = $this->unzipEar["toPath"].DIRECTORY_SEPARATOR.$fileBaseName;
                        $this->unzipWar["baseName"] = $fileBaseName;
                        $war = new ZipArchive();
                        if ($war->open($this->unzipEar["toPath"].DIRECTORY_SEPARATOR.$entry) === true) {
                            $this->unzipWar["fileName"] = $entry;
                            $war->extractTo($this->unzipEar["toPath"].DIRECTORY_SEPARATOR.$fileBaseName);
                            $this->unzipWar["web_xml"] = $this->unzipEar["toPath"].DIRECTORY_SEPARATOR.$fileBaseName.DIRECTORY_SEPARATOR."WEB-INF".DIRECTORY_SEPARATOR."web.xml";
                        }
                        $war->close();
                    }
                }
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

    function updateWar(){
        try {
            $zip = new ZipArchive;
            
            if ($zip->open($this->unzipEar["toPath"].DIRECTORY_SEPARATOR.$this->unzipWar["fileName"]) === TRUE) {
                $zip->deleteName('WEB-INF/web.xml');
                $zip->addFile($this->unzipWar["web_xml"], 'WEB-INF/web.xml');
                $zip->close();  
                $dir = $this->unzipEar["path"].$this->unzipEar["baseName"].DIRECTORY_SEPARATOR.$this->unzipWar["baseName"].DIRECTORY_SEPARATOR;
                $this->deleteDirectory($dir);
            } else {
                echo 'failed';
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
    
    function delete_env(){
        try {
            $dom=new DOMDocument();
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            $dom->load($this->unzipWar["web_xml"]);                    
            $root=$dom->documentElement; // This can differ (I am not sure, it can be only documentElement or documentElement->firstChild or only firstChild)
            $nodesToDelete=array();
            
            $markers=$root->getElementsByTagName('env-entry');
            
            // Loop trough childNodes
            foreach ($markers as $marker) {
                $name=$marker->getElementsByTagName('env-entry-name')->item(0)->textContent;
                $type=$marker->getElementsByTagName('env-entry-type')->item(0)->textContent;
                $value=$marker->getElementsByTagName('env-entry-value')->item(0)->textContent;
                $value=$marker->getElementsByTagName('description')->item(0)->textContent;
                // To remove the marker you just add it to a list of nodes to delete
                $nodesToDelete[]=$marker;
            }     

            // You delete the nodes
            foreach ($nodesToDelete as $node) $node->parentNode->removeChild($node);
            
            $dom->save($this->unzipWar["web_xml"]); // This saves the XML to a file
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

    function add_env(){
        try {
            $variables= $this->loadVariables();

            $dom=new DOMDocument();
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            $dom->load($this->unzipWar["web_xml"]); 

            if ( $variables != false){
                foreach ($variables as $variable) {
                    $env  = $dom->createElement('env-entry');
                    if ( isset($variable["descripcion"]) )
                        $description = $dom->createElement('description',$variable["descripcion"]);
                    $name = $dom->createElement('env-entry-name',$variable["nombre"]);
                    $type = $dom->createElement('env-entry-type',$variable["tipo"]);
                    $value = $dom->createElement('env-entry-value',$variable["valor"]??NULL);
                    
                    if ( isset($variable["descripcion"]) )
                        $env->appendChild($description);
                    $env->appendChild($name);
                    $env->appendChild($type);
                    $env->appendChild($value);

                    $main=$dom->getElementsByTagName('web-app');
                    foreach ($main as $item) {                        
                        $item->appendChild( $env );                        
                    }

                    $dom->save($this->unzipWar["web_xml"]); // This saves the XML to a file
                }   
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

    function loadVariables(){
        try {
            $sql='SELECT vp.nombre, vp.valor, vp.tipo, vp.descripcion
            FROM variableXAPP vp
            INNER JOIN apps ap
            ON vp.idAPP = ap.id
            WHERE ap.nombre = :baseName;';
            $param= array(':baseName'=>$this->unzipEar["baseName"]);  
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

    function zipEar(){
        try {
            $zipEar = new ZipArchive();
            $dir = $this->unzipEar["path"].$this->unzipEar["baseName"].DIRECTORY_SEPARATOR;
            // $dir = '..'.DIRECTORY_SEPARATOR.'ear'.DIRECTORY_SEPARATOR.'SPMcer'.DIRECTORY_SEPARATOR;
            $rutaFinal = "..".DIRECTORY_SEPARATOR."ear".DIRECTORY_SEPARATOR."EAR_LISTO";

            if(!file_exists($rutaFinal)){
                mkdir($rutaFinal);
            }

            $archivoZip = $this->unzipEar["fileName"];

            if ($zipEar->open($rutaFinal.DIRECTORY_SEPARATOR.$archivoZip, ZIPARCHIVE::CREATE) === true) {
                $this->agregar_zip($dir, $zipEar, false);
                $zipEar->close();
                if (file_exists($rutaFinal.DIRECTORY_SEPARATOR. $archivoZip)) {
                    echo "Proceso Finalizado!! <br/><br/>";
                } else {
                    echo "Error, archivo zip no ha sido creado!!";
                }
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

    function zipWar(){
        try {
            $zipWar = new ZipArchive();
            $dir = $this->unzipEar["path"].$this->unzipEar["baseName"].DIRECTORY_SEPARATOR.$this->unzipWar["baseName"].DIRECTORY_SEPARATOR;
            $rutaFinal = $this->unzipWar["path"] .DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR;

            $archivoZip = $this->unzipWar["fileName"];
            unlink($this->unzipWar["path"].".war");

            if(!file_exists($rutaFinal)){
                mkdir($rutaFinal);
            }

            if ($zipWar->open($rutaFinal.$archivoZip, ZIPARCHIVE::CREATE) === true) {
                $this->agregar_zip($dir, $zipWar);
                $zipWar->close();
                if (file_exists($rutaFinal.DIRECTORY_SEPARATOR. $archivoZip)) {
                    echo "Proceso Finalizado!! <br/><br/>";
                    $dir = $this->unzipEar["path"].$this->unzipEar["baseName"].DIRECTORY_SEPARATOR.$this->unzipWar["baseName"];
                    $this->deleteDirectory($dir);

                } else {
                    echo "Error, archivo zip no ha sido creado!!";
                }
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

    function agregar_zip($dir, $zip, $war=true) {
        try {
            if (is_dir($dir)) {
                if ($da = opendir($dir)) {
                    while (($archivo = readdir($da)) !== false) {
                        if (is_dir($dir .DIRECTORY_SEPARATOR. $archivo) && $archivo != "." && $archivo != "..") {
                            $this->agregar_zip($dir.$archivo.DIRECTORY_SEPARATOR, $zip, $war);
                            echo "<strong>Creando directorio: $dir$archivo</strong><br/>";
                        } 
                        elseif (is_file($dir . $archivo) && $archivo != "." && $archivo != "..") {
                            if ($war){
                                $rutaEnZip = str_replace( ($this->unzipEar["path"].$this->unzipEar["baseName"].DIRECTORY_SEPARATOR.$this->unzipWar["baseName"].DIRECTORY_SEPARATOR) , "", $dir);
                                $rutaEnZip = str_replace('\\', '/', $rutaEnZip);
                                $zip->addFile($dir . $archivo, $rutaEnZip . $archivo);
                            }else{
                                $rutaEnZip = str_replace( ($this->unzipEar["path"].$this->unzipEar["baseName"].DIRECTORY_SEPARATOR) , "", $dir);
                                $rutaEnZip = str_replace('\\', '/', $rutaEnZip);
                                $zip->addFile($dir . $archivo, $rutaEnZip . $archivo);
                            }
                            echo "Agregando archivo: $rutaEnZip$archivo <br/>";
                        }
                    }
                    closedir($da);
                }
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

    function deleteDirectory($dirname) {
        try {
            if (is_dir($dirname))
                $dir_handle = opendir($dirname);
            if (!$dir_handle)
                return false;
            while($file = readdir($dir_handle)) {
                if ($file != "." && $file != "..") {
                    if (!is_dir($dirname.DIRECTORY_SEPARATOR.$file))
                        unlink($dirname.DIRECTORY_SEPARATOR.$file);
                    else
                        $this->deleteDirectory($dirname.DIRECTORY_SEPARATOR.$file);
                }
            }
            closedir($dir_handle);
            rmdir($dirname);
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