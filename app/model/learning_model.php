<?php
namespace App\Model;

use App\Lib\Database;
use App\Lib\Response;

class LearningModel extends GeneralConfig
{
    private $db; 
    private $dbpeTemp;
    private $dbmaster;
    
    public function __CONSTRUCT($token_data = array())
    {
        $this->db = Database::StartUpPe(); 
        $this->dbpe = Database::StartUpArea( isset($token_data->amb) ? $token_data->amb : $this->bd_base_pe );
        $this->dbmaster = Database::StartUpMaster();
        $this->response = new Response(); 
        $this->token_data = $token_data;

    }
    
    public function GetAllcompetitions()
    {
		try
		{
			$result = array();

			$stm = $this->db->prepare("SELECT * FROM $this->table_competitions");
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

    public function GetAllCapacitys()
    {
        try
        {
            $result = array();

            $stm = $this->db->prepare("SELECT cp.id id, cp.name name, co.id id_competition, co.name name_competition FROM $this->table_capacitys cp inner join $this->table_competitions co on cp.id_competition = co.id  WHERE cp.id = ?");
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

     public function GetAllIndicators()
     {
        try
        {
            $result = array(); 

            $stm = $this->db->prepare("SELECT id.id id, id.name name, cp.id id_capacity, cp.name name_capacity, co.id id_competition, co.name name_competition FROM $this->table_indicators id inner join $this->table_capacitys cp on id.id_capacity = cp.id inner join $this->table_competitions co on cp.id_competition = co.id");


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


    
    
    public function GetCompetitios($id)
    {
		try
		{
			$result = array();

			$stm = $this->db->prepare("SELECT * FROM $this->table_competitions WHERE id = ?");
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

    public function GetCapacitys($id)
    {
        try
        {
            $result = array();

            $stm = $this->db->prepare("SELECT cp.id id, cp.name name, co.id id_competition, co.name name_competition FROM $this->table_capacitys cp inner join $this->table_competitions co on cp.id_competition = co.id  WHERE cp.id = ?");
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

    public function GetCapacitysByCompetition($id_competition)
    {
        try
        {
            $result = array();

            $stm = $this->db->prepare("SELECT * FROM $this->table_capacitys WHERE id_competition = ?");
            $stm->execute(array($id_competition));

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

    public function GetIndicators($id)
    {
        try
        {
            $result = array(); 
             $stm = $this->db->prepare("SELECT id.id id, id.name name, cp.id id_capacity, cp.name name_capacity, co.id id_competition, co.name name_competition FROM $this->table_indicators id inner join $this->table_capacitys cp on id.id_capacity = cp.id inner join $this->table_competitions co on cp.id_competition = co.id  WHERE id.id = ?");
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

    public function GetIndicatorsByCapacity($id_capacity)
    {
        try
        {
            $result = array();

             $stm = $this->db->prepare("SELECT id.id id, id.name name, cp.id id_capacity, cp.name name_capacity, co.id id_competition, co.name name_competition FROM $this->table_indicators id inner join $this->table_capacitys cp on id.id_capacity = cp.id inner join $this->table_competitions co on cp.id_competition = co.id  WHERE id.id_capacity = ?");

            $stm->execute(array($id_capacity));

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

    public function GetAllEvaluationType()
    {
        try
        {
            $result = array();

            $stm = $this->dbpe->prepare("SELECT * FROM $this->table_calification_type");
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


    public function GetScoredLetters()
    {
        try
        {
            $result = array();

            $stm = $this->dbpe->prepare("SELECT * FROM $this->table_scored_letters order by value asc");
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



    public function GetEvaluationRange()
    {
        try
        {
            $result = array();

            $stm = $this->dbpe->prepare("SELECT * FROM $this->table_evaluation_range");
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

    private function GetEvaluationByStatus_private($amb, $id_user_link, $status)
    {
     
        $returnData = array();
        $resultUserAmb = array();
        $this->dbpeTemp = Database::StartUpArea($amb);
        $stm = $this->dbpeTemp ->prepare("SELECT id FROM $this->table_user WHERE id_user_link = ?");
        $stm->execute(array($id_user_link));
        $resultUserAmb = $stm->fetch(); 

        $cond = ($status == false) ? '' : ' and qj.status = '.$status;

        $result = array(); 
        $stm = $this->dbpeTemp->prepare("SELECT c.id,c.code,qj.id_user id_alumn, score, id_calification_type, status FROM $this->table_question_join qj INNER JOIN $this->table_class c on qj.id_class = c.id WHERE c.id_teacher = ?" . $cond);
        $stm->execute(array($resultUserAmb->id)); 
        $result = $stm->fetchAll();
        foreach ($result as $key => $value) { 
            array_push($returnData, $value);
        }
        return $returnData;
    }
    public function GetAllScoresByClass($code_class){
        try
        {
            $returnData = array();
            $resultActivitys = array();
            $id_teacher = $this->token_data->id; 
            //var_dump($this->token_data);
            $stm = $this->dbpe ->prepare("
                SELECT r.name, r.id, r.value, r.page, r.id_session id_session, s.name name_session, s.number number_session
                FROM $this->table_resources r
                INNER JOIN $this->table_class c on r.id_class = c.id 
                LEFT JOIN $this->table_sessions s on r.id_session = s.id
                WHERE c.code = ? and c.id_teacher = ? and r.type in (1,2)
                order by r.page");
            $stm->execute(array($code_class, $id_teacher));
            $resultActivitys = $stm->fetchAll();  

            $resultAlumnsActivitys = array(); 
            $code_alumn =uniqid();
            $stm = $this->dbpe->prepare("
                SELECT u.id, u.first_name, u.last_name, uc.code_class, uc.id_class, qj.status, qj.score, qj.date_scored, qj.id_resource, qj.id_calification_type, qj.code code_question, qj.id id_question,
                    '".$code_alumn."' code_alumn,  r.id_unity
                FROM $this->table_user u
                LEFT JOIN $this->table_user_class uc on u.id = uc.id_user
                LEFT JOIN $this->table_class c on uc.id_class = c.id
                INNER JOIN $this->table_question_join qj on uc.id_class = qj.id_class and u.id = qj.id_user
                INNER JOIN $this->table_resources r on qj.id_resource = r.id
                WHERE u.id_type = 1 and c.code = ? and c.id_teacher = ?
                ORDER BY u.first_name, u.last_name");
            $stm->execute(array($code_class, $id_teacher)); 
            $resultAlumnsActivitys = $stm->fetchAll();

            $resultAlumns = array(); 
            $stm = $this->dbpe->prepare("
                SELECT u.id, u.first_name, u.last_name, uc.code_class, uc.id_class 
                FROM $this->table_user u
                LEFT JOIN $this->table_user_class uc on u.id = uc.id_user
                LEFT JOIN $this->table_class c on uc.id_class = c.id
                WHERE u.id_type = 1 and c.code = ? and c.id_teacher = ?
                ORDER BY u.first_name, u.last_name");
            $stm->execute(array($code_class, $id_teacher)); 
            $resultAlumns = $stm->fetchAll();

            $this->response->result = array("activitys" => $resultActivitys, "alumns_activitys" => $resultAlumnsActivitys, "alumns" => $resultAlumns);
            $this->response->setResponse(true);  
            return $this->response;

        }
        catch(Exception $e)
        {
            $this->response->setResponse(false, $e->getMessage());
            return $this->response;
        } 
    }

    public function GetAllScoresByClassByUnity($code_class, $id_unity){
        try
        {
            $returnData = array();

            $resultActivitys = array();
            $id_teacher = $this->token_data->id; 
            //var_dump($this->token_data);
            $stm = $this->dbpe ->prepare("
                SELECT r.name, r.id, r.value, r.page, r.id_session id_session, s.name name_session, s.number number_session
                FROM $this->table_resources r
                INNER JOIN $this->table_class c on r.id_class = c.id 
                LEFT JOIN $this->table_sessions s on r.id_session = s.id
                WHERE c.code = ? and c.id_teacher = ? and r.type in (1,2) and r.id_unity = ?
                order by r.page");
            $stm->execute(array($code_class, $id_teacher, $id_unity));
            $resultActivitys = $stm->fetchAll();  

            $resultAlumnsActivitys = array(); 
            $code_alumn =uniqid();
            $stm = $this->dbpe->prepare("
                SELECT u.id, u.first_name, u.last_name, uc.code_class, uc.id_class, qj.status, qj.score, qj.date_scored, qj.id_resource, qj.id_calification_type, qj.code code_question, qj.id id_question,
                    '".$code_alumn."' code_alumn
                FROM $this->table_user u
                LEFT JOIN $this->table_user_class uc on u.id = uc.id_user
                LEFT JOIN $this->table_class c on uc.id_class = c.id
                INNER JOIN $this->table_question_join qj on uc.id_class = qj.id_class and u.id = qj.id_user
                INNER JOIN $this->table_resources r on qj.id_resource = r.id
                WHERE u.id_type = 1 and c.code = ? and c.id_teacher = ? and r.id_unity = ?
                ORDER BY u.first_name, u.last_name");
            $stm->execute(array($code_class, $id_teacher, $id_unity)); 
            $resultAlumnsActivitys = $stm->fetchAll();

            $resultAlumns = array(); 
            $stm = $this->dbpe->prepare("
                SELECT u.id, u.first_name, u.last_name, uc.code_class, uc.id_class 
                FROM $this->table_user u
                LEFT JOIN $this->table_user_class uc on u.id = uc.id_user
                LEFT JOIN $this->table_class c on uc.id_class = c.id
                WHERE u.id_type = 1 and c.code = ? and c.id_teacher = ?
                ORDER BY u.first_name, u.last_name");
            $stm->execute(array($code_class, $id_teacher)); 
            $resultAlumns = $stm->fetchAll();

            $this->response->result = array("activitys" => $resultActivitys, "alumns_activitys" => $resultAlumnsActivitys, "alumns" => $resultAlumns);
            $this->response->setResponse(true);  
            return $this->response;

        }
        catch(Exception $e)
        {
            $this->response->setResponse(false, $e->getMessage());
            return $this->response;
        } 
    }

    public function GetAllEvaluationByStatus($status = false)
    {
        try
        {    
            $totalEv = array();
            $totalAmbs = $this->getAllAmbs();  


            for($i=0; $i < count($totalAmbs); $i++)
            {
                $pendingAmb = $this->GetEvaluationByStatus_private($totalAmbs[$i]->amb, $totalAmbs[$i]->id_user_link, $status); 

                foreach ($pendingAmb as $key => $value) { 
                    array_push($totalEv, $value);
                } 
            }    

            $this->response->result = $totalEv;
            $this->response->setResponse(true);  
            return $this->response;
        }
        catch(Exception $e)
        {
            $this->response->setResponse(false, $e->getMessage());
            return $this->response;
        } 
    }

    public function GetEvaluationByClassByStatus($code_class, $status)
    {
        try
        { 
            $id_user = $this->token_data->id;
            $cond = ($status == false) ? '' : ' and qj.status = '.$status;

            $result = array(); 
            $stm = $this->dbpe->prepare("SELECT c.id,c.code, qj.id_user id_alumn, qj.score, qj.id_calification_type, qj.status FROM $this->table_question_join qj INNER JOIN $this->table_class c on qj.id_class = c.id WHERE c.id_teacher = ? and c.code = ? " . $cond);
            $stm->execute(array($id_user, $code_class)); 
            $result = $stm->fetchAll();
            $this->response->result = $result;
            $this->response->setResponse(true);  
            return $this->response;
        }
        catch(Exception $e)
        {
            $this->response->setResponse(false, $e->getMessage());
            return $this->response;
        } 
    }

    public function GetEvaluationByClassByStatusDetail($code_class, $status)
    {
        try
        { 
            $id_user = $this->token_data->id;
            $cond = ($status == false) ? '' : ' and qj.status = '.$status;
            $code = uniqid();

            $result = array(); 
            $stm = $this->dbpe->prepare("SELECT c.id,c.code, qj.id id_question, qj.code code_question,  qj.id_user id_alumn, '".$code."' code_alumn, u.first_name, u.last_name, r.name, r.page, qj.score, qj.id_calification_type, qj.status, date_format(qj.inserted, '%e/%c/%Y a las %l:%i %p') date_inserted, date_format(qj.inserted, 'a las %l:%i %p') date_inserted_hour, qj.inserted, DATEDIFF(now(),qj.inserted) daysago
                FROM $this->table_question_join qj 
                INNER JOIN $this->table_class c on qj.id_class = c.id 
                INNER JOIN $this->table_user u on qj.id_user = u.id
                INNER JOIN $this->table_resources r on c.id = r.id_class and r.id = qj.id_resource
                WHERE c.id_teacher = ? and c.code = ? " . $cond . " order by qj.inserted desc");
            $stm->execute(array($id_user, $code_class)); 
            $result = $stm->fetchAll();
            $this->response->result = $result;
            $this->response->setResponse(true);  
            return $this->response;
        }
        catch(Exception $e)
        {
            $this->response->setResponse(false, $e->getMessage());
            return $this->response;
        } 
    }

    private function getAllAmbs()
    {
        $totalAmbs = array();
        $idm = $this->token_data->idm;
        $resultAmbsScholls = (object)[];
        $stm = $this->dbmaster->prepare("SELECT sc.id, sc.amb, ums.id_user_link FROM $this->table_user_master_scholl ums INNER JOIN $this->table_scholls sc on ums.id_scholl = sc.id  WHERE id_user_master = ?"); 
        $stm->execute(array($idm));
        $resultAmbsScholls = $stm->fetchAll();
        foreach ($resultAmbsScholls as $key => $value) { 
            array_push($totalAmbs, $value);
        }
        return $totalAmbs;
    }

    public function GetSessionsByBook($id_book)
    {
        try
        {
            $result = array(); 
            $stm = $this->dbpe->prepare("SELECT * FROM $this->table_sessions WHERE id_book = ?"); 
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
     

}

