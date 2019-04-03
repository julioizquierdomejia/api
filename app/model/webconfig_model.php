<?php
namespace App\Model;

use App\Lib\Database;
use App\Lib\Response;
require '../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception; 

class WebConfigModel
{
    private $db;
    private $table_nav = 'nav_menu_item';
    private $table_nav2 = 'nav2_menu_item';
    private $table_config = 'config_web';
    private $table_dd_nav = 'dropdown_nav_item';
    private $table_dd_nav2 = 'dropdown_nav2_item';
    private $table_slider = 'slider';
    private $table_circle_pe = 'circle_pe_items';
    private $table_circle_pe_especial = 'circle_pe_items_extra';
    private $table_staff_capaciters = 'staff_capaciters';
    private $response;
    
    public function __CONSTRUCT()
    {
        $this->db = Database::StartUp();
        $this->response = new Response();
    }
    
    public function GetAllNav()
    {
		try
		{
			$result = array();

			$stm = $this->db->prepare("SELECT * FROM $this->table_nav where module = 'web' order by order_number");
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

    public function GetAllNav2()
    {
		try
		{
			$result = array();

			$stm = $this->db->prepare("SELECT * FROM $this->table_nav2  where module = 'web' order by order_number");
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

    public function GetAllConfig()
    {
		try
		{
			$result = array();

			$stm = $this->db->prepare("SELECT * FROM $this->table_config");
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

    public function GetSlider($area)
    {
		try
		{
			$result = array();

			$stm = $this->db->prepare("SELECT * FROM $this->table_slider WHERE area = ?");
			$stm->execute(array($area));

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

    public function GetCirclePE()
    {
		try
		{
			$result = array();

			$stm = $this->db->prepare("SELECT * FROM $this->table_circle_pe");
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

    public function GetCirclePEByCode($code)
    {
    	try
		{
			$result = array();

			$stm = $this->db->prepare("SELECT * FROM $this->table_circle_pe WHERE code = ?");
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

    public function GetCirclePEEspecial($id)
    {
    	try
		{
			$result = array();

			$stm = $this->db->prepare("SELECT * FROM $this->table_circle_pe_especial WHERE id_item_circle = ? order by number_order");
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

    public function GetStaffCapaciters()
    {
		try
		{
			$result = array();

			$stm = $this->db->prepare("SELECT * FROM $this->table_staff_capaciters order by number_order");
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
    
    
}