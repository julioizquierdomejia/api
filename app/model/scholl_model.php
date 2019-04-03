<?php
namespace App\Model;

use App\Lib\Database;
use App\Lib\Response;

class SchollModel extends GeneralConfig
{
    private $table; 
    private $logger;


    private $dbmaster;
    private $dbpe;
    
    public function __CONSTRUCT()
    {
        $this->dbmaster = Database::StartUpMaster();
        $this->response = new Response();

        $this->table = $this->table_scholls; 
    }

    
    public function GetAll()
    {
        try
        {
            $result = array();

            $stm = $this->dbmaster->prepare("SELECT * FROM $this->table");
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
    
    public function Get($id)
    {
		try
		{
			$result = array(); 
			$stm = $this->dbmaster->prepare("SELECT * FROM $this->table WHERE id = ?");
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
    
    public function Delete($id)
    {
		try 
		{
			$stm = $this->dbmaster
			            ->prepare("DELETE FROM $this->table WHERE id = ?");			          

			$stm->execute(array($id));
            
			$this->response->setResponse(true);
            return $this->response;
		} catch (Exception $e) 
		{
			$this->response->setResponse(false, $e->getMessage());
		}
    }
}