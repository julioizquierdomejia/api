<?php
namespace App\Model;

use App\Lib\Database;
use App\Lib\Response;

class AdminModel extends GeneralConfig
{
    private $db;
    private $table_menu = 'admin_menu'; 
    
    public function __CONSTRUCT()
    {
        $this->dbpe = Database::StartUpArea( $this->bd_base_pe );
        $this->response = new Response();
    }
    
    public function GetAllMenu()
    {
		try
		{
			$result = array();

			$stm = $this->dbpe->prepare("SELECT * FROM $this->table_menu");
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

			$stm = $this->dbpe->prepare("SELECT * FROM $this->table_menu WHERE id = ?");
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

    
}