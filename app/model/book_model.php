<?php
namespace App\Model;

use App\Lib\Database;
use App\Lib\Response;
use App\Model;

class BookModel extends GeneralConfig
{
    private $db;
    private $table; 
    
    public function __CONSTRUCT($token_data = array())
    {
        $this->db = Database::StartUp();
        $this->dbmaster = Database::StartUpMaster();
        $this->response = new Response();
        $this->token_data = $token_data;

        $this->table = $this->table_book;
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

    public function GetRegistered()
    {
        try
        {
            $resultGlobal = array();
            $result = array();
            $id_user_master = $this->token_data->idm;
            $stm = $this->dbmaster->prepare("SELECT id_book, id_book_group, date_expired FROM $this->table_book_code WHERE id_user_join = ? and id_status = 2");
            $stm->execute(array($id_user_master));
            $result = $stm->fetchAll(); 

            if( count($result) > 0){
                foreach ($result as $row) { 
                    $resultBookGroup = $this->getBooksFromGroup($row->id_book_group);                    
                    $id_book_groups = $resultBookGroup[0]->id_book_links;  

                    $resultGlobal = $this->getBookDataRel($id_book_groups);
                    $resultGlobal["books_group"] = $resultBookGroup;
                }
            } 

            $this->response->result = $resultGlobal;
            $this->response->setResponse(true);            
            return $this->response;
        }
        catch(Exception $e)
        {
            $this->response->setResponse(false, $e->getMessage());
            return $this->response;
        }  
    }

    public function CheckCode($code)
    {
        try
        {
            //var_dump($code);
            $result = array();
            $stm = $this->dbmaster->prepare("
                SELECT id, code, id_book, id_book_group, id_status 
                FROM $this->table_book_code 
                WHERE code = ? and enabled = 1"); 
            $stm->execute(array($code));
            $result = $stm->fetchAll();

            if( count($result) > 0 )
            {
                $detail = $this->getBooksDetailsFromGroup($result[0]->id_book_group, false);
                $this->response->result = $detail;
                $this->response->setResponse(true); 
            }else{
                $this->response->setResponse(false); 
            } 
            return $this->response;
        }
        catch (Exception $e) 
        {
            return $this->response;
        }
    }

    public function getBooksDetailsFromGroup($id_book_group, $json = true)
    { 
        try
        {  
            $resultBookGroup = $this->getBooksFromGroup($id_book_group);     
            $id_book_groups = $resultBookGroup[0]->id_book_links;  

            $resultGlobal = $this->getBookDataRel($id_book_groups);
            $resultGlobal["books_group"] = $resultBookGroup;

            if($json){
                $this->response->result = $resultGlobal;
                $this->response->setResponse(true);   
                return $this->response;
            }else{
                return $resultGlobal;
            }

           
        }
        catch(Exception $e)
        {
            return array();
        }
    }

    private function getBooksFromGroup($id_book_group)
    {
        try
        { 
            $stm = $this->dbmaster->prepare("SELECT id, code, name, id_book_links FROM $this->table_book_group WHERE id = ?"); 
            $stm->execute(array($id_book_group)); 
            $resultBookGroup = $stm->fetchAll();
            return $resultBookGroup;
        }
        catch(Exception $e)
        {
            return array();
        }
    }

    private function getBookDataRel($id_book_groups)
    {

        

        $globalData = array("series" => array(), "studystages" => array(), "books" => array()); 
        $id_groups = explode( ',', $id_book_groups); 


        for($i = 0; $i < count($id_groups); $i++){
            $resultBook = array(); 
            $stm = $this->db->prepare("SELECT id, code, id_serie, name, id_type_calification FROM $this->table_book WHERE id = ?");
            $stm->execute(array($id_groups[$i]));
            $resultBook = $stm->fetch(); 
            $id_serie = (!$resultBook) ? '' : $resultBook->id_serie; 

            $resultSerie = array();  
            $stm = $this->db->prepare("SELECT id, code, id_stage, name FROM $this->table_serie WHERE id = ?");
            $stm->execute(array($id_serie));
            $resultSerie = $stm->fetch();  
            $id_studystage = (!$resultSerie) ? '' : $resultSerie->id_stage;

            $resultStudyStage = array();  
            $stm = $this->db->prepare("SELECT id, code, name FROM $this->table_studystage WHERE id = ?");
            $stm->execute(array($id_studystage));
            $resultStudyStage = $stm->fetch();    
            //array_push($globalData, array("serie" => $resultSerie, "studystage" => $resultStudyStage, "book" => $resultBook )); 
            
            array_push($globalData["books"] , $resultBook);
            array_push($globalData["series"] , $resultSerie);
            array_push($globalData["studystages"] , $resultStudyStage);
        } 

      
        

        return $globalData;
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

    public function byGrade($id_grade)
    {
        try
        {
            $result = array(); 
            $stm = $this->db->prepare("SELECT * FROM $this->table WHERE id_grade = ?"); 
            $stm->execute(array($id_grade));

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

    public function bySerie($id_serie, $order = 'name')
    {
        try
        {
            $result = array(); 
            $stm = $this->db->prepare("SELECT * FROM $this->table WHERE id_serie = ? order by $order"); 
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

    public function bySerieCS($id_serie, $order = 'name')
    {
        try
        {
            $result = array(); 
            $stm = $this->db->prepare("SELECT * FROM $this->table WHERE id_serie = ? and slider_active = 1 order by $order"); 
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
                            code            = ?, 
                            name            = ?,
                            description     = ?,
                            unitys_number   = ?,
                            grade_id        = ?,
                            updated         = ?,
                        WHERE id = ?";
                
                $this->db->prepare($sql)
                     ->execute(
                        array(
                            $data['code'], 
                            $data['name'],
                            $data['description'],
                            $data['unitys_number'],
                            $data['grade_id'],
                            $data['id'],
                            date('Y-m-d G:H:i')
                        )
                    );
            }
            else
            {
                $sql = "INSERT INTO $this->description
                            (code, name, description, number_unity, grade, inserted)
                            VALUES (?,?,?,?,?,?)";
                
                $this->db->prepare($sql)
                     ->execute(
                        array(
                            $data['code'], 
                            $data['name'],
                            $data['description'],
                            $data['unitys_number'],
                            $data['grade_id'],
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