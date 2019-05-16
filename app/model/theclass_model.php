<?php
namespace App\Model; 

use App\Lib\Database;
use App\Lib\Response;

class TheClassModel extends GeneralConfig
{
    private $table; 
    private $logger;


    private $dbmaster;
    private $dbpe; 
    private $dbpeTemp;
    

    public function __CONSTRUCT($token_data = array())
    {
        $this->dbmaster = Database::StartUpMaster();
        $this->dbpe = Database::StartUpArea( isset($token_data->amb) ? $token_data->amb : $this->bd_base_pe );
        $this->db = Database::StartUp();

        $this->response = new Response();

        $this->notification = new NotificationModel($token_data);

        $this->table = $this->table_scholls; 
        $this->token_data = $token_data;
    }

    public function Get($id_class)
    {
         
        try
        {
            $id_user = $this->token_data->id;
            $stm = $this->dbpe->prepare("SELECT * FROM $this->table_class WHERE id_teacher = ? and id = ?");                   
            $stm->execute(array($id_user, $id_class));

            $this->response->result = $stm->fetch();
            $this->response->setResponse(true);  
            return $this->response;
        }
        catch(Exception $e)
        {
            $this->response->setResponse(false, $e->getMessage());
            return $this->response;
        } 
    }

    public function GetByCodeAlumn($code_class)
    { 
        try
        {
            $id_user = $this->token_data->id;
            $amb = $this->token_data->amb; 

            $stm = $this->dbpe->prepare("SELECT uc.date_joined, c.id, c.id_teacher, c.code, c.name, c.description, c.id_scholl, c.id_book, c.id_book_group, u.first_name teacher_first_name, u.last_name teacher_last_name, u.email teacher_email, '".$amb."' amb FROM $this->table_user_class uc INNER JOIN $this->table_class c on uc.id_class = c.id INNER JOIN $this->table_user u on c.id_teacher = u.id WHERE uc.id_user = ? and c.code = ? group by code"); 

            $stm->execute(array($id_user, $code_class));

            $this->response->result = $stm->fetch();
            $this->response->setResponse(true);  
            return $this->response;
        }
        catch(Exception $e)
        {
            $this->response->setResponse(false, $e->getMessage());
            return $this->response;
        } 
    }

    public function GetByCodeTeacher($code_class)
    { 
        try
        {
            $id_teacher = $this->token_data->id;
            $amb = $this->token_data->amb;   

            $stm = $this->dbpe->prepare("SELECT uc.date_joined, c.id, c.id_teacher, c.code, c.name, c.description, c.id_scholl, c.id_book, c.id_book_group, u.first_name teacher_first_name, u.last_name teacher_last_name, u.email teacher_email, '".$amb."' amb FROM $this->table_class c INNER JOIN $this->table_user u on c.id_teacher = u.id LEFT JOIN $this->table_user_class uc on c.id = uc.id_class WHERE c.id_teacher = ? and c.code = ? group by code"); 

            $stm->execute(array($id_teacher, $code_class));

            $this->response->result = $stm->fetch();
            $this->response->setResponse(true);  
            return $this->response;
        }
        catch(Exception $e)
        {
            $this->response->setResponse(false, $e->getMessage());
            return $this->response;
        } 
    }

    
    public function GetAllTeacher()
    { 
        try
        {    
            $totalClass = array();
            $totalAmbs = $this->getAllAmbs(); 

            for($i=0; $i < count($totalAmbs); $i++)
            { 
                $classAmb = $this->getAllByAmbTeacher($totalAmbs[$i]->amb, $totalAmbs[$i]->id_user_link, $totalAmbs[$i]->id); 
                foreach ($classAmb as $key => $value) { 
                    array_push($totalClass, $value);
                } 
            }  

            $this->response->result = $totalClass;
            $this->response->setResponse(true);  
            return $this->response;
        }
        catch(Exception $e)
        {
            $this->response->setResponse(false, $e->getMessage());
            return $this->response;
        } 
    }

    public function GetAllAlumn()
    { 
        try
        {    
            $totalClass = array();
            $totalAmbs = $this->getAllAmbs();  

            for($i=0; $i < count($totalAmbs); $i++)
            {
                $classAmb = $this->getAllByAmbAlumn($totalAmbs[$i]->amb, $totalAmbs[$i]->id_user_link, $totalAmbs[$i]->id); 

                foreach ($classAmb as $key => $value) { 
                    array_push($totalClass, $value);
                } 
            }    

            $this->response->result = $totalClass;
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

    public function getAllByAmbTeacher($amb, $id_user, $id_scholl = 0)
    {
        $resultUserAmb = $this->getUserByUserLink($amb, $id_user);

        $resultClass = array();
        $returnData = array(); 

        $this->dbpeTemp = Database::StartUpArea($amb);
        $stm = $this->dbpeTemp->prepare("SELECT id, id_book_group, name, code, id_scholl FROM $this->table_class WHERE id_teacher = ? group by code, id_book_group");
        $stm->execute(array($resultUserAmb->id));
        $resultClass = $stm->fetchAll();


        $resultClassChildren = array();
        foreach ($resultClass as $key => $value) {  
            $value->amb = $amb;
            $this->dbpeTemp = Database::StartUpArea($amb);
            $stm = $this->dbpeTemp->prepare("SELECT *, '".$amb."' amb, '".$id_scholl."' id_scholl FROM $this->table_class WHERE id_book_group = ? and code= ?"); 
            $stm->execute(array($value->id_book_group, $value->code));
            $resultClassChildren = $stm->fetchAll();
            foreach ($resultClassChildren as $keyChildren => $valueChildren) {
                $valueChildren->book = $this->getBook($valueChildren->id_book);
            }
            $value->books_linked = $resultClassChildren;
            array_push($returnData, $value);
        }  
        return $returnData;
    }

    public function getAllByAmbAlumn($amb, $id_user, $id_scholl = 0)
    {
        $resultUserAmb = $this->getUserByUserLink($amb, $id_user); 

        $resultClass = array();
        $returnData = array(); 
        //$stm = $this->dbpeTemp ->prepare("SELECT *, '".$amb."' amb, '".$id_scholl."' id_scholl FROM $this->table_user_class WHERE id_user = ?");
        $this->dbpeTemp = Database::StartUpArea($amb);
        $stm = $this->dbpeTemp->prepare("SELECT uc.date_joined, c.id, c.id_teacher, c.code, c.name, c.description, c.id_scholl, c.id_book, c.id_book_group, u.first_name teacher_first_name, u.last_name teacher_last_name, u.email teacher_email, '".$amb."' amb, '".$id_scholl."' id_scholl FROM $this->table_user_class uc INNER JOIN $this->table_class c on uc.id_class = c.id INNER JOIN $this->table_user u on c.id_teacher = u.id WHERE uc.id_user = ? group by c.code, c.id_book_group");
        $stm->execute(array($resultUserAmb->id));
        $resultClass = $stm->fetchAll();

        $resultClassChildren = array();
        foreach ($resultClass as $key => $value) {  
            $value->amb = $amb;
            $this->dbpeTemp = Database::StartUpArea($amb);
            $stm = $this->dbpeTemp->prepare("SELECT *, '".$amb."' amb, '".$id_scholl."' id_scholl FROM $this->table_class WHERE id_book_group = ? and code= ?"); 
            $stm->execute(array($value->id_book_group, $value->code));
            $resultClassChildren = $stm->fetchAll();
            foreach ($resultClassChildren as $keyChildren => $valueChildren) {
                $valueChildren->book = $this->getBook($valueChildren->id_book);
            }
            $value->books_linked = $resultClassChildren;
            array_push($returnData, $value);
        }  
        return $returnData;
    }

    private function getUserByUserLink($amb, $id_user_link)
    {
        $resultUserAmb = array();  
        $this->dbpeTemp = Database::StartUpArea($amb);
        $stm = $this->dbpeTemp ->prepare("SELECT id FROM $this->table_user WHERE id_user_link = ?");
        $stm->execute(array($id_user_link));
        $resultUserAmb = $stm->fetch();  
        return $resultUserAmb;
    } 

    /*public function GetAllAlumn()
    { 
        try
        {   
            $id_user = $this->token_data->id;
            $stm = $this->dbpe->prepare("SELECT uc.date_joined, c.id, c.id_teacher, c.code, c.name, c.description, c.id_scholl, c.id_book, u.first_name teacher_first_name, u.last_name teacher_last_name, u.email teacher_email FROM $this->table_user_class uc INNER JOIN $this->table_class c on uc.id_class = c.id INNER JOIN $this->table_users u on c.id_teacher = u.id WHERE uc.id_user = ?");

            $stm->execute(array($id_user));

            $this->response->result = $stm->fetchAll();
            $this->response->setResponse(true);  
            return $this->response;
        }
        catch(Exception $e)
        {
            $this->response->setResponse(false, $e->getMessage());
            return $this->response;
        } 
    }*/

    public function GetAllAlumnByBook($id_book)
    { 
        try
        {   
            $id_user = $this->token_data->id;
            $stm = $this->dbpe->prepare("SELECT uc.date_joined, c.id, c.id_teacher, c.code, c.name, c.description, c.id_scholl, c.id_book, u.first_name teacher_first_name, u.last_name teacher_last_name, u.email teacher_email FROM $this->table_user_class uc INNER JOIN $this->table_class c on uc.id_class = c.id INNER JOIN $this->table_user u on c.id_teacher = u.id WHERE uc.id_user = ? and c.id_book = ?");

            $stm->execute(array($id_user, $id_book));

            $this->response->result = $stm->fetch();
            $this->response->setResponse(true);  
            return $this->response;
        }
        catch(Exception $e)
        {
            $this->response->setResponse(false, $e->getMessage());
            return $this->response;
        } 
    } 
   

    public function GetAlumnsByCode($code_class, $id_teacher = false)
    { 
        try
        {
            $allActivitysClass = array();
            $id_teacher = (!$id_teacher) ? $this->token_data->id : $id_teacher;
            //var_dump($this->token_data);
            $stm = $this->dbpe->prepare("
                SELECT r.id, r.id_class, r.type, r.id_calification_type, r.status
                FROM $this->table_resources r 
                INNER JOIN $this->table_class c on r.id_class = c.id
                where c.code = ? and c.id_teacher = ?");                   
            $stm->execute(array($code_class, $id_teacher));
            $allActivitysClass = $stm->fetchAll();

            $alumns = array();
            $stm = $this->dbpe->prepare("
                SELECT u.id, u.email, u.first_name, u.last_name, u.id_type, c.code code_class, uc.date_joined 
                FROM $this->table_user u 
                INNER JOIN $this->table_user_class uc on u.id = uc.id_user 
                INNER JOIN $this->table_class c on uc.id_class = c.id
                where uc.code_class = ? and c.id_teacher = ? group by u.id");                   
            $stm->execute(array($code_class, $id_teacher));
            $alumns = $stm->fetchAll();

            foreach ($alumns as $row) { 
               $row->detailActivitys = $this->GetDetailAlumnsByCode($code_class, $id_teacher, $row->id);
            } 


            $this->response->result = array("alumns"=>$alumns, "activitys"=>$allActivitysClass);
            $this->response->setResponse(true);  
            return $this->response;
        }
        catch(Exception $e)
        {
            $this->response->setResponse(false, $e->getMessage());
            return $this->response;
        } 
    }

    public function GetDetailAlumnsByCode($code_class, $id_teacher, $id_user)
    { 
        try
        {   
            $result = array();
            $stm = $this->dbpe->prepare("
                SELECT qj.status, qj.id, qj.score, qj.id_calification_type, qj.id_resource, qj.id_user
                FROM $this->table_question_join qj 
                INNER JOIN $this->table_user u on qj.id_user = u.id
                INNER JOIN $this->table_class c on c.id = qj.id_class 
                INNER JOIN $this->table_user_class uc on c.id = uc.id_class and qj.id_user = uc.id_user 
                where c.code = ? and c.id_teacher = ? and u.id = ?");                   
            $stm->execute(array($code_class, $id_teacher, $id_user));
            $result = $stm->fetchAll();
            return $result;
        }
        catch(Exception $e)
        {
            return false;
        } 
    }


    public function InsertOrUpdate($data)
    { 
 
        if( $data == null ) 
        {
            $this->response->setResponse(false, "Error, no data");
            return $this->response;
        }
        else
        {
             
            try 
            {
                $amb = $this->token_data->amb;
                $mode = '';

               
                if(isset($data['id']))
                {
                    $this->dbpe = Database::StartUpArea($amb);  
                    $mode = 'edit';
                    $sql = "UPDATE $this->table_class SET   
                                name        = ?, 
                                description = ?,
                                updated     = ?
                            WHERE id = ?";
                    
                    $this->dbpe->prepare($sql)
                         ->execute(
                            array(  
                                $data['name'], 
                                $data['description'], 
                                date('Y-m-d G:H:i'),
                                $data['id']
                            )
                        );
                    $idresponse = $data['id'];
                }
                else
                {
                    $cont_success = 0;
                    $ids_inserted = array();
                    $mode = 'new';
                    $id_scholl = $data["id_scholl"]; 
                    $resultScholl = (object)[]; 
                    $stm = $this->dbmaster->prepare("SELECT code FROM $this->table_scholls WHERE id = ?"); 
                    $stm->execute(array($id_scholl));
                    $resultScholl = $stm->fetch();
                    $amb = $resultScholl->code; 

                    $this->dbpe = Database::StartUpArea($amb);
                    $code = $this->generateCodeClass();

                    $this->registerClassMaster($amb, $code);
                    $dataUserReigster = $this->registerJoinUserScholl($this->token_data->idm, $id_scholl, $amb, $this->token_data->id);
                   
                    //book group
                    $stm = $this->dbmaster->prepare("SELECT id_book_links FROM $this->table_book_group WHERE id = ?"); 
                    $stm->execute(array($id_scholl));
                    $resultScholl = $stm->fetch();

                    $resultBookGroup = $this->getBooksFromGroup($data['id_book_group']);  
                    if(count($resultBookGroup) > 0)
                    {
                        $id_book_group = $resultBookGroup[0]->id;
                        $id_groups_array = explode( ',', $resultBookGroup[0]->id_book_links); 
                        for($i = 0; $i < count($id_groups_array); $i++){ 
                            $id_book_temp = $id_groups_array[$i];
                            $sql = "INSERT INTO $this->table_class
                                    (id_teacher, code, name, description, id_scholl, id_book, id_book_group, inserted)
                                    VALUES (?,?,?,?,?,?,?,?)";
                        
                            $this->dbpe->prepare($sql)
                                 ->execute(
                                    array(
                                        $dataUserReigster["id_user"],
                                        $code,
                                        $data['name'], 
                                        isset($data['description']) ? $data['description'] : '',
                                        $data['id_scholl'],                                        
                                        $id_book_temp, 
                                        $id_book_group,
                                        date('Y-m-d G:H:i')
                                    )
                                ); 
                            $idresponse = $this->dbpe->lastInsertId();    
                            if( $idresponse > 0 ){
                                $cont_success++;
                                array_push($ids_inserted, $idresponse);
                                $this->recreateResourcesBase($idresponse, $id_book_temp); 
                            } 
                        }           
                    }  

                } 
 
                
                if($mode == 'new' && count($ids_inserted) == count($resultBookGroup)){                          
                    $this->response->result = array('id' =>  $ids_inserted,'code' => $code ); 
                    $this->response->setResponse(true); 
                }else if($mode == 'edit' && $idresponse > 0){ 
                    $this->response->result = array('id' =>  $idresponse ); 
                    $this->response->setResponse(true); 
                } 
                else
                {
                    $this->response->setResponse(false); 
                } 
                return $this->response;
                
            }catch (Exception $e) 
            {
                $this->response->setResponse(false, $e->getMessage()); 
            }
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

    private function getBook($id_book)
    { 
        try
        {
            $resultBook = array(); 
            $stm = $this->db->prepare("SELECT id, code, name FROM $this->table_book WHERE id = ?"); 
            $stm->execute(array($id_book)); 
            $resultBook = $stm->fetch();
            return $resultBook;
        }
        catch(Exception $e)
        {
            return array();
        }
    }

    private function recreateResourcesBase($id_class, $id_book)
    {
        try
        {
            $resources = array();
            $id_user = $this->token_data->id;
            $this->dbpeTemp = Database::StartUpArea($this->bd_base_pe); 
            $stm = $this->dbpeTemp->prepare("SELECT * FROM $this->table_resources WHERE id_book = ?");                   
            $stm->execute(array($id_book));
            $resources = $stm->fetchAll(); 
            $arrayInsert = array();

            $contInsert = 0;

            $this->dbpeTemp = Database::StartUpArea($this->token_data->amb); 
            foreach ($resources as $row) {   
               $sql = "INSERT INTO $this->table_resources
                                (code, name, description, id_book, id_unity, page, type, time_band, time, button_color, button_title, button_left, button_top, button_icon, head_img_path, head_style, url, text_extra, id_class, id_session, id_calification_type, id_user, inserted)
                                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
                    
                $this->dbpe->prepare($sql)
                     ->execute(
                        array(
                            $row->code,
                            $row->name, 
                            $row->description,
                            $row->id_book,
                            $row->id_unity,
                            $row->page,
                            $row->type,
                            $row->time_band,
                            $row->time,
                            $row->button_color,
                            $row->button_title,
                            $row->button_left,
                            $row->button_top,
                            $row->button_icon,   
                            $row->head_img_path,   
                            $row->head_style,
                            $row->url,
                            $row->text_extra,
                            $id_class,
                            $row->id_session,
                            $row->id_calification_type,
                            $id_user,
                            date('Y-m-d G:H:i')
                        )
                    ); 
                $idresponse = $this->dbpe->lastInsertId();  
                if($idresponse > 0)
                {
                    $contInsert++;
                }
            }

            if($contInsert == count($resources)){
                return array("success" => true, "total" => $contInsert);
            }  

            return array("success" => false);
        }
        catch (Exception $e)
        {
            return array("success" => false);
        }
    }

    private function registerJoinUserScholl($idm, $id_scholl, $amb, $id_user_link)
    {   
         
        try
        {  
            $resultJoinInScholl = $this->checkJoinUserScholl($idm, $id_scholl, $amb, $id_user_link);
            if(!$resultJoinInScholl["success"])
            {
                $sql = "INSERT INTO $this->table_user_master_scholl
                            (id_user_master, id_scholl, id_user_link, inserted)
                            VALUES (?,?,?,?)";
                
                $this->dbmaster->prepare($sql)
                     ->execute(
                        array(
                            $idm,
                            $id_scholl, 
                            $id_user_link,
                            date('Y-m-d G:H:i')
                        )
                    ); 
                $idresponse = $this->dbmaster->lastInsertId();
                if($idresponse > 0)
                {
                    $checkUser = $this->checkUserAmb($idm, $amb, $id_user_link);
                    return $checkUser;     
                } 
            }

            return $resultJoinInScholl;

        }
        catch (Exception $e) 
        {
            return $resultJoinInScholl;
        }
    }

    private function checkJoinUserScholl($idm, $id_scholl, $amb, $id_user_link)
    {   
        
        try
        { 
            $resultJoinScholl = array();
            $stm = $this->dbmaster->prepare("SELECT um.id_user_link FROM $this->table_user_master_scholl ums INNER JOIN $this->table_user_master um on ums.id_user_master = um.id WHERE ums.id_user_master = ? and ums.id_scholl = ?"); 
            $stm->execute(array($idm, $id_scholl));
            $resultJoinScholl = $stm->fetchAll();   
            if(count($resultJoinScholl) > 0)
            { 
                $checkUser = $this->checkUserAmb($idm, $amb, $resultJoinScholl[0]->id_user_link);
                if($checkUser["success"])
                {
                    return $checkUser;
                }                 
            }
            return array("success" => false);
        }
        catch (Exception $e) 
        {
            return array("success" => false);
        }
    }

    private function checkUserAmb($idm, $amb, $id_user_link)
    {   
        
        try
        { 
            //get amb del id_scholl
            /*$id_user = 0;
            $resultJoinScholl = array();
            $stm = $this->dbmaster->prepare("SELECT amb FROM $this->table_scholls WHERE id = ?"); 
            $stm->execute(array($id_scholl));
            $resultJoinScholl = $stm->fetchAll();
            if(count($resultJoinScholl) > 0)*/ 
            if(trim($amb) != "")
            {
                //verificar si esta registrado como usuario en ese amb
                $this->dbpeTemp = Database::StartUpArea($amb);
                $resultUserAmb = array();
                $stm = $this->dbpeTemp->prepare("SELECT id, first_name, last_name, email, id_type FROM $this->table_user WHERE id_user_master = ?"); 
                $stm->execute(array($idm));
                $resultUserAmb = $stm->fetchAll(); 
                
                if(count($resultUserAmb) > 0)
                {
                    //si existe obtiene id usuario en ese amb
                    $id_user = $resultUserAmb[0]->id;
                }
                else
                {
                    //si no existe registra al usuario en el amb y obtiene el id
                    $id_user = $this->registerUserAmb($amb, $idm, $id_user_link);
                }
            } 
             
            if( $id_user != 0 )
            {
                return array("success" => true, "id_user" => $id_user);
            }
            return array("success" => false);
        }
        catch (Exception $e) 
        {
            return array("success" => false);
        }
    }

    private function registerUserAmb($amb, $id_user_master, $id_user_link)
    { 
        try
        {  
            $resultUserAmb = array();
            $this->dbpeTemp = Database::StartUpArea($this->bd_base_pe);
            $stm = $this->dbpeTemp->prepare("SELECT id, first_name, last_name, email, id_type FROM $this->table_user WHERE id = ?"); 
            $stm->execute(array($id_user_link));
            $resultUserAmb = $stm->fetch();

            $this->dbpeTemp = Database::StartUpArea($amb);
            $sql = "INSERT INTO $this->table_user
                        (id_user_master, id_user_link, email, first_name, last_name, id_type, inserted)
                        VALUES (?,?,?,?,?,?,?)";
            
            $this->dbpeTemp->prepare($sql)
                 ->execute(
                    array(
                        $id_user_master,
                        $id_user_link,
                        $resultUserAmb->email,
                        $resultUserAmb->first_name,
                        $resultUserAmb->last_name,
                        $resultUserAmb->id_type, 
                        date('Y-m-d G:H:i')
                    )
                ); 
            $idresponse = $this->dbpeTemp->lastInsertId();
            if($idresponse > 0)
            {
                return $idresponse;     
            }
            else
            {
                return false;
            }
        }
        catch (Exception $e) 
        {
            return false;
        }
    }
   
    private function registerClassMaster($amb, $code_class)
    { 
        try
        {   
            $id_user_master = $this->token_data->idm;
            $id_user = $this->token_data->id;
            $sql = "INSERT INTO $this->table_class_master
                        (amb, code_class, id_user_master, id_user, inserted)
                        VALUES (?,?,?,?,?)";
            
            $this->dbmaster->prepare($sql)
                 ->execute(
                    array(
                        $amb,
                        $code_class,
                        $id_user_master,
                        $id_user,
                        date('Y-m-d G:H:i')
                    )
                ); 
            $idresponse = $this->dbmaster->lastInsertId();
            if($idresponse > 0)
            {
                return ( $idresponse > 0 ) ? true : false;     
            }
            else
            {
                return false;
            }
        }
        catch (Exception $e) 
        {
            return false;
        }
    }
   

    private function generateCodeClass()
    {
        $code = '';
        $pattern1 = 'ABCDEFGHJKLMNPQRSTVWZ';
        $pattern2 = '123456789';
        $max1 = strlen($pattern1)-1;
        $max2 = strlen($pattern2)-1;

        for($i=0;$i < $this->lengh_letter_code_class;$i++) 
            $code .= $pattern1{mt_rand(0,$max1)};

        for($i=0;$i < $this->lengh_number_code_class;$i++) 
            $code .= $pattern2{mt_rand(0,$max2)};  


        $resultClass = (object)[];
        $stm = $this->dbmaster->prepare("SELECT code_class FROM $this->table_class_master WHERE code_class = ?"); 
        $stm->execute(array($code));
        $resultClass = $stm->fetchAll();
        if(count($resultClass) > 0)
        {
            return generateCodeClass();
        }

        return $code;
    }

    public function checkCodeClass($code)
    {
        try
        {
            $resultClass = array();
            $stm = $this->dbmaster->prepare("SELECT s.id, s.name, s.amb, cm.code_class FROM $this->table_class_master cm INNER JOIN $this->table_scholls s on cm.amb = s.amb WHERE code_class = ?"); 
            $stm->execute(array($code));
            $resultClass = $stm->fetchAll(); 
            if(count($resultClass) > 0)
            {  
                $this->dbpeTemp = Database::StartUpArea($resultClass[0]->amb);
                $stm = $this->dbpeTemp->prepare("SELECT c.code, c.id, u.id id_teacher, u.first_name, u.last_name, c.name, '".$resultClass[0]->name."' name_scholl, '".$resultClass[0]->amb."' code_scholl, '".$resultClass[0]->id."' id_scholl FROM $this->table_user u INNER JOIN $this->table_class c on u.id = c.id_teacher WHERE c.code = ?"); 
                $stm->execute(array($code));  
                $this->response->result = $stm->fetch();
                $this->response->setResponse(true);
            }
            else
            {
                $this->response->setResponse(false);
            }
            return $this->response;
        }
        catch (Exception $e) 
        {
            return $this->response;
        }
    }

    public function JoinA($data)
    { 
 
        if( $data == null ) 
        {
            $this->response->setResponse(false, "Error, no data");
            return $this->response;
        }
        else
        {
             
            try 
            {
                $id = $data["id"]; 
                $code = $data["code"];  
                $amb = $data["code_scholl"]; 
                $id_scholl = $data["id_scholl"]; 
                $id_teacher = $data["id_teacher"]; 

                //$checkUser = $this->checkUserAmb($this->token_data->idm, $amb, $this->token_data->id);
                $checkUser = $this->registerJoinUserScholl($this->token_data->idm, $id_scholl, $amb, $this->token_data->id);
                //var_dump($checkUser);
                if($checkUser["success"])
                {
                    $userClassJoined = $this->checkJoinUserClass($checkUser["id_user"], $code, $amb);
                    if(!$userClassJoined)
                    {
                        $this->dbpe = Database::StartUpArea($amb);
                        $sql = "INSERT INTO $this->table_user_class
                                    (id_user, id_class, code_class, date_joined)
                                    VALUES (?,?,?,?)";
                        
                        $this->dbpe->prepare($sql)
                             ->execute(
                                array(
                                    $checkUser["id_user"],
                                    $id,
                                    $code,  
                                    date('Y-m-d G:H:i')
                                )
                            ); 
                        $idresponse = $this->dbpe->lastInsertId();

                        if( $idresponse > 0 )
                        {  
                             $notifData = array(                                 
                                array( "name" => "alumn", "id" => $checkUser["id_user"] ),
                                array( "name" => "class", "id" => $id )
                            ); 
                    
                            $linkData = array("amb" => $amb, "code_class" => $code, "code_alumn" => $checkUser["id_user"] . '_' . uniqid());  

                            $this->notification->privateRegister($this->notification->alumn_class_join, $id_teacher, $amb, $notifData, $checkUser["id_user"], $linkData);

                            $this->response->result = array('id' =>  $idresponse, 'duplicate' => false );
                            $this->response->setResponse(true); 
                        }
                        else
                        {
                            $this->response->setResponse(false); 
                        } 
                    }
                    else
                    {
                            $this->response->result = array('duplicate' =>  true );
                            $this->response->setResponse(true); 
                    }
                }
                else
                {
                    $this->response->setResponse(false); 
                } 
                
                return $this->response;
                
            }catch (Exception $e) 
            {
                $this->response->setResponse(false, $e->getMessage());
            }
        } 
    }

    public function checkJoinUserClass($id_user, $code_class, $amb = '')
    {
        $resulUserClass = array(); 
        $this->dbpeTemp = Database::StartUpArea($amb);
        $stm = $this->dbpeTemp->prepare("SELECT * FROM $this->table_user_class WHERE id_user = ? and code_class = ?");
        $stm->execute(array($id_user, trim($code_class)));
        $resulUserClass = $stm->fetchAll();
        if( count($resulUserClass) > 0){
             return true;
        }   
        return false;
    }

}
 