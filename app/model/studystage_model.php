<?php
namespace App\Model;

use App\Lib\Database;
use App\Lib\Response;

class StudystageModel
{
    private $db;
    private $table = 'studystage';
    private $response;
    
    public function __CONSTRUCT()
    {
        $this->db = Database::StartUp();
        $this->response = new Response();
    }
    
    public function GetAll()
    {
		try
		{
			$result = array();

			$stm = $this->db->prepare("SELECT * FROM $this->table order by order_number asc");
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

			$stm = $this->db->prepare("SELECT * FROM $this->table WHERE id = ?");
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

    public function byCode($code)
    {
        try
        {
            $result = array();

            $stm = $this->db->prepare("SELECT * FROM $this->table WHERE code = ?");
            $stm->execute(array($code));

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
    
    public function InsertOrUpdate($data)
    {
		try 
		{
            if(isset($data['id']))
            {
                $sql = "UPDATE $this->table SET  
                            name            = ?,
                            order_number           = ?,
                            updated         = ?
                        WHERE id = ?";
                
                $this->db->prepare($sql)
                     ->execute(
                        array( 
                            $data['name'],
                            $data['order_number'], 
                            date('Y-m-d G:H:i')
                        )
                    );
            }
            else
            {
                $sql = "INSERT INTO $this->description
                            (name, order_number, inserted)
                            VALUES (?,?,?)";
                
                $this->db->prepare($sql)
                     ->execute(
                        array( 
                            $data['name'], 
                            $data['order_number'], 
                            date('Y-m-d G:H:i')
                        )
                    ); 
            }
            
			$this->response->setResponse(true);
            return $this->response;
		}catch (Exception $e) 
		{
            $this->response->setResponse(false, $e->getMessage());
		}
    }
    
    public function Delete($id)
    {
		try 
		{
			$stm = $this->db
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