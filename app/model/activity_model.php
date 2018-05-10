<?php
namespace App\Model;

use App\Lib\Database;
use App\Lib\Response;

class ActivityModel
{
    private $db;
    private $table = 'activity';
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

    public function byBook($id_book)
    {
        try
        {
            $result = array(); 
            $stm = $this->db->prepare("SELECT * FROM $this->table WHERE id_book = ?"); 
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

    public function byUnity($id_unity)
    {
        try
        {
            $result = array(); 
            $stm = $this->db->prepare("SELECT * FROM $this->table WHERE id_unity = ?"); 
            $stm->execute(array($id_unity));

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
        if($data == null)
        {
            $this->response->setResponse(false, "Error, no data");
            return $this->response;
        }
        else
        {
            $code = uniqid();
            try 
            {
                if(isset($data['id']))
                {
                    $sql = "UPDATE $this->table SET 
                                code        = ?,
                                name        = ?, 
                                description = ?,
                                id_book     = ?,
                                id_unity    = ?,
                                page        = ?,
                                side        = ?,
                                updated     = ?
                            WHERE id = ?";
                    
                    $this->db->prepare($sql)
                         ->execute(
                            array(
                                $code,
                                $data['name'], 
                                $data['description'],
                                $data['id_book'],
                                $data['id_unity'],
                                $data['page'],
                                $data['side'],  
                                date('Y-m-d G:H:i'),
                                $data['id']
                            )
                        );
                }
                else
                {
                    $sql = "INSERT INTO $this->table
                                (code, name, description, id_book, id_unity, page, side, inserted)
                                VALUES (?, ?,?,?,?,?,?,?)";
                    
                    $this->db->prepare($sql)
                         ->execute(
                            array(
                                $code,
                                $data['name'], 
                                $data['description'],
                                $data['id_book'],
                                $data['id_unity'],
                                $data['page'],
                                $data['side'],  
                                date('Y-m-d G:H:i')
                            )
                        ); 
                }
 
                $this->response->result = array('id' =>  $this->db->lastInsertId(),'code' => $code );;
                $this->response->setResponse(true);
                return $this->response;
            }catch (Exception $e) 
            {
                $this->response->setResponse(false, $e->getMessage());
            }
        } 
    }

    public function saveJson($filePath,$data)
    {
        if( file_put_contents($filePath, $data) != false ){ 
            $this->response->setResponse(true);
            return $this->response;
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