<?php
namespace App\Model;

use App\Lib\Database;
use App\Lib\Response;

class AdminModel extends GeneralConfig
{
    private $db; 
    private $dbpeTemp;
    private $friendClass;
    
    public function __CONSTRUCT($token_data = array())
    {
        $this->dbpe = Database::StartUpArea( $this->bd_base_pe ); 
        $this->response = new Response();
        $this->token_data = $token_data;
    }
    
    public function GetAllMenu()
    {
		try
		{
			$result = array();

			$stm = $this->dbpe->prepare("SELECT * FROM $this->table_admin_menu");
			$stm->execute();
            
			$this->response->setResponse(true);
            $this->response->result = $stm->fetchAll();
            
            return $this->response;
		}
		catch(Exception $e)
		{
			$this->response->setResponse(false, $e->getMessage());
            return $this->response;
		}
    }
    
    public function GetMenu($id)
    {
		try
		{
			$result = array();

			$stm = $this->dbpe->prepare("SELECT * FROM $this->table_admin_menu WHERE id = ?");
			$stm->execute(array($id));

			$this->response->setResponse(true);
            $this->response->result = $stm->fetch();
            
            return $this->response;
		}
		catch(Exception $e)
		{
			$this->response->setResponse(false, $e->getMessage());
            return $this->response;
		}  
    }

    public function GetAllGroupMenu()
    {
		try
		{
			$result = array();

			$stm = $this->dbpe->prepare("SELECT * FROM $this->table_group_admin_menu");
			$stm->execute();
            
			$this->response->setResponse(true);
            $this->response->result = $stm->fetchAll();
            
            return $this->response;
		}
		catch(Exception $e)
		{
			$this->response->setResponse(false, $e->getMessage());
            return $this->response;
		}
    }

    public function GetGroupMenu($id)
    {
		try
		{
			$result = array();

			$stm = $this->dbpe->prepare("SELECT * FROM $this->table_group_admin_menu WHERE id = ?");
			$stm->execute(array($id));

			$this->response->setResponse(true);
            $this->response->result = $stm->fetch();
            
            return $this->response;
		}
		catch(Exception $e)
		{
			$this->response->setResponse(false, $e->getMessage());
            return $this->response;
		}  
    }

    public function GetAllTablesMantenience(){
    	try
		{
			$result = array();

			$stm = $this->dbpe->prepare("SELECT * FROM $this->table_tables_mantenience order by name");
			$stm->execute();
            
			$this->response->setResponse(true);
            $this->response->result = $stm->fetchAll();
            
            return $this->response;
		}
		catch(Exception $e)
		{
			$this->response->setResponse(false, $e->getMessage());
            return $this->response;
		}
    }

    public function GetTableMantenience($id, $apiResponse = false)
    {
		try
		{
			$result = array();

			$stm = $this->dbpe->prepare("SELECT * FROM $this->table_tables_mantenience WHERE id = ?");
			$stm->execute(array($id)); 

            if($apiResponse){
            	return $stm->fetch();
            }else{
            	$this->response->setResponse(true);
            	$this->response->result = $stm->fetch();
            	return $this->response;
            } 
            
		}
		catch(Exception $e)
		{

            if($apiResponse){
            	return false;
            }else{
            	$this->response->setResponse(false, $e->getMessage());
            	return $this->response;
            } 
		}  
    }

    public function GetTableMantenienceByCode($code, $apiResponse = true)
    {
		try
		{
			$result = array();

			$stm = $this->dbpe->prepare("SELECT * FROM $this->table_tables_mantenience WHERE code = ?");
			$stm->execute(array($code)); 

            if(!$apiResponse){
            	return $stm->fetch();
            }else{
            	$this->response->setResponse(true);
            	$this->response->result = $stm->fetch();
            	return $this->response;
            } 
            
		}
		catch(Exception $e)
		{ 
            if(!$apiResponse){
            	return false;
            }else{
            	$this->response->setResponse(false, $e->getMessage());
            	return $this->response;
            } 
		}  
    }

    public function GetTableData($code)
    {
    	try
		{
			$result = array();

			$infoTable = $this->GetTableMantenienceByCode($code, false); 

			$tables_m = array("book", "series");
			if (in_array($infoTable->table, $tables_m)) {  
				$this->dbpeTemp = Database::StartUpArea('data');  
	            $stm = $this->dbpeTemp->prepare("SELECT * FROM $infoTable->table order by $infoTable->field_order");
	            $stm->execute(); 
 
	            $strippedFE = preg_replace('/\s+/', '', $infoTable->fields_edit );
	            $strippedFP = preg_replace('/\s+/', '', $infoTable->fields_preview );
	            $this->response->result = array("data" => $stm->fetchAll(), "fields_edit" => $strippedFE, "fields_preview" => $strippedFP);
	            $this->response->setResponse(true);
			}else{
				$this->response->setResponse(false);
			} 
            
            return $this->response; 
		}
		catch(Exception $e)
		{
			$this->response->setResponse(false, $e->getMessage());
            return $this->response;
		}  
    }


    public function GetTableDataEdit($code, $id){
    	if( trim( $code ) == 'libros' ){ 
    		$this->friendClass = new BookModel($this->token_data); 
    		return $this->friendClass->Get($id);
    	}
    }

    public function InsertOrUpdate($data){ 
    	if( trim( $data["tableName"] ) == 'libros' ){ 
    		$this->friendClass = new BookModel($this->token_data); 
    		return $this->friendClass->InsertOrUpdate( $data["data"] );
    		var_dump($data);
    	}
    } 

    public function fileExists($type, $code,  $name){
        $types_p = array("unidad", "cover", "cover3d", "sesion");
        if (in_array($type, $types_p)) {  
            $final_path = $this->getPathMedia($type, $code) . $name; 
            $band = file_exists($final_path);
            if( $band ){ 
                $this->response->result = array("size" => $this->human_filesize( filesize($final_path) ) );
            }
            $this->response->setResponse( $band );
        }else{
            $this->response->setResponse( false );
        } 
        return $this->response;
    }

    private function getPathMedia($type, $code = ''){
        $final_path = '';
        if($type === 'unidad'){
            $final_path = $this->path_units . $code . '/';
        }
        else if($type === 'cover'){
            $final_path = $this->path_covers;
        }
        else if($type === 'cover3d'){
            $final_path = $this->path_covers3d;
        }

        return $final_path;
    }

    public function human_filesize($bytes, $decimals = 2) {
        $factor = floor((strlen($bytes) - 1) / 3);
        if ($factor > 0) $sz = 'KMGT';
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor - 1] . 'B';
    }

    public function uploadMedia($data){
       /* var_dump($data); 
        var_dump($_FILES);
  */

        if($data == null)
        {
            $this->response->setResponse(false, "Error, no data");
            return $this->response;
        }
        else
        {   
            try 
            {   
                //var_dump("entra 0");
                $myFiles = $_FILES['file'];  
                $code = $data["code"];
                $type = $data["type"];
                $name = $data["name"];
                $number = $data["number"];

                $total_upload = 0;
                $folder = $this->getPathMedia($type, $code);
                $final_path = $folder;

                if (!file_exists($folder)) {
                    mkdir($folder, 0777, true);
                }   

                $tmp_name = $myFiles['tmp_name']; 
                $name_file = pathinfo($myFiles['name']);  
                
                $ext = $name_file['extension'];
                $codeu = uniqid();
                $final_path = $folder . $name;
                if( move_uploaded_file( $tmp_name, $final_path ) != false ){   
                    $this->response->result = array("path" => $final_path);
                    $this->response->setResponse(true); 
                } else {
                    $this->response->setResponse(false, 'Error data format '); 
                }  
                return $this->response;
                      
            }
            catch (Exception $e) 
            { 
                $this->response->setResponse(false, $e->getMessage());
            }
        } 
    } 

    
}