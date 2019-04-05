<?php
namespace App\Model;

use App\Lib\Database;
use App\Lib\Response;
require '../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception; 

class PeConfigModel extends GeneralConfig
{
    private $db;  
    private $dbpeTemp;
    private $dbmaster;
    
    public function __CONSTRUCT($token_data = array())
    {
        $this->db = Database::StartUp();
        $this->dbpe = Database::StartUpArea( $this->bd_base_pe );
        $this->dbmaster = Database::StartUpMaster();
        $this->response = new Response();
        $this->token_data = $token_data; 
        $this->table = $this->table_book;
    }
     
    public function GetAllPeNav()
    {
		try
		{
			$result = array();

			$stm = $this->db->prepare("SELECT * FROM $this->table_nav where module = 'pe' order by order_number");
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
 

    public function GetAllPeNav2()
    {
		try
		{
			$result = array();

			$stm = $this->db->prepare("SELECT * FROM $this->table_nav2  where module = 'pe' order by order_number");
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

    public function GetPrivateNav()
    {
		try
		{
			$result = array();

			$stm = $this->dbpe->prepare("SELECT * FROM $this->table_nav_private order by order_number");
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

    public function GetAllConfig()
    {
		try
		{
			$result = array();

			$stm = $this->dbpe->prepare("SELECT * FROM $this->table_config");
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

    public function GetAllBookGroup()
    {
        try
        {
            $result = array();

            $stm = $this->dbmaster->prepare("SELECT * FROM $this->table_book_group order by order_number");
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

    public function GetAllUserType()
    {
        try
        {
            $result = array();

            $stm = $this->dbmaster->prepare("SELECT * FROM $this->table_user_master_type where public = 1");
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

    public function GetResourcesByBook($id_book, $class_code)
    {
        try
        {
            $amb = $this->token_data->amb; 
            $this->dbpeTemp = Database::StartUpArea($amb);
            $result = array();  

            $stm = $this->dbpeTemp->prepare("SELECT * FROM $this->resources_general_table WHERE id_book = ? and class_code = ?");
            $stm->execute(array($id_book, $class_code));

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


    public function createResource($data)
    {    
        if($data == null)
        {
            $this->response->setResponse(false, "Error, no data");
            return $this->response;
        }
        else
        {  
            $myFiles = $_FILES['file']; 
            try 
            {
                if(isset($data['bookcode']) && isset($data['titles']))
                {   

                	list($bookcode,$tr0, $bookid, $tr1) = preg_split("/[-._]/", $data['bookcode']); 
                	$titles = $data['titles'];
                    $class_code = $data['class_code'];
                	$resultGlobal = array();
                	$fullpass = true;
                	$total_upload = 0; 
                    $final_path = $this->path_upload . $bookcode . '/' . $class_code . '/';

                    if (!file_exists($final_path)) {
                        mkdir($final_path, 0777, true);
                    }

                	for($i = 0; $i < count($myFiles['tmp_name']); $i++ ){
                		$tmp_name = $myFiles['tmp_name'][$i];
                		$title = $titles[$i];
                		$name_file = pathinfo($myFiles['name'][$i]);
                		$ext = $name_file['extension'];
                		$code = uniqid();
                		$final_name = $i . $class_code . '_' . $code . '.' . $ext;

                        if (!is_dir($final_path)) { 
                            mkdir($final_path); 
                        }

                		if( move_uploaded_file( $tmp_name , $final_path . $final_name ) != false ){ 
                			$result = $this->createResourceDB( $bookid, $title, $final_name, $class_code );
                			array_push($resultGlobal, $result); 
                			$total_upload++;
                		} else {
                			$fullpass = false;
                			array_push($resultGlobal, array( "success" => false, "filename" => $final_name )); 
                		}
                	}

                	$this->response->result = array("total_upload"=> $total_upload, "detail" => $resultGlobal);
                	$this->response->setResponse($fullpass); 
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

    public function createResourceDB($bookid, $name, $filename, $class_code){ 
    	try 
		{
    		$name_file = pathinfo($filename);
            $ext = $name_file['extension'];
            $media_type = 'other';
    		$type = 'file'; 
    		$link = '';
    		$imageList = array('jpg','jpeg', 'png', 'gif');
    		$videoList = array('mp4','mkv','avi','3gp','wmv');
    		$audioList = array('mp3','wav','ogg','aac');
 
			if (in_array($ext, $imageList)) {
			    $media_type = 'image';
			}
			if (in_array($ext, $videoList)) {
				$media_type = 'video';
			}

			if (in_array($ext, $audioList)) {
				$media_type = 'audio';
			}  

            $amb = $this->token_data->amb; 
            $this->dbpeTemp = Database::StartUpArea($amb);
            
            $sql = "INSERT INTO $this->resources_general_table
                        (id_book, class_code, name, type, filename, media_type, link, inserted)
                        VALUES (?,?,?,?,?,?,?)";
            
            $result = $this->dbpe->prepare($sql)
                 ->execute(
                    array(
                        $bookid, 
                        $class_code,
                        $name,
                        $type,
                        $filename,
                        $media_type,
                        $link,
                        date('Y-m-d G:H:i')
                    )
                );   
 
			 
            return  array("success" => $result, "filename" => $filename); 
        }catch (Exception $e) 
		{
            return  array("success" => false, "filename" => $filename); 
		}
    }

    public function createResourceLink($data){ 
    	if($data == null)
        {
            $this->response->setResponse(false, "Error, no data");
            return $this->response;
        }
        else
        {
	    	try 
			{
	    		 
	            $media_type = 'other';
	    		$type = 'link'; 
	    		$link = $data['link'];
	    		$filename = '';
	    		$name = $data['title'];
	    		$bookid = $data['bookid'];
                $class_code = $data['class_code'];
	    	 
	            
	            $sql = "INSERT INTO $this->resources_general_table
	                        (id_book, name, type, filename, media_type, link, inserted)
	                        VALUES (?,?,?,?,?,?,?)";
	            
	            $result = $this->dbpe->prepare($sql)
	                 ->execute(
	                    array(
	                        $bookid, 
                            $class_code,
	                        $name,
	                        $type,
	                        $filename,
	                        $media_type,
	                        $link,
	                        date('Y-m-d G:H:i')
	                    )
	                );   
	 
				//$this->response->result = array('id' =>  $this->db->lastInsertId(),'code' => $code );;
	            $this->response->setResponse(true);
	            return $this->response;
	        }catch (Exception $e) 
			{
	            $this->response->setResponse(false, $e->getMessage());
			}
		}
    }

    public function GetImgsHead(){
        $scanned_directory = array_diff(scandir($this->path_imgs_head,0), array('..', '.', 'thumbs.db', 'desktop.ini'));
        $this->response->result = $scanned_directory;
        $this->response->setResponse(true);
        return $this->response;
    }

    public function GenerateTree($id_book){
        try {
            $resultBook = array(); 
            $stm = $this->db->prepare("SELECT * FROM $this->table_book WHERE id = ?");
            $stm->execute(array($id_book));
            $resultBook = $stm->fetch();
            $id_serie = $resultBook->id_serie;
            $code_book = $resultBook->code;

            $resultSerie = array();  
            $stm = $this->db->prepare("SELECT * FROM $this->table_serie WHERE id = ?");
            $stm->execute(array($id_serie));
            $resultSerie = $stm->fetch();  
            $id_studystage = $resultSerie->id_stage;
            $code_serie = $resultSerie->code;

            $resultStudyStage = array();  
            $stm = $this->db->prepare("SELECT * FROM $this->table_studystage WHERE id = ?");
            $stm->execute(array($id_studystage));
            $resultStudyStage = $stm->fetch();  
            $code_studyStage = $resultStudyStage->code;
     
            $link = $code_studyStage . '/' . $code_serie . '/' . $code_book;

            $this->response->result = array('link' =>  $link, 'serie' => $code_serie, 'book' => $code_book, 'studystage' => $code_studyStage );
            $this->response->setResponse(true);
            return $this->response;
        } 
        catch (Exception $e) {
            $this->response->setResponse(false);
            return $this->response;
        } 
    }

    public function sendContact($data)
    {
        //var_dump($data);
        if($data == null)
        {
            $this->response->setResponse(false, "Error, no data");
            return $this->response;
        }
        else
        { 
        	$mail = new PHPMailer(true);  
            try {
            	$data = $data["data"];

	            //$mail->SMTPDebug = 2;                                 // Enable verbose debug output
	            $mail->isSMTP();                                      // Set mailer to use SMTP
	            $mail->Host = 'box5273.bluehost.com';  // Specify main and backup SMTP servers
	            $mail->SMTPAuth = true;                               // Enable SMTP authentication
	            $mail->Username = 'contactoweb@ebiolibros.com';                 // SMTP username
	            $mail->Password = '1RRiKjBmxU';                           // SMTP password
	            $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
	            $mail->Port = 26;  
	            $mail->Hostname = "ebiolibros.com";
	            $mail->Helo = $mail->Hostname;   
	            $mail->setFrom('contactoweb@ebiolibros.com', 'Web ebiolibros');
	            $mail->addAddress('contacto@ebiolibros.com', 'Contacto eBiolibros');    
	            //Content  
	            $mail->isHTML(true);                                  // Set email format to HTML
	            $mail->Subject = 'Contacto Web: ' . utf8_decode($data["subjet"]);
	            $mail->Body    = '<br> <b>Nombre</b>:' . utf8_decode($data["name"]) . '<br><b>Correo:</b>' . utf8_decode($data["mailcontact"]) . '<br><br>' . utf8_decode($data["body"]);
	            $mail->AltBody = '';

	            if( $mail->send() )
	                return array("success"=>true,"msg"=>"Success");
	            else
	                return array("success"=>false,"msg"=>"Not send mail");
	        
	        } catch (Exception $e) {
	            return array("success"=>false,"msg"=>$mail->ErrorInfo); ;
		    }
		}
    }

    public function CreateBOOKIDCARD($data){
        if($data == null)
        {
            $this->response->setResponse(false, "Error, no data");
            return $this->response;
        }
        else
        {
            try { 

                $bookgroup_id = $data['bookgroup_id'];
                $user_type_id = $data['user_type_id']; 
                $quantity = $data['quantity'];

                $resultBook = array(); 
                $stm = $this->dbmaster->prepare("SELECT * FROM $this->table_book_group WHERE id = ?");
                $stm->execute(array($bookgroup_id));
                $resultBook = $stm->fetch();
                $id_groups = $resultBook->id_book_links;
                $id_groups = explode( ',', $id_groups);

                $resultUserType = array();  
                $stm = $this->dbmaster->prepare("SELECT * FROM $this->table_user_master_type WHERE id = ?");
                $stm->execute(array($user_type_id));
                $resultUserType = $stm->fetch();  
                $user_type_name = $resultUserType->name;
                $user_type_prefix = $resultUserType->prefix;
 
                $codes = array();
                for( $i=0; $i < $quantity; $i++ ){
                    $prefix = $resultBook->prefix;
                    $postfix = $resultBook->postfix;
                    $id_book = $resultBook->id;
                    $id_book_end = $id_groups[0];
                    array_push( $codes, array( "code" => $this->GenerateBOOKIDCARD($user_type_prefix, $id_book_end, $prefix, $postfix, $id_book, $user_type_id), "user_type_name" => $user_type_name, "grade" => $postfix ) );
                } 

                
                $this->response->result = $codes;
                $this->response->setResponse(true);
                return $this->response;
            } 
            catch (Exception $e) {
                $this->response->setResponse(false);
                return $this->response;
            } 
        } 
    }

    private function GenerateBOOKIDCARD($user_type_prefix, $id_book_group, $prefix,$postfix, $id_book, $user_type_id)
    {
        $code = '';
        $id_user = $this->token_data->idm;
        $pattern1 = 'ABCDEFGHJKLMNPQRTVWZ123456789'; 
        $max1 = strlen($pattern1)-1; 

        for($i=0;$i < $this->lengh_code_BIC;$i++) 
            $code .= $pattern1{mt_rand(0,$max1)};

        $code = $user_type_prefix . $prefix . $code . $postfix;
  
        $resultClass = (object)[];
        $stm = $this->dbmaster->prepare("SELECT code FROM $this->table_book_code WHERE code = ? and id_book = ?"); 
        $stm->execute(array($code, $id_book_group)); 
        $resultExist = $stm->fetchAll();
        if(count($resultExist) > 0)
        {
            return false;
        }else{ 
            $sql = "INSERT INTO $this->table_book_code
                        (code, id_book, id_book_group, id_status, id_user, id_type, year, inserted)
                        VALUES (?,?,?,?,?,?,?)";
            
            $result = $this->dbmaster->prepare($sql)
                 ->execute(
                    array(
                        $code, 
                        $id_book,
                        $id_book_group,
                        1,
                        $id_user,
                        $user_type_id,
                        2019,
                        date('Y-m-d G:H:i')
                    )
                );  

            return $code; 
        }

        
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
    
    
}