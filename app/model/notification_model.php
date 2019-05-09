<?php
namespace App\Model;

use App\Lib\Database;
use App\Lib\Response; 

class NotificationModel extends GeneralConfig
{
    private $table;
    private $table_extra;
    private $table_type;
    private $dbpe;
    private $dbmaster;
    private $dbpeTemp;
    
    public function __CONSTRUCT($token_data = array())
    { 
        $this->dbpe = Database::StartUpArea( isset($token_data->amb) ? $token_data->amb : $this->bd_base_pe );
        $this->dbmaster = Database::StartUpMaster();
        $this->response = new Response();
        $this->token_data = $token_data;

        $this->table = $this->table_notification;
        $this->table_extra = $this->table_notification_extra;
        $this->table_type = $this->table_notification_type;
    }

 
    public function GetAll()
    {
        try
        {
            $result = array();
            $id_user = $this->token_data->id;  
            $idm = $this->token_data->idm;  
            $amb = $this->token_data->amb;

            $totalNotif = array();

            if($amb == $this->bd_base_pe)
            {
                $totalAmbs = $this->getAllAmbsUser($this->dbmaster, $idm);
                
                foreach ($totalAmbs as $key => $value) {
                    $this->dbpeTemp = Database::StartUpArea($value->amb);
                    $stm = $this->dbpeTemp->prepare("SELECT n.id, n.id_type, n.title, n.id_user, n.id_user_trigger, n.link, nt.name name_type, nt.link_text, '".$value->amb."' amb, date_format(n.inserted, '%e/%c/%Y a las %l:%i %p') inserted, n.inserted date_inserted,  date_format(n.inserted, '%e/%c/%Y') inserted_short, n.inserted date_inserted, DATEDIFF(now(),n.inserted) daysago  FROM $this->table n INNER JOIN $this->table_type nt on n.id_type = nt.id INNER JOIN $this->table_user u on n.id_user = u.id WHERE u.id_user_master = ? order by n.inserted desc LIMIT 0,10");

                    $stm->execute(array($idm));
                    $result = $stm->fetchAll();
                    foreach ($result as $keyR => $valueR) {
                        array_push($totalNotif, $valueR);
                    }
                }

                $this->response->setResponse(true);
                $this->response->result = $totalNotif;
            }
            else
            {
                $stm = $this->dbpe->prepare("SELECT n.id, n.id_type, n.title, n.id_user, n.id_user_trigger, n.link, nt.name name_type, nt.link_text, '".$amb."' amb FROM $this->table n INNER JOIN $this->table_type nt on n.id_type = nt.id WHERE n.id_user = ? order by n.inserted desc LIMIT 0,10");
                $stm->execute(array($id_user));
                
                $this->response->setResponse(true);
                $this->response->result = $stm->fetchAll();
            } 
            
            return $this->response;
        }
        catch(Exception $e)
        {
            $this->response->setResponse(false, $e->getMessage());
            return $this->response;
        }
    } 

    public function GetFull()
    {
        try
        {
            $result = array();
            $id_user = $this->token_data->id;  
            $idm = $this->token_data->idm;  
            $amb = $this->token_data->amb;

            $totalNotif = array();

            if($amb == $this->bd_base_pe)
            {
                $totalAmbs = $this->getAllAmbsUser($this->dbmaster, $idm);
                
                foreach ($totalAmbs as $key => $value) {
                    $this->dbpeTemp = Database::StartUpArea($value->amb);
                    $stm = $this->dbpeTemp->prepare("SELECT n.id, n.id_type, n.title, n.id_user, n.id_user_trigger, n.link, nt.name name_type, nt.link_text, '".$value->amb."' amb, date_format(n.inserted, '%e/%c/%Y a las %l:%i %p') inserted, n.inserted date_inserted,  date_format(n.inserted, '%e/%c/%Y') inserted_short, n.inserted date_inserted, DATEDIFF(now(),n.inserted) daysago  FROM $this->table n INNER JOIN $this->table_type nt on n.id_type = nt.id INNER JOIN $this->table_user u on n.id_user = u.id WHERE u.id_user_master = ? order by n.inserted desc");

                    $stm->execute(array($idm));
                    $result = $stm->fetchAll();
                    foreach ($result as $keyR => $valueR) {
                        array_push($totalNotif, $valueR);
                    }
                }

                $this->response->setResponse(true);
                $this->response->result = $totalNotif;
            }
            else
            {
                $stm = $this->dbpe->prepare("SELECT n.id, n.id_type, n.title, n.id_user, n.id_user_trigger, n.link, nt.name name_type, nt.link_text, '".$amb."' amb FROM $this->table n INNER JOIN $this->table_type nt on n.id_type = nt.id WHERE n.id_user = ? order by n.inserted desc");
                $stm->execute(array($id_user));
                
                $this->response->setResponse(true);
                $this->response->result = $stm->fetchAll();
            } 
            
            return $this->response;
        }
        catch(Exception $e)
        {
            $this->response->setResponse(false, $e->getMessage());
            return $this->response;
        }
    } 

    public function GetAllByUser($id_user)
    {
        try
        {
            $result = array();

            $stm = $this->dbpe->prepare("SELECT * FROM $this->table where id_user = ?");
            $stm->execute($id_user);
            
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
    
    
    public function Get($id_notification, $amb = '') 
    {
        $amb = ($amb == '') ? $this->bd_base_pe : $amb;
        try
        { 
            $result = array();
            $id_user = $this->token_data->id;
            $this->dbpeTemp = Database::StartUpArea($amb);
            $stm = $this->dbpeTemp->prepare("SELECT n.id, n.id_type, n.title, n.id_user, n.id_user_trigger, n.link, nt.name name_type, nt.link_text FROM $this->table n INNER JOIN $this->table_type nt on n.id_type = nt.id WHERE n.id = ?");
            $stm->execute(array($id_notification));

            $result = $stm->fetch();
            $dataExtra = $this->getExtraData($id_notification, $amb);

            $this->response->setResponse(true);
            $this->response->result = array("notification" => $result, "extra" => $dataExtra);
            
            return $this->response;
        }
        catch(Exception $e)
        {
            $this->response->setResponse(false, $e->getMessage());
            return $this->response;
        }  
    }

    public function privateGetType($id_type)
    {
        try
        {  
            $stm = $this->dbpe->prepare("SELECT * FROM $this->table_type WHERE id = ?");
            $stm->execute(array($id_type));
            return $stm->fetch();
        }
        catch(Exception $e)
        { 
            return false;
        }  
    }

    public function privateRegister($id_type, $id_user, $amb, $data = array(), $id_user_trigger = 0, $linkData = array()) 
    {   
        try
        {  
            $dataProcessed = $this->generateDataProcessed($id_type, $data, $linkData, $id_user, $amb);
 

            $sql = "INSERT INTO $this->table
                                (id_type, title, id_user, id_user_trigger, link, inserted)
                                VALUES (?,?,?,?,?,?)";
                    
            $this->dbpe->prepare($sql)
                        ->execute(
                            array(
                                $id_type, 
                                $dataProcessed['title'],
                                $id_user,
                                $id_user_trigger,
                                $dataProcessed['link'],
                                date('Y-m-d G:H:i')
                            )
                        );

            $idresponse = $this->dbpe->lastInsertId();   

            if($idresponse > 0){
                $totalDE = count($data);
                $contSucces = 0;
                for($de = 0; $de < $totalDE; $de++)
                {
                    if( $this->registerExtra($idresponse, $data[$de]["name"], $data[$de]["id"]) )
                        $contSucces++;
                }
                if($contSucces == $totalDE)
                    return true;
            }

            return false;
        }
        catch(Exception $e)
        {
            return false;
        } 
    }

    private function generateDataProcessed($id_type, $data, $linkData, $id_user, $amb){        
        $typeData = $this->privateGetType($id_type); 
        $linkProcessed = (count($linkData) > 0) ? $this->generateLink($linkData, $typeData->link) : '';

        $dataReady = array(); 
        $title = ''; 
        $idrel = '';
        $fieldrel = '';

        if($typeData != false)
        {

            for($i = 0; $i < count($data); $i++)
            { 
                $dataField = $this->getRow($data[$i]["name"], $data[$i]["id"], $amb);
                $dataField =  (array) $dataField;  
                $dataReady[ $data[$i]["name"] ] = $dataField; 
            } 

            //var_dump($dataReady);
            $title = $typeData->title; 
            preg_match_all('/<(.*?)>/', $title, $matches);
            $patterns = $matches[1];
            //var_dump($patterns);

            for($i = 0; $i < count($patterns); $i++)
            { 
                $arraySeparate = explode('.', $patterns[$i]);
                $entity = $arraySeparate[0];
                $field = $arraySeparate[1];
                $entityData = $dataReady[$entity];
                //var_dump($entityData);
                $title = preg_replace('/<'. $patterns[$i] .'>/', '<b>' . $entityData[$field] . '</b>', $title);
            }
            return array( "title" => $title, "data" => $dataReady, "link" => $linkProcessed );
        }
        else
        {
            return array( "title" => $typeData->title, "link" => $linkProcessed );
        }
    }

    private function generateLink($linkData, $link)
    {
        $linkFinal = $link;
        foreach ($linkData as $key => $value) { 
            $linkFinal = str_replace( '<' . $key . '>' , $value, $link);
            $link = $linkFinal;
        } 
        return $linkFinal;
    }  

    private function getRow($table, $id, $amb, $filter = false)
    {  
        $table_query = $table;
        $db = NULL; 
        $fields = (!$filter) ? "*" : $this->gc_configEntityPublicData[$table]["fields"];

        switch ($table) {
            case 'teacher':
            case 'alumn':
                $table_query = $this->table_user; 
                $db = $this->dbpeTemp = Database::StartUpArea($amb);
                break;
            case 'class':
                $table_query = $this->table_class; 
                $db = $this->dbpeTemp = Database::StartUpArea($amb);
                break; 
            case 'resource':
                $table_query = $this->table_resource; 
                $db = $this->dbpeTemp = Database::StartUpArea($amb);
                break;
            case 'book':
                $table_query = $this->table_book; 
                $db = Database::StartUp();
                break; 
        } 
        $stm = $db->prepare("SELECT ".$fields." FROM $table_query where id = ?"); 
        $stm->execute(array($id));
        return $stm->fetch();
    } 

    public function registerExtra($id_notification, $entity_name, $id_entity){ 
        try 
        {
            $sql = "INSERT INTO $this->table_extra
                                (id_notification, entity_name, id_entity, inserted)
                                VALUES (?,?,?,?)";
                    
                    $this->dbpe->prepare($sql)
                         ->execute(
                            array(
                                $id_notification,  
                                $entity_name,
                                $id_entity, 
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

    private function getExtraData($id_notification, $amb)
    {
        try 
        {  
            $result = array(); 
            $id_user = $this->token_data->id;
            $this->dbpeTemp = Database::StartUpArea($amb);
            $stm = $this->dbpeTemp->prepare("SELECT * FROM $this->table_extra te WHERE id_notification = ?");
            $stm->execute(array($id_notification)); 
            $result = $stm->fetchAll();  
            $result = (array) $result;
            $dataFinal = array();

            for ($i = 0; $i < count($result); $i++) { 
                $lineExtra = $result[$i]; 
                $dataLineExtra = $this->getRow($lineExtra->entity_name, $lineExtra->id_entity, $amb, true);
                array_push($dataFinal, $dataLineExtra);
            } 

            return $dataFinal;
        }
        catch (Exception $e) 
        {
            return false;
        }  
    }


}