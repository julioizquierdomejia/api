<?php
namespace App\Model;

use App\Lib\Database;
use App\Lib\Response;

class UnityModel
{
    private $db;
    private $table = 'unity';
    private $table_book  = 'book';
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

			$stm = $this->db->prepare("SELECT * FROM $this->table");
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

    public function GetDemo($id_book)
    { 
        try
        {
            $result = array();

            $stm = $this->db->prepare("SELECT * FROM $this->table u inner join $this->table_book b on u.id_book = b.id WHERE u.id_book = ? and u.number = b.unity_demo"); 

            $stm->execute(array($id_book));

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

    public function byBook($id_book)
    {
        try
        {
            $result = array(); 
            $stm = $this->db->prepare("SELECT * FROM $this->table WHERE id_book = ? order by number"); 
            $stm->execute(array($id_book));

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

    public function byNumber($id_book, $number)
    {
        try
        {
            $result = array(); 
            $stm = $this->db->prepare("SELECT * FROM $this->table WHERE id_book = ? and number = ? "); 
            $stm->execute(array($id_book, $number));

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
                            code            = ?, 
                            name            = ?,
                            description     = ?,
                            id_book         = ?,
                            pages_number    = ?,
                            updated         = ?
                        WHERE id = ?";
                
                $this->db->prepare($sql)
                     ->execute(
                        array(
                            $data['code'], 
                            $data['name'],
                            $data['description'],
                            $data['id_book'],
                            $data['pages_number'],
                            $data['id'],
                            date('Y-m-d G:H:i')
                        )
                    );
            }
            else
            {
                $sql = "INSERT INTO $this->description
                            (code, name, description, id_book, pages_number, inserted)
                            VALUES (?,?,?,?,?,?)";
                
                $this->db->prepare($sql)
                     ->execute(
                        array(
                            $data['code'], 
                            $data['name'],
                            $data['description'],
                            $data['id_book'],
                            $data['pages_number'],
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