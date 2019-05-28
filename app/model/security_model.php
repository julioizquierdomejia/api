<?php 
namespace App\Model;

use App\Lib\Database;
use App\Lib\Response;
use \Firebase\JWT\JWT;

class SecurityModel extends GeneralConfig
{ 
    private $table;
    private $table_master; 
    private $logger;


    private $dbmaster;
    private $dbpe;
    private $dbpeTemp;
    
    public function __CONSTRUCT()
    { 
        $this->dbpe = Database::StartUpArea($this->bdbase);
        $this->dbmaster = Database::StartUpMaster();
        $this->response = new Response();
        //$this->logger = $logger;

        $this->table_master = $this->table_user_master;
        $this->table = $this->table_user; 
    }  
 
    public function login($authPair, $options, $uri)
    { 
        

        try
        { 
            $authPair = base64_decode($authPair);
            $user_email = explode(":", $authPair)[0];
            $user_password = explode(":", $authPair)[1];

            if( $user_email !== NULL && $user_email !== '' && $user_password !== NULL && $user_password !== '' )
            { 

                $resulUserMaster = array(); 
                $stm = $this->dbmaster->prepare("SELECT * FROM $this->table_master WHERE email = ?");
                $stm->execute(array(trim($user_email)));
                $resulUserMaster = $stm->fetchAll();
                if( count($resulUserMaster) > 0){
                    $bd = $this->bd_base_pe;
                    $id_user_link = $resulUserMaster[0]->id_user_link;
                    $id_user_master = $resulUserMaster[0]->id;

                    $stm = $this->dbpe->prepare("SELECT email, password, first_name, last_name, id 'level',id_type, id_studystage, id_grade, '".$id_user_master."' idm, '".$bd."' amb, '' name_scholl FROM $this->table WHERE id = ?");
                    $stm->execute(array($id_user_link)); 
                    $user = $stm->fetchObject();

                    $sesid = gethostname();

                    if(isset($_SERVER['HTTP_COOKIE'])){
                        $sesidA = explode(';', $_SERVER['HTTP_COOKIE']);
                        $sesid = $sesidA[0] . gethostname();
                    } 
                     
                    // verify email address.  
                    if(!$user || $user === NULL) { 
                        $this->response->setResponse(false, 'These credentials do not match our records.');
                        return $this->response;
                    }  

                    // verify password. 
                    if (!password_verify($user_password , $user->password)) { 
                        $this->response->setResponse(false, 'These credentials do not match our records.');
                        return $this->response; 
                    }

                    //verify expired user
                    if($this->checkUserExpired($id_user_master))
                    {
                         $this->response->setResponse(false, 'User Date Expired.');
                        return $this->response; 
                    }

                    //var_dump(time()); 
                    $user->password = '*****';
                    $days = 7;

                    $now = time();
                    $future = time() + (86400 * $days); 
                    //$jti = (new Base64)->encode(random_bytes(16));
                    $payload = [
                        "iat" => $now,
                        "exp" => $future, 
                        "sub" => ['id' => $user->level, 'idm' => $id_user_master, 'email' => $user->email, 'sesid' => $sesid, 'amb' => $bd, 'type' => $user->id_type]
                    ];
        
                    $token = JWT::encode($payload,  $options["secret"], "HS256");    

                    //$this->logger->info('User Login', array("user" => $user->level, "sesid" => $sesid) );
                    $this->response->setResponse(true);
                    $this->response->result = ['token' => $token, 'user' => $user ];
                    return $this->response;  
                }
                else
                {
                    $this->response->setResponse(false, 'These credentials do not match our records.');
                    return $this->response; 
                }
            }
            else
            {
                $this->response->setResponse(false, 'Invalid Data');
                return $this->response;
            }
        }
        catch(Exception $e)
        {
            $this->response->setResponse(false, $e->getMessage());
            return $this->response;
        }  

    }   

    public function changeAmb($data, $options, $uri, $token_data)
    {
        try
        { 
            $resultJoinScholl = array(); 
            $idm = $token_data["sub"]->idm;
            $amb = $data["amb"]; 
     

            $stm = $this->dbmaster->prepare("SELECT um.id_user_link, s.name name_scholl, s.image, s.image_full, s.web, s.color_primary, s.color_secondary FROM $this->table_user_master_scholl ums INNER JOIN $this->table_user_master um on ums.id_user_master = um.id INNER JOIN $this->table_scholls s on ums.id_scholl = s.id WHERE ums.id_user_master = ? and s.amb = ?"); 
            $stm->execute(array($idm, $amb)); 
            $resultJoinScholl = $stm->fetchAll();  
            $id_user_link = ''; 
            if(count($resultJoinScholl) > 0)
            { 
                $id_user_link = $resultJoinScholl[0]->id_user_link;
                $this->dbpeTemp = Database::StartUpArea($amb);
                $stm = $this->dbpeTemp->prepare("
                    SELECT email, first_name, last_name, id 'level',id_type, 
                    '".$idm."' idm, '".$amb."' amb, 
                    '".$resultJoinScholl[0]->name_scholl."' name_scholl,
                     '".$resultJoinScholl[0]->image."' image, 
                     '".$resultJoinScholl[0]->image_full."' image_full, 
                     '".$resultJoinScholl[0]->web."' web,
                     '".$resultJoinScholl[0]->color_primary."' color_primary,
                     '".$resultJoinScholl[0]->color_secondary."' color_secondary 
                     FROM $this->table WHERE id_user_master = ?");
                $stm->execute(array($idm)); 
                $user = $stm->fetchObject();

                $sesid = gethostname();

                if(isset($_SERVER['HTTP_COOKIE'])){
                    $sesidA = explode(';', $_SERVER['HTTP_COOKIE']);
                    $sesid = $sesidA[0] . gethostname();
                } 
                 
                // verify email address.  
                if(!$user || $user === NULL) { 
                    $this->response->setResponse(false, 'These credentials do not match our records.');
                    return $this->response;
                }  
         
                //verify expired user
                if($this->checkUserExpired($idm))
                {
                    $this->response->setResponse(false, 'User Date Expired.');
                    return $this->response; 
                }

                 
                $payload = [
                    "iat" => $token_data["iat"],
                    "exp" => $token_data["exp"], 
                    "sub" => ['id' => $user->level, 'idm' => $idm, 'email' => $user->email, 'sesid' => $sesid, 'amb' => $amb, 'type' => $user->id_type]
                ];

                $token = JWT::encode($payload,  $options["secret"], "HS256");    

                //$this->logger->info('User Login', array("user" => $user->level, "sesid" => $sesid) );
                $this->response->setResponse(true);
                $this->response->result = ['token' => $token, 'user' => $user ];
                
            }
            else
            {
                $this->response->setResponse(false);
            }
            return $this->response;  
        }
        catch(Exception $e)
        { 
            $this->response->setResponse(false);
            return $this->response;  
        }
    } 

    public function baseAmb($options, $uri, $token_data)
    {
        try
        { 
            $resultJoinScholl = array();  
            $idm = $token_data["sub"]->idm;
            $amb = $this->bd_base_pe;

            $stm = $this->dbmaster->prepare("SELECT * FROM $this->table_master WHERE id = ?");
            $stm->execute(array($idm));
            $resulUserMaster = $stm->fetchAll();
             if( count($resulUserMaster) > 0){
                $bd = $this->bd_base_pe;
                $id_user_link = $resulUserMaster[0]->id_user_link;
                $id_user_master = $resulUserMaster[0]->id;

                $stm = $this->dbpe->prepare("SELECT email, password, first_name, last_name, id 'level',id_type, id_studystage, id_grade, '".$id_user_master."' idm, '".$bd."' amb, '' name_scholl, '' image, '' image_full, 'ebiolibros.com' web, '#2581c5' color_primary, '#fff' color_secondary FROM $this->table WHERE id = ?");
                $stm->execute(array($id_user_link)); 
                $user = $stm->fetchObject();

                $sesid = gethostname();

                if(isset($_SERVER['HTTP_COOKIE'])){
                    $sesidA = explode(';', $_SERVER['HTTP_COOKIE']);
                    $sesid = $sesidA[0] . gethostname();
                } 
                 
                // verify email address.  
                if(!$user || $user === NULL) { 
                    $this->response->setResponse(false, 'These credentials do not match our records.');
                    return $this->response;
                }  
        
                //verify expired user
                if($this->checkUserExpired($idm))
                {
                    $this->response->setResponse(false, 'User Date Expired.');
                    return $this->response; 
                }

                 
                $payload = [
                    "iat" => $token_data["iat"],
                    "exp" => $token_data["exp"], 
                    "sub" => ['id' => $user->level, 'idm' => $idm, 'email' => $user->email, 'sesid' => $sesid, 'amb' => $amb, 'type' => $user->id_type]
                ];

                $token = JWT::encode($payload,  $options["secret"], "HS256");    

                //$this->logger->info('User Login', array("user" => $user->level, "sesid" => $sesid) );
                $this->response->setResponse(true);
                $this->response->result = ['token' => $token, 'user' => $user ];
                
            }
            else
            {
                $this->response->setResponse(false);
            }
            return $this->response;  
        }
        catch(Exception $e)
        { 
            $this->response->setResponse(false);
            return $this->response;  
        }
    } 

    public function checkUserExpired($id_user)
    {
        try
        {
            $resultBookCode = array();
            $stm = $this->dbmaster->prepare("SELECT * FROM $this->table_book_code WHERE id_user_join = ?");
            $stm->execute(array($id_user));
            $resultBookCode = $stm->fetchAll();
            if( count($resultBookCode) > 0){
                if( date('Y-m-d G:H:i') > $resultBookCode[0]->date_expired )
                {
                    return true;
                }   
            }
            return false;
        }
        catch(Exception $e)
        {
            return false;
        }
    }

    public function checkUrlValidTypeUser($token_data, $url_type)
    { 
        $type_token = ($token_data->type == '1') ? "estudiante" : "docente";
        if(trim($url_type) != 'docente' || trim($url_type) != 'estudiante')
        {
            return array("valid" => true);
        }
        
        if( trim($type_token) == trim($url_type) )
        {
            return array("valid" => true);
        }
        else
        {
            return array("valid" => false, "" => $type_token);
        }
    }

}