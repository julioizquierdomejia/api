<?php
namespace App\Model;

use App\Lib\Database;
use App\Lib\Response;

class GradeModel extends GeneralConfig
{
    private $db;
    private $table = 'grade'; 
    
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

			$stm = $this->db->prepare("SELECT g.id, g.name, ss.name stage, ss.id id_studystage FROM $this->table g INNER JOIN $this->table_studystage ss on g.id_studystage = ss.id");
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
			$stm = $this->db->prepare("SELECT g.id, g.name, ss.name stage, ss.id id_studystage FROM $this->table g INNER JOIN $this->table_studystage ss on g.id_studystage = ss.id WHERE id = ?");
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

    public function byStage($id_studystage)
    {
        try
        {
            $result = array(); 
            $stm = $this->db->prepare("SELECT g.id, g.name, ss.name stage, ss.id id_studystage FROM $this->table g INNER JOIN $this->table_studystage ss on g.id_studystage = ss.id WHERE id_studystage = ?"); 
            $stm->execute(array($id_studystage));

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

    public function bySerie($id_serie)
    {
        try
        {
            $result = array();  
            $stm = $this->db->prepare("SELECT g.id, g.name, s.id id_studystage, s.name name_serie, ss.name name_studystage 
                                        FROM $this->table g 
                                        INNER JOIN $this->table_serie s on g.id_studystage = s.id_stage
                                        INNER JOIN $this->table_studystage ss on g.id_studystage = ss.id
                                        WHERE s.id = ?"); 
            $stm->execute(array($id_serie));

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
    
    public function InsertOrUpdate($data)
    {
		try 
		{
            if(isset($data['id']))
            {
                $sql = "UPDATE $this->table SET  
                            name            = ?, 
                            id_studystage   = ?,
                            updated         = ?
                        WHERE id = ?";
                
                $this->db->prepare($sql)
                     ->execute(
                        array( 
                            $data['name'], 
                            $data['id_studystage'], 
                            date('Y-m-d G:H:i')
                        )
                    );
            }
            else
            {
                $sql = "INSERT INTO $this->description
                            (name, id_studystage, inserted)
                            VALUES (?,?,?)";
                
                $this->db->prepare($sql)
                     ->execute(
                        array( 
                            $data['name'], 
                            $data['id_studystage'], 
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