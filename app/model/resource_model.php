<?php
namespace App\Model;

use App\Lib\Database;
use App\Lib\Response;

require '../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception; 


class ResourceModel extends GeneralConfig 
{
    private $db;
    private $dbpeTemp;
    private $dbmaster;

    private $table;
    private $table_type;
    private $table_upload;
    private $table_join_indicators;
    

    private $notification; 
    
    public function __CONSTRUCT($token_data = array())
    { 
        //$this->dbpe = Database::StartUpPe();
        $this->dbpe = Database::StartUpArea( isset($token_data->amb) ? $token_data->amb : $this->bd_base_pe );
        $this->response = new Response();
        $this->notification = new NotificationModel($token_data);
        $this->token_data = $token_data;

        $this->table = $this->table_resources;
        $this->table_type = $this->table_resources_type;
        $this->table_upload = $this->table_resources_upload;
        $this->table_join_indicators = $this->table_resources_indicator; 
    }
    
    public function GetAll()
    {
		try
		{
			$result = array();

			$stm = $this->dbpe->prepare("SELECT * FROM $this->table");
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

    public function GetAllActivitys()
    {
        try
        {
            $result = array();

            $stm = $this->dbpe->prepare("SELECT * FROM $this->table where type in (1,2)");
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

    public function getIdBase($code){
        try
        {
            $this->dbpeTemp = Database::StartUpArea($this->bd_base_pe); 
            $stm = $this->dbpeTemp->prepare("SELECT id FROM $this->table where code = ?");
            $stm->execute(array($code));
            
            return $stm->fetch()->id; 
        }
        catch(Exception $e)
        {   
            return false;
        }
    }
    
    public function Get($id)
    {
		try
		{
			$result = array();

			$stm = $this->dbpe->prepare("SELECT * FROM $this->table WHERE id = ?");
			$stm->execute(array($id));

			$this->response->setResponse(true);
            $thedata = $stm->fetch();
            $thedata->indicators_count = $this->getNumIndicators($id);
            $this->response->result = $thedata;
            
            return $this->response;
		}
		catch(Exception $e)
		{
			$this->response->setResponse(false, $e->getMessage());
            return $this->response;
		}  
    }

    public function getNumIndicators($id_resource){
        try
        {
            $result = array();

            $stm = $this->dbpe->prepare("SELECT count(*) FROM $this->table_join_indicators WHERE id_resource = ?");
            $stm->execute(array($id_resource));  
            return  $stm->fetchColumn();
        }
        catch(Exception $e)
        { 
            return 0;
        } 
    }

    public function GetIndicators($id_resource)
    {
        try
        {
            $result = array(); 
            $stm = $this->dbpe->prepare("SELECT id.id id, id.name name, cp.id id_capacity, cp.name name_capacity, co.id id_competition, co.name name_competition FROM $this->table_join_indicators ji inner join $this->table_indicators id on ji.id_indicator = id.id inner join $this->table_capacitys cp on id.id_capacity = cp.id inner join $this->table_competitions co on cp.id_competition = co.id  WHERE ji.id_resource = ?");

            $stm->execute(array($id_resource));

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

    public function SetStatus($data)
    {
        try 
        {
            if(isset($data['id']) && isset($data['status']))
            {
                $sql = "UPDATE $this->table SET 
                            status      = ?,
                            updated     = ?
                        WHERE id = ?";
                
                $this->dbpe->prepare($sql)
                     ->execute(
                        array(
                            $data['status'], 
                            date('Y-m-d G:H:i'),
                            $data['id']
                        )
                    );
            }else{
                $this->response->setResponse(false, 'Error, wrong Data');
                return $this->response;
            }
 
            $this->response->setResponse(true);
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
            $stm = $this->dbpe->prepare("SELECT * FROM $this->table WHERE id_book = ? and status = 1"); 
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

    public function byType($id_type)
    {
        try
        {
            $result = array(); 
            $stm = $this->dbpe->prepare("SELECT * FROM $this->table WHERE id_type = ? and status = 1"); 
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

    public function byBookAdmin($id_book)
    {
        try
        {
            $result = array(); 
            $stm = $this->dbpe->prepare("SELECT * FROM $this->table WHERE id_book = ? "); 
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

    public function byUnity($id_unity, $class_code)
    {
        try
        {
            if($class_code == 'base'){ 
                 $stm = $this->dbpe->prepare("SELECT r.id, r.code, r.name, r.description, r.id_unity, r.id_book, r.type, r.value, r.page, r.time_band, r.time, r.button_color, r.button_title, r.button_left, r.button_top, r.button_icon, r.head_img_path, r.head_style, r.url, r.text_extra, r.status, r.id_calification_type 
                    FROM $this->table r 
                    WHERE r.id_unity = ? and r.status = 1 ORDER BY r.page");  
                $stm->execute(array($id_unity));
                $result = $stm->fetchAll();  
            }else{
                $amb = $this->token_data->amb;  
                $theClass = array(); 
                $stm = $this->dbpe->prepare("SELECT * FROM $this->table_class WHERE code = ?"); 
                $stm->execute(array($class_code)); 
                $theClass = $stm->fetch();

                $id_user = $this->token_data->id; 
                $result = array(); 
                $stm = $this->dbpe->prepare("SELECT r.id, r.code, r.name, r.description, r.id_unity, r.id_book, r.type, r.value, r.page, r.time_band, r.time, r.button_color, r.button_title, r.button_left, r.button_top, r.button_icon, r.head_img_path, r.head_style, r.url, r.text_extra, r.status, r.id_calification_type, qj.score, qj.id_score_letter, qj.date_scored, qj.inserted date_resolved, IF(qj.id > 0, 1, 0) resolved, r.id_class, IF(qj.status is null, 0, qj.status) estatus_evaluate, qj.id question_id, qj.code question_code 
                    FROM $this->table r 
                    LEFT JOIN $this->table_question_join qj on (r.id = qj.id_resource and qj.id_user = ?) 
                    WHERE r.id_unity = ? and r.status = 1 and r.id_class = ? ORDER BY r.page");  
                $stm->execute(array($id_user, $id_unity, $theClass->id));
                $result = $stm->fetchAll();  
            } 

            foreach ($result as $row) {
               if( $row->type == '4'){
                    $row->files = $this->getResourseUpload($row->id, false); 
               }
            } 


            $this->response->setResponse(true);
            $this->response->result = $result;
            
            return $this->response;
        }
        catch(Exception $e)
        {
            $this->response->setResponse(false, $e->getMessage());
            return $this->response;
        }  
    }   

    public function byUnityActivity($id_unity)
    {
        try
        {
            $result = array(); 
            $stm = $this->dbpe->prepare("SELECT * FROM $this->table WHERE id_unity = ? and type in (1,2) and status = 1"); 
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

    public function byUnityAdmin($id_unity)
    {
        try
        {

            $result = array(); 
            $stm = $this->dbpe->prepare("SELECT r.*, rt.name type_name, date_format(r.inserted, '%e/%c/%Y a las %l:%i %p') date_inserted, s.name name_session, s.number number_session, u.first_name, u.last_name, u.id id_user
                                        FROM $this->table r 
                                        INNER JOIN $this->table_type rt on r.type = rt.id 
                                        LEFT JOIN $this->table_sessions s on r.id_session = s.id 
                                        LEFT JOIN $this->table_user u on r.id_user = u.id 
                                        WHERE r.id_unity = ?"); 
            $stm->execute(array($id_unity));
            $result = $stm->fetchAll();

            $this->response->setResponse(true); 
            $this->response->result = $result; 
            
            return $this->response;
        }
        catch(Exception $e)
        {
            $this->response->setResponse(false, $e->getMessage());
            return $this->response;
        }  
    }



    public function byUnityAdminActivity($id_unity)
    {
        try
        {
            $result = array(); 
            $stm = $this->dbpe->prepare("SELECT * FROM $this->table WHERE id_unity = ? and type in (1,2)"); 
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
        $id_user = $this->token_data->id;
        $amb = $this->token_data->amb;
        if( $data == null ) 
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
                                type        = ?,
                                time_band   = ?,
                                time        = ?, 
                                button_color= ?, 
                                button_title= ?, 
                                button_left = ?, 
                                button_top  = ?, 
                                button_icon = ?,
                                head_img_path  = ?, 
                                head_style  = ?, 
                                url         = ?,
                                text_extra  = ?,
                                id_class    = ?,
                                id_session  = ?,
                                id_calification_type = ?,
                                updated     = ?
                            WHERE id = ?";
                    
                    $this->dbpe->prepare($sql)
                         ->execute(
                            array(
                                $code,
                                $data['name'], 
                                $data['description'],
                                $data['id_book'],
                                $data['id_unity'],
                                $data['page'],
                                $data['type'],
                                ( $data['time_band'] == false || $data['time_band'] == 0 ) ? 0 : 1,
                                $data['time'], 
                                $data['button_color'],
                                $data['button_title'],
                                $data['button_left'],
                                $data['button_top'],
                                $data['button_icon'], 
                                $data['head_img_path'],   
                                $data['head_style'],
                                $data['url'],
                                $data['text_extra'],
                                $data['id_class'],
                                ( $data['id_session'] == '' ) ? NULL : $data['id_session'] == '',
                                $data['id_calification_type'],
                                date('Y-m-d G:H:i'),
                                $data['id']
                            )
                        );
                    $idresponse = $data['id'];
                }
                else
                {
                    $sql = "INSERT INTO $this->table
                                (code, name, description, id_book, id_unity, page, type, time_band, time, button_color, button_title, button_left, button_top, button_icon, head_img_path, head_style, url, text_extra, id_class, id_session, id_calification_type,id_user, inserted)
                                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?, ?,?)";
                    
                    $data['time_band'] = ($data['time_band'] == false) ? 0 : 1;
                    $this->dbpe->prepare($sql)
                         ->execute(
                            array(
                                $code,
                                $data['name'], 
                                $data['description'],
                                $data['id_book'],
                                $data['id_unity'],
                                $data['page'],
                                $data['type'],
                                $data['time_band'],
                                $data['time'],
                                $data['button_color'],
                                $data['button_title'],
                                $data['button_left'],
                                $data['button_top'],
                                $data['button_icon'],   
                                $data['head_img_path'],   
                                $data['head_style'],
                                $data['url'],
                                $data['text_extra'],
                                $data['id_class'],
                                ( $data['id_session'] == '' ) ? NULL : $data['id_session'] == '',
                                $data['id_calification_type'],
                                $id_user,
                                date('Y-m-d G:H:i')
                            )
                        ); 
                    $idresponse = $this->dbpe->lastInsertId();                
                } 
 
                if( $data['type'] == '4'){
                    return $this->uploadFiles($data, $idresponse, $code);
                }else{
                    if( $data['indicators_count'] > 0){
                        if( $this->setIndicators($idresponse, $data['indicators']) > 0){
                                $this->response->result = array('id' =>  $idresponse,'code' => $code );
                                $this->response->setResponse(true);
                                return $this->response;
                        }
                    }
                    if( $data['is_linked'] == 1 ){
                        $this->removeActiviysJoinLinked($idresponse);
                    }  

                    if($idresponse > 0){
                        $this->response->result = array('id' =>  $idresponse,'code' => $code );
                        $this->response->setResponse(true);
                        return $this->response;
                    }else{
                        $this->response->setResponse(false);
                        return $this->response;
                    }
                    
                } 
                
            }catch (Exception $e) 
            {
                $this->response->setResponse(false, $e->getMessage());
                return $this->response;
            }
        } 
    }

    public function removeActiviysJoinLinked($id_resource)
    {
        $stm = $this->dbpe->prepare("DELETE FROM $this->table_question_join WHERE id_resource = ?");   
        $stm->execute(array($id_resource));

        $stm = $this->dbpe->prepare("DELETE FROM $this->table_question_evaluate WHERE id_resource = ?");   
        $stm->execute(array($id_resource));
    }

    public function setIndicators($id_resource, $dataIndicators)
    {
        $count = 0;
        foreach ($dataIndicators as $key => $value) { 
              $sql = "INSERT INTO $this->table_join_indicators
                                (id_resource, id_indicator, inserted)
                                VALUES (?,?,?)"; 
                    $this->dbpe->prepare($sql)
                         ->execute(
                            array( 
                                $id_resource,
                                $value['id'],
                                date('Y-m-d G:H:i')
                            )
                        ); 
                    $idresponse = $this->dbpe->lastInsertId(); 
                if($idresponse > 0){
                    $count++;
                }
        }

        return $count;
    }

    public function uploadFiles($data, $iddb, $code)
    {    
 

        if($data == null)
        {
            $this->response->setResponse(false, "Error, no data");
            return $this->response;
        }
        else
        {   
            try 
            {
                if(isset($data['titles']) && $iddb !== null)
                {   
                    $myFiles = $_FILES['file']; 
                    $id_book = $data['id_book'];
                    $id_unity = $data['id_unity'];
                    $titles = $data['titles'];
                    $filesUpload = array();
                    $names = array();
                    $fullpass = true;
                    $total_upload = 0;
                    $final_path = $this->path_upload_pecontent . $id_book . '-' . $id_unity . '/';

                    if (!file_exists($final_path)) {
                        mkdir($final_path, 0777, true);
                    }

                    for($i = 0; $i < count($myFiles['tmp_name']); $i++ ){
                        $tmp_name = $myFiles['tmp_name'][$i];
                        $title = $titles[$i];
                        $name_file = pathinfo($myFiles['name'][$i]);
                        $ext = $name_file['extension'];
                        $codeu = uniqid();
                        $final_name = $code . '-' .  $i . $codeu . '.' . $ext;
                        if( move_uploaded_file( $tmp_name , $final_path . $final_name ) != false ){  
                            array_push($filesUpload, $final_name);
                            array_push($names, $data['titles'][$i]);
                            $total_upload++;
                        } else {
                            $fullpass = false; 
                        }
                    } 

                    return $this->registerUploadResources($iddb, $code, $id_book, $id_unity, $names, $filesUpload);
                   
                }
                else{
                    $this->response->setResponse(false, 'Error data format ');
                } 
            }catch (Exception $e) 
            {
                $this->response->setResponse(false, $e->getMessage());
            }
        } 
    } 

    public function registerUploadResources($id_resource, $code_resource, $id_book, $id_unity, $names, $filesUpload){
        $saved = 0;
        for($i = 0; $i < count($filesUpload); $i++ ){
           $insert =  $this->refreshUploadBD($id_resource, $id_book . '-' . $id_unity , $names[$i], $filesUpload[$i]);
           if($insert){
                $saved++;
           }
        }

        if($saved == count($filesUpload)){
            $this->response->result = array('id' =>  $id_resource,'code' => $code_resource , 'total_upload'=> $saved); 
            $this->response->setResponse(true);  
            return $this->response;
        }else{
            $this->response->setResponse(false);  
            return $this->response;
        }        
    }

    public function refreshUploadBD($id_resource, $folder, $name, $filename){ 
        $this->CleanResourceUpload($id_resource); 
        $sql = "INSERT INTO $this->table_upload (id_resource, folder, name, filename, inserted) VALUES (?,?,?,?,?)";
        return $this->dbpe->prepare($sql)
             ->execute(
                array(
                    $id_resource,
                    $folder,
                    $name, 
                    $filename,
                    date('Y-m-d G:H:i')
                )
            ); 
    }

    public function CleanResourceUpload($id_resource, $returnFinal = true)
    {
        try 
        { 

            $stm = $this->dbpe->prepare("SELECT * FROM $this->table_upload WHERE id_resource = ?"); 
            $stm->execute(array($id_resource)); 
            $dataClean = $stm->fetchAll(); 

            foreach ($dataClean as $key => $value) { 
                if(file_exists($this->path_upload_pecontent . $value->folder . '/' . $value->filename)){
                    unlink($this->path_upload_pecontent . $value->folder . '/' . $value->filename);
                } 
            }

            $stm = $this->dbpe->prepare("DELETE FROM $this->table_upload WHERE id_resource = ?");   
            $stm->execute(array($id_resource));
            
            $this->response->setResponse(true);

            if($returnFinal)
                return $this->response;
            else
                return true;
        } catch (Exception $e) 
        {
            if($returnFinal)
                $this->response->setResponse(false, $e->getMessage());
            else
                return false;
        }
    }

    public function SaveQuestion($data){
        //var_dump($data);
        if($data == null)
        {
            $this->response->setResponse(false, "Error, no data");
            return $this->response;
        }
        else
        {
            $id_user = $this->token_data->id; 
            $amb = $this->token_data->amb;
            $id_teacher = $data['class']['id_teacher'];
            $class_code = $data['class']['code'];
            $class_id = $data['class']['id'];
            $id_activity =  $data['activity']['id'];
            $id_book = $data['activity']['id_book'];

            $code = uniqid();
            try 
            {
                $code = uniqid();
                $code = $code . '.' . $this->token_data->id;
                $sql = "INSERT INTO $this->table_question_join
                            (id_resource, code, id_user, code_class, id_class, times,  inserted)
                            VALUES (?,?,?,?,?,?,?)";
                
                $this->dbpe->prepare($sql)
                     ->execute(
                        array(
                            $id_activity, 
                            $code,
                            $id_user,
                            $class_code,  
                            $class_id,
                            $data['activity']['times'],
                            date('Y-m-d G:H:i')
                        )
                );   

                $idresponse = $this->dbpe->lastInsertId();

                $dataQuestionsBD = array();
                $arraySaveType = array('file', 'uploader', 'input', 'radio-group', 'checkbox-group', 'select', 'textarea', 'date', 'text', 'dragg');
                $arrayLinkType = array('file', 'uploader');

                $contQD = 0;
                $contRQD = 0; 

                foreach ($data["dataForm"] as $key => $linea) { 
                    //$cont++;  
                    if(in_array($linea["type"], $arraySaveType)){
                        $contQD++;
                        $value = ( in_array( $linea["type"], $arrayLinkType) ) ? $this->shortLink($linea["value"]) : $linea["value"];
                        $resultQD = $this->insertQuestionDetail($idresponse, $data['activity']['id'], $id_user, $linea["type"], $linea["name"], $value, $linea["puntaje"]);
                        if($resultQD)
                        {
                            $contRQD++;
                        }
                    }  
                } 

                if($contQD == $contRQD)
                {
                    $filename =  $data['activity']['id'] . "_" . $idresponse . "_" . $code . "_question.json";
                    $path = $this->path_upload_activitys_question . $filename; 

                    $saveJson = $this->saveJson( $data['activity']['id'], $path, $data['dataForm'], false);
     
                    //Notificacion resolucion de actividad
                    $notifData = array(                                 
                                array( "name" => "alumn", "id" => $id_user ),
                                array( "name" => "resource", "id" => $id_activity ),
                                array( "name" => "class", "id" => $class_id ),
                                array( "name" => "book", "id" => $id_book )
                            ); 
                    
                    $linkData = array("amb" => $amb, "code_class" => $class_code, "code_alumn" => $id_user . '_' . uniqid(), "code_question" =>  $idresponse . "_" . $code);  

                    $this->notification->privateRegister($this->resolve_activity_join, $id_teacher, $amb, $notifData, $id_user, $linkData);
                    
                    $this->response->result = array('id' =>  $idresponse ,'code' => $code, 'saveJson' => $saveJson );
                    $this->response->setResponse(true);
                    
                }
                else
                { 
                    
                    $this->logger->error("Save Detail Question", $data); 
                    $this->response->setResponse(false);
                }
                return $this->response;
            }catch (Exception $e) 
            {
                $this->response->setResponse(false, $e->getMessage());
                return $this->response;
            }
        } 
    }

    public function SaveQuestionEvaluation($data)
    {
        try 
        {
            if(isset($data) && $data !== null)
            {   

                $id_question = intval(substr($data['code'], 0, strpos($data['code'], "_") ));    
                $code_question = substr($data['code'], strpos($data['code'], "_") + 1 );

                $class_code = $data["class_code"];
                $id_class = $data["class_id"];
                $id_alumn = $data["id_alumn"];
                $id_teacher = $this->token_data->id;
                $comment = $data["comment"];
                $id_score_letter = $data["id_score_letter"];
                $score = $data["score"];
                $status_evaluate = 2;
                $id_resource = $data["id_resource"]; 
                $id_book = $data["id_book"]; 
                $amb = $this->token_data->amb;

                $log = $this->SaveQuestionEvaluationLog($id_question, $score, $id_score_letter, $comment, $id_alumn);

                $saveDetail = $this->SaveQuestionDetail($data); 

                if($log && $saveDetail)
                { 
                    $sql = "UPDATE $this->table_question_join SET 
                                score = ?,
                                id_score_letter = ?, 
                                comment = ?,
                                date_scored = ?,
                                status = ?
                            WHERE 
                                id = ? and 
                                code = ? and 
                                code_class = ? and 
                                id_user = ?";
                    
                    $stm = $this->dbpe->prepare($sql);
                    $stm->execute(
                            array(
                                $score,
                                $id_score_letter,
                                $comment,
                                date('Y-m-d G:H:i'),
                                $status_evaluate,
                                $id_question,
                                $code_question,
                                $class_code,
                                $id_alumn
                            )
                        );
 
                    $totalUpdate = $stm->rowCount();  
                    if($stm){ 
                        
                        $notifData = array(                                 
                                array( "name" => "teacher", "id" => $id_teacher ),
                                array( "name" => "resource", "id" => $id_resource ),
                                array( "name" => "class", "id" => $id_class ),
                                array( "name" => "book", "id" => $id_book )
                            ); 
                    
                        $linkData = array("amb" => $amb, "code_class" => $class_code, "code_question" =>  $id_question . "_" . $code_question);  

                        $this->notification->privateRegister($this->evaluation_realized_teacher, $id_alumn, $amb, $notifData, $id_teacher, $linkData);


                        $this->response->result = array("affected"=>$totalUpdate);
                        $this->response->setResponse(true);
                        return $this->response;
                    } 
                }

                $this->response->setResponse(false);
                return $this->response;
            }
        }
        catch (Exception $e) 
        {
            $this->response->setResponse(false, $e->getMessage());
            return $this->response;
        } 
    }

    public function SaveQuestionDetail($data)
    {
        try 
        {   
            $totalUpdate = 0; 
            $detail = $data["detail"];
            for($i=0; $i< count($detail); $i++)
            {
                $id = $detail[$i]["id_question_detail"];
                $score = $detail[$i]["score"];
                $id_score_letter = $detail[$i]["id_score_letter"];
                $comment = $detail[$i]["comment"];
                $id_resource = $data["id_resource"]; 

                $sql = "UPDATE $this->table_question_evaluate SET 
                                score = ?,
                                id_score_letter = ?, 
                                comment = ?  
                        WHERE 
                            id = ? and 
                            id_resource = ?";
                
                $stm = $this->dbpe->prepare($sql);
                $stm->execute(
                        array(
                            $score,
                            $id_score_letter,
                            $comment,
                            $id, 
                            $id_resource 
                        )
                    ); 
                $totalUpdate+=$stm->rowCount();
                $pass = ($stm) ? true : false;
            }

            if($pass)
                return true;

            return false;
        }
        catch (Exception $e) 
        {
            return false;
        } 
    }

    public function GetLogQuestionEvaluation($code_question)
    { 
        try 
        { 
            $id_question = intval(substr($code_question, 0, strpos($code_question, "_") ));

            $result = array(); 
            $stm = $this->dbpe->prepare("SELECT id, comment, id_teacher, id_user, id_score_letter, score,  date_format(date_scored, '%e/%c/%Y a las %l:%i %p') date_scored FROM $this->table_question_evaluate_log WHERE id_question = ? order by id desc");
            $stm->execute(array($id_question)); 
            $result = $stm->fetchAll();  


            $this->response->result = $result;
            $this->response->setResponse(true);
            return $this->response;  
        } 
        catch (Exception $e) 
        {
            $this->response->setResponse(false);
            return $this->response;  
        } 
    }

    private function SaveQuestionEvaluationLog($id_question, $score, $id_score_letter, $comment, $id_user)
    {
        try 
        { 
            $id_teacher = $this->token_data->id;
            $sql = "INSERT INTO $this->table_question_evaluate_log
                            (id_question, score, id_score_letter, comment, id_user, id_teacher, date_scored)
                            VALUES (?,?,?,?,?,?,?)"; 
                $this->dbpe->prepare($sql)
                     ->execute(
                        array( 
                            $id_question,
                            $score,
                            $id_score_letter,
                            $comment,
                            $id_user,
                            $id_teacher,
                            date('Y-m-d G:H:i')
                        )
                    ); 
                $idresponse = $this->dbpe->lastInsertId(); 

            if($idresponse > 0){
                return true; 
            }

            return false;  

        } 
        catch (Exception $e) 
        {
            return false;
        } 
    }


    public function insertQuestionDetail($id_question, $id_resource, $id_user, $type, $code, $value, $puntaje){ 
        try 
        {
            if( is_array($value)  ){
                $object = (object) $value;
                $value = serialize($object); 
            }
            $sql = "INSERT INTO $this->table_question_evaluate
                                (id_resource, id_user, id_question, type, code, value, value_score, inserted)
                                VALUES (?,?,?,?,?,?,?,?)";
                    
                    $this->dbpe->prepare($sql)
                         ->execute(
                            array(
                                $id_resource,  
                                $id_user,
                                $id_question,
                                $type,
                                $code,  
                                $value,
                                ($puntaje == null || $puntaje == '') ? 0 : $puntaje, 
                                date('Y-m-d G:H:i')
                            )
                    );   
            return true;
        }
        catch (Exception $e) 
        {
            return false;
        }  
    }

    public function GetActivityQuestion($code)
    {
        try 
        { 

            $resultHead = array();
            $id_question = intval(substr($code, 0, strpos($code, "_") ));   
            $stm = $this->dbpe->prepare("SELECT id_resource, code, id_user, code_class, times, score, id_score_letter, id_class, comment, date_format(date_scored, '%e/%c/%Y a las %l:%i %p') date_scored, date_format(inserted, '%e/%c/%Y a las %l:%i %p') date_resolved, status, id_calification_type  FROM $this->table_question_join WHERE id = ?"); 
            $stm->execute(array($id_question)); 
            $resultHead = $stm->fetch();  


            $resultDetail = array();
            $stm = $this->dbpe->prepare("SELECT qe.id, qe.id_resource, qe.id_user, qe.type, qe.code, qe.value, qe.score, qe.id_score_letter, qe.value_score, qe.comment FROM $this->table_question_join qj INNER JOIN $this->table_question_evaluate qe on qj.id = qe.id_question WHERE qj.id = ?"); 
            $stm->execute(array($id_question)); 
            $resultDetail = $stm->fetchAll();  

            foreach ($resultDetail as $row) {
               if( $row->type == 'dragg'){
                    $row->value = unserialize($row->value);
               }
            } 
            
            $this->response->result = array( "head" => $resultHead, "detail" => $resultDetail);
            $this->response->setResponse(true);
            return $this->response;  
        } catch (Exception $e) 
        {
            $this->response->setResponse(false);
            return $this->response;  
        } 
    }

    public function SaveQuestionOpen($data){
        //var_dump($data);
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
                $code = uniqid();
                $sql = "INSERT INTO $this->table_question
                            (activity_id, code, name, student_name, student_mail, teacher_mail, studystage_id, grade_id, id_book, id_unity, page, times,  inserted)
                            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)";
                
                $this->dbpe->prepare($sql)
                     ->execute(
                        array(
                            $data['activity']['id'], 
                            $code,
                            $data['activity']['name'],  
                            $data['student_name'],
                            $data['student_mail'],
                            $data['teacher_mail'],
                            $data['studystage_id'],
                            $data['book']['id_grade'], 
                            $data['book']['id'],
                            $data['unity']['id'],
                            $data['activity']['page'],
                            $data['activity']['times'],
                            date('Y-m-d G:H:i')
                        )
                );  

                $filename =  $data['activity']['id'] . "_" . $code . "_question.json";
                $path = $this->path_upload_activitys_question . $filename; 

                $saveJson = $this->saveJson( $data['activity']['id'], $path, $data['dataForm'], false);

                $dataEmail = $this->htmlForEmail($data);
                $resultMail = $this->sendMailActivity($data['activity']['name'], $data['student_name'], $data['student_mail'], $data['teacher_mail'] ,$dataEmail);  
                
                $this->response->result = array('id' =>  $this->dbpe->lastInsertId(),'code' => $code, 'saveJson' => $saveJson, 'mail' => $resultMail["success"], 'mailmsg' => $resultMail["msg"] );
                $this->response->setResponse(true);
                return $this->response;
            }catch (Exception $e) 
            {
                $this->response->setResponse(false, $e->getMessage());
            }
        } 
    }


    public function getJson($filePath)
    { 
        //var_dump( json_encode(file_get_contents($filePath)) );
        if(file_exists($filePath)){
            $json = json_decode( file_get_contents($filePath) );  
            $this->response->result = $json;
            $this->response->setResponse(true);
            return $this->response; 
        }
        else
        {
            $this->response->setResponse(false);
            return $this->response;
        }
    }

    public function saveJson($id_activity, $filePath, $data, $responseDirect = true)
    {   

        $path = pathinfo($filePath);    
        $data = json_encode($data);  
        if(substr_count($filePath,"_question") == 0){
            foreach ( glob($path['dirname'] . "/" . $id_activity ."_*") as $filename ) {
               unlink($filename);
            }
        } 
        if( file_put_contents($filePath, $data) != false ){  
            if($responseDirect)
            {
                $this->response->setResponse(true);
                return $this->response;  
            }
            else
            {
                return true; 
            }
            
        } 
        else
        {
            if($responseDirect)
            {
                $this->response->setResponse(false);
                return $this->response;  
            }
            else
            {
                return false;
            }
        }
    }
    
    public function Delete($id)
    {
		try 
		{
			$stm = $this->dbpe
			            ->prepare("DELETE FROM $this->table WHERE id = ?");			          

			$stm->execute(array($id));
            
			$this->response->setResponse(true);
            return $this->response;
		} catch (Exception $e) 
		{
			$this->response->setResponse(false, $e->getMessage());
		}
    }
 

    public function htmlForEmail($data)
    { 
        $cont = 0;
        $html = '<div class="background-color:#d4d4d4; padding: 10px; "><br>';
        $html.= '<h3><b>Libro:</b> '. utf8_decode($data['book']['name']).' , <b>Unidad:</b> ' . utf8_decode($data['unity']['name']) . '</h3>';
        $html.= '<h3><b>Actividad realizada:</b> '. utf8_decode($data['activity']['name']) .', '. ' (Pagina:' . $data['activity']['page'] . ')</h3>';
        $html.= '<h4><b>Alumno:</b>'. utf8_decode($data['student_name']) .' , ' . $data['student_mail'] . '</h4>';
        $html.= '<h5><b>Fecha / Hora: </b>'. date('Y-m-d G:H:i') . '</h5>';
        $html.= '<br><h5><b>Nro de intentos: </b>'. $data['activity']['times'] .'</h5>';
        //$html.= '<h4><i>'..'</i></h4>';
        $html.= '<br>';
        $html.= '</div>';
        $html.= '<div class="padding: 10px;">';
        foreach ($data["dataForm"] as $key => $linea) { 
            $cont++; 
            $html.= '<b>'.  $cont .')' . utf8_decode($linea["label"]) . '</b>';
            $html.= '<br>';

            if($linea["type"] == "image"){
                $html.= '<img src="'.$linea["value"].'" alt="Imagen" />';
            }else if($linea["type"] == "konva"){ 
                $dataImg = str_replace('data:image/png;base64,', '', $linea["img"] );
                $dataImg = str_replace(' ', '+', $dataImg);
                $dataImg = base64_decode($dataImg);
                $pathImgKonvas = $this->path_upload_activitys_question . $linea["name"] . '.png';  
                $success = file_put_contents($pathImgKonvas, $dataImg);
                $html.= '<img src="'.$linea["path"].'" alt="Imagen" />';
                $html.= '<br /><a href="'.$this->shortLink($linea["path"]).'" download >Descargar Grafico</a>';
            }else if($linea["type"] == "file" || $linea["type"] == "uploader"){
                $html.= $this->shortLink($linea["value"]);
            }else{
                if(isset($linea["value"]))
                    $html.= '' . $linea["value"] . '';
            } 
            
            $html.= '<br><br>';
        } 
        $html.="</div>"; 
        return $html;
    }

    public function shortLink($link)
    {
        $data = '{"dynamicLinkInfo":{"domainUriPrefix": "ebiolibros.page.link","link": $link},"suffix": {"suffix": "SHORT"}}'; 

        $myObj = array(); 
        $myObj["dynamicLinkInfo"] = array("dynamicLinkDomain" => "ebiolibros.page.link", "link" => $link);
        $myObj["suffix"] = array("option"=>"SHORT"); 
        $myJSON = json_encode($myObj);

        $url = "https://firebasedynamiclinks.googleapis.com/v1/shortLinks?key=AIzaSyDXAsZcKfFZoey01BkObo__Xg4ECDVVg7c";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);                               
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $myJSON);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $jsonResponse = curl_exec($ch);
        if(curl_errno($ch))
        {
            //echo 'Curl error: ' . curl_error($ch);
            curl_close($ch); 
            return $link;
        }

        $result = json_decode($jsonResponse);
        curl_close($ch);  
        return $result->shortLink; 
    }

    public function sendMailActivity($activity_name, $student_name, $student_mail, $teacher_mail, $html)
    { 
        $mail = new PHPMailer(true);  
        try {
            //$mail->SMTPDebug = 2;                                 // Enable verbose debug output
            $mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->Host = 'box5273.bluehost.com';  // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = 'infope@ebiolibros.com';                 // SMTP username
            $mail->Password = 'mY5c59lRz5';                           // SMTP password
            $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
            $mail->Port = 26;  
            $mail->Hostname = "ebiolibros.com";
            $mail->Helo = $mail->Hostname;   
            $mail->setFrom('infope@ebiolibros.com', 'Plataforma Educativa eBiolibros');
            $mail->addAddress($teacher_mail, 'Profesor');   
            $mail->addBCC($student_mail); 
            //Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = $student_name . ' ha realizado la actividad: ' .  $activity_name;
            $mail->Body    = $html;
            $mail->AltBody = '';

            if( $mail->send() )
                return array("success"=>true,"msg"=>"Success");
            else
                return array("success"=>false,"msg"=>"Not send mail");
        
        } catch (Exception $e) {
            return array("success"=>false,"msg"=>$mail->ErrorInfo); ;
        }
    }

    public function GetAllType()
    {
        try
        {
            $result = array();

            $stm = $this->dbpe->prepare("SELECT * FROM $this->table_type");
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

    public function GetType($id)
    {
        try
        {
            $result = array();

            $stm = $this->dbpe->prepare("SELECT * FROM $this->table_type WHERE id = ?");
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

    public function uploadImagesActivity($data)
    {    

        if($data == null)
        {
            $this->response->setResponse(false, "Error, no data");
            return $this->response;
        }
        else
        {   
            try 
            {
                if(isset($data['nameField']))
                {   
                    $myFile = $_FILES['file'];  
                    $nameField = $data['nameField'];
                    $filesUpload = array(); 
                    $fullpass = true;
                    $total_upload = 0;
                    $final_path = $this->path_upload_image_activity;

                    if (!file_exists($final_path)) {
                        mkdir($final_path, 0777, true);
                    } 

                    $tmp_name = $myFile['tmp_name']; 
                    $name_file = pathinfo($myFile['name']);
                    $ext = $name_file['extension'];
                    $codeu = uniqid();
                    $nameField = str_replace("-preview", "", $nameField);
                    $final_name = $nameField . '.' . $ext;
                    if( move_uploaded_file( $tmp_name , $final_path . $final_name ) != false ){  
                        array_push($filesUpload, $final_name); 
                        $total_upload++;
                    } else {
                        $fullpass = false; 
                    } 

                    $this->response->result = array('total_upload' => $total_upload, 'name' => $final_name );
                    $this->response->setResponse(true);
                    return $this->response;
                   
                }
                else{
                    $this->response->setResponse(false, 'Error data format ');
                } 
            }catch (Exception $e) 
            {
                $this->response->setResponse(false, $e->getMessage());
            }
        } 
    } 

    public function checkResourceHistory($id)
    {
        try
        {
            $result = array();

            $stm = $this->dbpe->prepare("SELECT qj.id, qj.code, u.id id_user, u.first_name, u.last_name FROM $this->table_resources r
                                        INNER JOIN $this->table_question_join qj on r.id = qj.id_resource
                                        INNER JOIN $this->table_user u on qj.id_user = u.id
                                        WHERE r.id = ?");
            $stm->execute(array($id));
            
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