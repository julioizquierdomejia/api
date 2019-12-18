<?php
namespace App\Model;

use App\Model\TheClassModel; 
use App\Lib\Database;
use App\Lib\Response;  

class UserModel extends GeneralConfig
{
    private $db;
    private $dbpeTemp;
    private $dbmaster;

    private $table;
    private $table_type; 
    private $table_join_type;
    private $table_join_user;    

    private $status_aviable = 1;
    private $status_used = 2; 
 
    private $notification;   

    public function __CONSTRUCT($token_data = array())
    {
        $this->db = Database::StartUp();
        $this->dbpe = Database::StartUpArea( isset($token_data->amb) ? $token_data->amb : $this->bd_base_pe );
        $this->dbmaster = Database::StartUpMaster();
        $this->response = new Response();
        $this->notification = new NotificationModel($token_data);
        $this->token_data = $token_data;

        $this->table = $this->table_user;
        $this->table_type = $this->table_user_type; 
        $this->table_join_type = $this->table_user_join_type;
        $this->table_join_user = $this->table_user_join_type_user;
    }

    public function getAlumn($code)
    {
        try
        {
            $result = array();    
            $id_alumn =  intval(substr($code, 0, strpos($code, "_")));

            $stm = $this->dbpe->prepare("SELECT * FROM $this->table where id_type = 1 and id = ?");
            $stm->execute(array($id_alumn)); 
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

    public function getType($public = true) 
    {
        try
        {
            $result = array();  
            $condPublic = ($public) ? 'where public = 1' : '';

            $stm = $this->dbpe->prepare("SELECT * FROM $this->table_type $condPublic");
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

    public function getJoinType($public = true)
    {
        try
        {
            $result = array(); 

            $stm = $this->dbpe->prepare("SELECT * FROM $this->table_join_type");
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

    public function getJoinStatus($class_code)
    { 
        try
        {
            if($class_code == 'base')
            {
                $this->response->result = array("joined" => 0);
                $this->response->setResponse(true); 
                return $this->response;
            }

            $result = array(); 
            $id_user = $this->token_data->id;
            $amb = $this->token_data->amb; 
            $this->dbpeTemp = Database::StartUpArea($amb);
            $stm = $this->dbpeTemp->prepare("SELECT uc.code_class, uc.id_class, '1' joined FROM $this->table u inner join $this->table_user_class uc on u.id = uc.id_user where u.id = ? and uc.code_class = ?");
            $stm->execute(array($id_user, $class_code));
            $result = $stm->fetch();

            var_dump( "SELECT uc.code_class, uc.id_class, '1' joined FROM $this->table u inner join $this->table_user_class uc on u.id = uc.id_user where u.id = '$id_user' and uc.code_class = '$class_code' ");

            if( count($result) > 0)
            {
                $this->response->setResponse(true); 
                $this->response->result = $result;
            } 
            else
            {
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
            
    public function GetExtraInfo(){
        try
        {
            $id_user = $this->token_data->id;
            $result = array(); 

            $stm = $this->dbpe->prepare("SELECT if( tutorialClass is null, 0, tutorialClass) tutorialClass, id_type FROM $this->table where id = ?");
            $stm->execute(array($id_user));
            
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

    public function GetAllGroups()
    {
        try
        {
            $result = array();

            $stm = $this->dbpe->prepare("SELECT * FROM $this->table_users_group order by name");
            $stm->execute();
            
            $this->response->setResponse(true);
            $result = $stm->fetchAll(); 

            foreach ($result as $key => $group) { 
                $group->users = $this->GetUsersByGroup($group->id, $group->code_class, false);
            }
            $this->response->result = $result;            
            return $this->response;
        }
        catch(Exception $e)
        {
            $this->response->setResponse(false, $e->getMessage());
            return $this->response;
        }
    }

    public function GetUsersByGroup($id_user_group, $code_class, $apiResponse = true)
    {
        try
        {
            $result = array();
            $id_teacher = $this->token_data->id;

            $stm = $this->dbpe->prepare("SELECT ugu.id id_user_group_detail, ugu.id_user_group, u.first_name, u.last_name, u.email, u.id id FROM $this->table_users_group_user ugu INNER JOIN $this->table_user u on ugu.id_user = u.id where id_user_group = ?");
            $stm->execute( array($id_user_group) );
            
            $this->response->setResponse(true);
            $result = $stm->fetchAll();   

            $cm = new TheClassModel($this->token_data);

            foreach ($result as $alumn) {
                $alumn->detailActivitys = $cm->GetDetailAlumnsByCode($code_class, $id_teacher, $alumn->id);
            } 

            if($apiResponse){
                $this->response->result = $result;            
                return $this->response;
            } 
            return $result;            
        }
        catch(Exception $e)
        {
            if($apiResponse){
                $this->response->setResponse(false, $e->getMessage());
                return $this->response;
            } 
            return false; 
        }
    }

    public function InserOrUpdateGroup($data)
    {
        $id_teacher = $this->token_data->id;
        $amb = $this->token_data->amb;
        if( $data == null ) 
        {
            $this->response->setResponse(false, "Error, no data");
            return $this->response;
        }
        else
        { 
            try 
            {
                if(isset($data['id_user_group']))
                {
 
                    $sql = "UPDATE $this->table_users_group SET 
                                name        = ?,  
                                id_teacher  = ?,
                                updated     = ?
                            WHERE id = ? and code_class = ?";
                    
                    $this->dbpe->prepare($sql)
                         ->execute(
                            array( 
                                $data['name'], 
                                $id_teacher, 
                                date('Y-m-d G:H:i'),
                                $data['id_user_group'],
                                $data['code_class'],
                            )
                        ); 
                    $idresponse = $data['id_user_group'];
                }
                else
                {
                    $sql = "INSERT INTO $this->table_users_group
                                (name, id_teacher, code_class, inserted)
                                VALUES (?,?,?,?)";
                     
                    $this->dbpe->prepare($sql)
                         ->execute(
                            array( 
                                $data['name'], 
                                $id_teacher, 
                                $data['code_class'], 
                                date('Y-m-d G:H:i')
                            )
                        ); 
                    $idresponse = $this->dbpe->lastInsertId();      

                }  
 
                if( $idresponse > 0 ){
                    $resultUsersInsert = $this->InserOrUpdateGroupUsers( $idresponse, $data['code_class'], $data['alumns'] );
                    if($resultUsersInsert["success"]){
                        $this->response->result = array('id_user_group' =>  $idresponse );
                        $this->response->setResponse(true);
                        return $this->response;
                    }else{
                        $this->deleteUsersGroup($idresponse, $code_class, false); 
                    }
                }  
                $this->response->setResponse(false);
                return $this->response;
                
            }catch (Exception $e) 
            {
                $this->response->setResponse(false, $e->getMessage());
                return $this->response;
            }
        } 
    }

    public function InserOrUpdateGroupUsers($id_user_group, $code_class, $alumns)
    { 
        if( $alumns == null ) 
        {
            return array("success"=>false);
        }
        else
        { 
            try 
            {
                $cont = 0;
                foreach ($alumns as $key => $alumn) { 
                    $id_user_temp = $alumn["id"];
                    $sql = "INSERT INTO $this->table_users_group_user
                            ( id_user_group , id_user, code_class, inserted)
                            VALUES (?,?,?,?)";
                 
                    $this->dbpe->prepare($sql)
                         ->execute(
                            array(  
                                $id_user_group,
                                $id_user_temp,                                
                                $code_class, 
                                date('Y-m-d G:H:i')
                            )
                        ); 
                    $idresponse = $this->dbpe->lastInsertId();  
                    if($idresponse > 0)
                        $cont++;                    
                } 

                if(count($alumns) == $cont){
                    return array("success"=>true);
                }else{
                    return array("success"=>false);
                }
                
            }catch (Exception $e) 
            {
                return array("success"=>false);
            }
        } 
    }

    public function deleteUsersGroup($id_user_group, $code_class, $apiResponse = true)
    {
        try 
        {
            $stm = $this->dbpe->prepare("DELETE FROM $this->table_users_group WHERE id = ? and code_class = ?");    
            $stm->execute(array($id_user_group, $code_class));
     
            $stm = $this->dbpe->prepare("DELETE FROM $this->table_users_group_user WHERE id_user_group = ?");   
            $stm->execute(array($id_user_group)); 

            if($apiResponse){
                $this->response->setResponse(true);       
                return $this->response;
            }
        }catch (Exception $e) 
        {
            if($apiResponse){
                $this->response->setResponse(false);       
                return $this->response;
            }
        }
    }

    public function GetAllGroupsByCodeClass($code_class)
    {
        try
        {
            $result = array();

            $stm = $this->dbpe->prepare("SELECT * FROM $this->table_users_group where code_class = ? order by name");
            $stm->execute(array($code_class)); 
            $result = $stm->fetchAll();   

            foreach ($result as $key => $group) { 
                $group->users = $this->GetUsersByGroup( $group->id, $group->code_class , false );
            } 

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

    public function UpdateField($data){ 
        try
        {
            $id_user = $this->token_data->id;
            $sql = "UPDATE $this->table SET 
                            ".$data["field"]." = ?, 
                            updated = ?
                        WHERE id = ?";  
                    
            $sent = $this->dbpe->prepare($sql) 
                         ->execute(
                            array( 
                                $data["value"],
                                date('Y-m-d G:H:i'),
                                $id_user
                            )
                        ); 
            
            $this->response->setResponse(true);  
            return $this->response;
        }
        catch(Exception $e)
        {
            $this->response->setResponse(false, $e->getMessage());
            return $this->response;
        }
    }

    public function register($data)
    { 
        try
        { 
            if(!$this->checkExistUser($data["mail"])){
                $aviableAcces = $this->checkJoin($data["id_join_type"], $data["code_join"], $data["mail"] );
                if($aviableAcces["aviable"]){
                    if($data["id_join_type"] == 1){
                        return $this->createUser($data, $aviableAcces);
                    }else if($data["id_join_type"] == 2){
                        if($aviableAcces["result"] == 'aviable'){
                             return $this->createUser($data, $aviableAcces);
                        }else{
                            $this->response->setResponse(false,"class_user_joined");
                            return $this->response;
                        }
                    }   
                }else{
                    if($data["id_join_type"] == 1){
                        if(isset($aviableAcces["data"])){
                             $this->response->setResponse(false,"book_code_used");
                        }else{
                             $this->response->setResponse(false,"book_code_invalid");
                        }
                    }
                    else if($data["id_join_type"] == 2){  
                        $this->response->setResponse(false,"class_invalid");
                    }
                    return $this->response;
                }
            }else{
                $this->response->setResponse(false,"exists");
                return $this->response;
            }
        }
        catch(Exception $e)
        {
            $this->response->setResponse(false, $e->getMessage());
            return $this->response;
        }  
    }


    public function checkExistUser ($mail){
        try
        {
            $dataResponse = array();  
            $result = array();  
            $stm = $this->dbpe->prepare("SELECT * FROM $this->table WHERE trim(email) = ?"); 
            $stm->execute(array(trim($mail)));
            $result = $stm->fetchAll();  
            if( count($result) > 0){  
                return true;  
            } 
            return false; 
        }
        catch(Exception $e)
        {
            return false; 
        } 
    }

    public function checkJoin($type, $code, $mail){
        try
        {
            $dataResponse = array();  
            $result = array();  
            if(intval($type) == 1){ 
                    
                $stm = $this->dbmaster->prepare("SELECT * FROM $this->table_book_code WHERE code = ?"); 
                $stm->execute(array($code));
                $result = $stm->fetchAll();  
                if( count($result) > 0){
                    foreach ($result as $row) 
                    {
                        if($row->id_type == 3){
                            $dataResponse = array("aviable" => true, "data" => $row ); 
                            return $dataResponse; 
                        }else{
                            $dataBookRel = $this->getBookDataRel($row->id_book); 
                            $dataResponse = array("aviable" => ($row->id_status == 1 && $row->enabled == 1) ? true : false, "data" => $row, "dataExtra" => $dataBookRel ); 
                            return $dataResponse; 
                        }  
                    }
                } 
                return array("aviable" => false); 
                
            }else if(intval($type) == 2){ 
                $stm = $this->dbpe->prepare("SELECT * FROM $this->table_class WHERE code = ?"); 
                $stm->execute(array($code));
                $result = $stm->fetchAll();  
                if( count($result) > 0){
                    foreach ($result as $row) { 
                        $checkJoinUserClass = $this->checkJoinUserClass1($code, $mail);  
                        $dataBookRel = $this->getBookDataRel($row->id_book); 
                        $dataResponse = array("aviable" => true, "result" => $checkJoinUserClass, "data" => $row, "dataExtra" => $dataBookRel); 
                        return $dataResponse; 
                    }
                } 
                return array("aviable" => false); 
            }else{
                return array("aviable" => false); 
            }
        }
        catch(Exception $e)
        {
            return array("aviable" => false); 
        } 
    }

    public function getBookDataRel($id_book){
        $resultBook = array(); 
        $stm = $this->db->prepare("SELECT * FROM $this->table_book WHERE id = ?");
        $stm->execute(array($id_book));
        $resultBook = $stm->fetch();
        $id_grade = $resultBook->id_grade;

        $resultGrade = array();  
        $stm = $this->db->prepare("SELECT * FROM $this->table_grade WHERE id = ?");
        $stm->execute(array($id_grade));
        $resultGrade = $stm->fetch();  
        $id_studystage = $resultGrade->id_studystage;
 
        return array("id_grade" => $id_grade, "id_studystage" => $id_studystage, "book" => $resultBook );
    }

    public function checkJoinUserClass1($code, $mail){
        $resulUser = array(); 
        $stm = $this->dbpe->prepare("SELECT * FROM $this->table WHERE email = ?");
        $stm->execute(array(trim($mail)));
        $resulUser = $stm->fetchAll();
        if( count($resulUser) > 0){
            foreach ($resulUser as $row) { 
                if($this->checkJoinUserClass2($row->id, $code)){
                    return 'exist';
                }
            }
        }     
        return 'aviable';
    }

    public function checkJoinUserClass2($id_user, $code_class)
    {
        $resulUserClass = array(); 
        $stm = $this->dbpe->prepare("SELECT * FROM $this->table_user_class WHERE id_user = ? and code_class = ?");
        $stm->execute(array($id_user, trim($code_class)));
        $resulUserClass = $stm->fetchAll();
        if( count($resulUserClass) > 0){
             return true;
        }   
        return false;
    }

    public function createUser($data, $dataExtra)
    { 
        try
        {  
            $data["amb"] = 'demo';
            $amb = $data["amb"];
            $sql = "INSERT INTO $this->table
                                (email, password, first_name, last_name, email_teacher, id_grade, id_studystage, id_type, id_join_type, inserted)
                                VALUES (?,?,?,?,?,?,?,?,?,?)"; 
            $this->dbpe->prepare($sql)
                 ->execute(
                    array( 
                        $data['mail'], 
                        password_hash($data['password'], PASSWORD_DEFAULT),
                        $data['first_name'],
                        $data['last_name'], 
                        '',
                        ( isset($dataExtra["dataExtra"]["id_grade"]) ) ? $dataExtra["dataExtra"]["id_grade"] : 0, 
                        ( isset($dataExtra["dataExtra"]["id_studystage"] )) ? $dataExtra["dataExtra"]["id_studystage"] : 0,
                        $dataExtra["data"]->id_type,
                        $data['id_join_type'],
                        date('Y-m-d G:H:i')
                    )
                ); 
            $idresponse = $this->dbpe->lastInsertId();         

            if($idresponse > 0){   
                return $this->registerJoin($data["id_join_type"], $idresponse, date('Y-m-d G:H:i'), $data["code_join"], $dataExtra["data"], $data["amb"], $data['mail']);  
            } 
           
            $this->response->setResponse(false);
            return $this->response; 
        }
        catch(Exception $e)
        {
            $this->response->setResponse(false, $e->getMessage());
            return $this->response;
        }  
    }

    private function registerMaster($amb, $id_user, $email)
    { 
        $sql = "INSERT INTO $this->table_user_master
                                (id_user_link, email, inserted)
                                VALUES (?,?,?)"; 
        $this->dbmaster->prepare($sql)
             ->execute(
                array( 
                    $id_user,
                    $email,
                    date('Y-m-d G:H:i')
                )
            );  
        $idresponse = $this->dbmaster->lastInsertId(); 
        if( $idresponse > 0){
            return array("success" => true, "idm" => $idresponse);
        }   
        return array("success" => false);
    }

    public function registerJoin($id_join_type, $id_user, $date_joined, $code, $data_code, $amb, $email)
    {  
        try 
        {
            if($id_join_type == 1){
                   $master = $this->registerMaster($amb, $id_user, $email);
                   if( $master["success"] )
                   {
                        if( $this->updateStatusCodeBook($data_code->id, 2, $id_user, $master["idm"]) ){
                            $sql = "INSERT INTO $this->table_join_user
                                            (id_join_type, id_user, date_joined, code)
                                            VALUES (?,?,?,?)";  

                            $this->dbpe->prepare($sql)
                                ->execute( 
                                    array(
                                        $id_join_type,
                                        $id_user, 
                                        $date_joined, 
                                        $code
                                    )
                                ); 
                            $idresponse = $this->dbpe->lastInsertId(); 
                            $this->response->SetIdInserted($id_user);
                            $this->response->setResponse(true);
                            return $this->response; 
                            
                        } 
                        else 
                        {
                            $this->rollBackUser($id_join_type, $id_user, $data_code->id);
                        }   
                   }
                   else
                   {
                        $this->rollBackUser($id_join_type, $id_user, $data_code->id);
                   }
                  
            }else if($id_join_type == 2){
                $sql = "INSERT INTO $this->table_user_class
                                        (id_user, id_class, code_class, date_joined)
                                        VALUES (?,?,?,?)";  

                $this->dbpe->prepare($sql)
                    ->execute( 
                        array(
                            $id_user,
                            $data_code->id,
                            $code, 
                            $date_joined 
                        )
                    ); 
                $idresponse = $this->dbpe->lastInsertId();

                $master = $this->registerMaster($amb, $id_user, $email);
                 
                if(intval($idresponse) > 0){   

                    $notifData = array(                                 
                                array( "name" => "alumn", "id" => $id_user ),
                                array( "name" => "class", "id" => $data_code->id ),
                                array( "name" => "teacher","id" => $data_code->id_teacher )
                            );
                    $linkData = array("code_class" => $data_code->code, "code_alumn" => $id_user . '_' . uniqid() ); 

                    $this->notification->privateRegister($this->notification->alumn_class_join, $data_code->id_teacher, $amb, $notifData, $id_user, $linkData);
                    $this->response->SetIdInserted($id_user);
                    $this->response->setResponse(true);
                    return $this->response; 
                }else{
                    $this->rollBackUser($id_join_type, $id_user, $data_code->id);
                }   
            }
        }
        catch (Exception $e) 
        {
            $this->rollBackUser($id_join_type, $id_user, $data_code->id);
        } 
    }

    public function rollBackUser($id_join_type, $id_user, $id_data_book){ 
        $stm = $this->db->prepare("DELETE FROM $this->table WHERE id = ?");   
        $stm->execute(array($id_user));

        $stm = $this->db->prepare("DELETE FROM $this->table_master WHERE id_user_link = ?");   
        $stm->execute(array($id_user));

        if($id_join_type == 1){
            $this->resetBookCode($id_data_book, 1); 
        } 
        
        $this->response->setResponse(false, $e->getMessage());
        return $this->response; 
    }


    public function updateStatusCodeBook($id_data_book, $id_status, $id_user_join, $id_user_master){

        $sql = "UPDATE $this->table_book_code SET 
                            id_status   = ?, 
                            id_user_join = ?,
                            date_activate = ?,
                            date_expired = ?
                        WHERE id = ?"; 

        $date_expired = date('Y-m-d G:H:i', strtotime('+1 years'));

                
        $sent = $this->dbmaster->prepare($sql) 
                     ->execute(
                        array(
                            $id_status,  
                            $id_user_master,
                            date('Y-m-d G:H:i'),
                            $date_expired,
                            $id_data_book
                        )
                    );
        return $sent;
    }

    public function resetBookCode($id_data_book, $id_status){

        $sql = "UPDATE $this->table_book_code SET 
                            id_status   = ?, 
                            id_user_join = ?,
                            date_activate = ?,
                            date_expired = ?
                        WHERE id = ?";

        $date_expired = date('Y-m-d G:H:i', strtotime('+5 years'));

                
        $sent = $this->dbmaster->prepare($sql) 
                     ->execute(
                        array(
                            $id_status,  
                            NULL,
                            NULL,
                            NULL,
                            $id_data_book
                        )
                    );
        return $sent;
    }

    public function validateJoin($id_type, $code){
        try
        {
            $result = array();  
            $stm = $this->dbmaster->prepare("SELECT * FROM $this->table_book_code where code = ? and id_status = $this->status_aviable");
            $stm->execute(array($data['code']));
            
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