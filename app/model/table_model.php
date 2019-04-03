<?php
namespace App\Model;

use App\Lib\Database;
use App\Lib\Response;

class TableModel
{
    private $db; 
    private $response;
    
    public function __CONSTRUCT()
    {
        $this->db = Database::StartUp();
        $this->response = new Response();
    }
    
    public function Get($table, $par = false, $filter = false)
    {
		try
		{
			$result = array();   
            $list = ['sesiones_word', 'scholls_web', 'company_info'];
            if(in_array($table, $list)){
                if($par != false && $filter != false){
                    $stm = $this->db->prepare("SELECT * FROM $table where $par = '$filter' order by order_number");
                }else{
                    $stm = $this->db->prepare("SELECT * FROM $table order by order_number");
                } 
                $stm->execute();
                
                $this->response->setResponse(true);
                $this->response->result = $stm->fetchAll(); 
                return $this->response;
            }else{
                $this->response->setResponse(false);
                return $this->response;
            }  
		}
		catch(Exception $e)
		{
			$this->response->setResponse(false, $e->getMessage());
            return $this->response;
		}
    }
    
     
}