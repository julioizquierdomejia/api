<?php
namespace App\Model;

use App\Lib\Database;
use App\Lib\Response;

require '../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception; 


class ActivityModel
{
    private $db;
    private $table = 'activity';
    private $table_question = 'activity_question';
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
                                time_band   = ?,
                                time        = ?,
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
                                $data['time_band'],
                                $data['time'],
                                $data['side'],  
                                date('Y-m-d G:H:i'),
                                $data['id']
                            )
                        );
                }
                else
                {
                    $sql = "INSERT INTO $this->table
                                (code, name, description, id_book, id_unity, page, time_band, time, side, inserted)
                                VALUES (?,?,?,?,?,?,?,?,?,?)";
                    
                    $this->db->prepare($sql)
                         ->execute(
                            array(
                                $code,
                                $data['name'], 
                                $data['description'],
                                $data['id_book'],
                                $data['id_unity'],
                                $data['page'],
                                $data['time_band'],
                                $data['time'],
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

    public function SaveQuestion($data){
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
                            (activity_id, code, name, student_name, student_mail, teacher_mail, studystage_id, grade_id, id_book, id_unity, page, times, inserted)
                            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)";
                
                $this->db->prepare($sql)
                     ->execute(
                        array(
                            $data['activity']['id'], 
                            $code,
                            $data['activity']['name'],  
                            $data['student_name'],
                            $data['student_mail'],
                            $data['teacher_mail'],
                            $data['studystage_id'],
                            $data['grade_id'], 
                            $data['book']['id'],
                            $data['unity']['id'],
                            $data['activity']['page'],
                            $data['activity']['times'] ,
                            date('Y-m-d G:H:i')
                        )
                );  

                $filename =  $data['activity']['id'] . "_" . $code . "_question.json";
                $path = "../../lib/media/content/activitys_questions/" . $filename; 

                $saveJson = $this->saveJson($path, json_encode( $data['dataForm']), false);

                $dataEmail = $this->htmlForEmail($data);
                //$resultMail = $this->sendMailActivity($data['activity']['name'], $data['student_name'], $data['student_mail'], $data['teacher_mail'] ,$dataEmail);  
                //'mail' => $resultMail["success"], 'mailmsg' => $resultMail["msg"], 
                $this->response->result = array('id' =>  $this->db->lastInsertId(),'code' => $code, 'saveJson' => $saveJson);
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

    public function saveJson($filePath,$data, $responseDirect = true)
    {
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
            if(isset($linea["value"]))
                $html.= '' . $linea["value"] . '';
            $html.= '<br><br>';
        } 
        $html.="</div>";
        return $html; 
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
            $mail->Password = 'ebiolibros2018';                           // SMTP password
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
}