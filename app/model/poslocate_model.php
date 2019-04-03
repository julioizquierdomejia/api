<?php
namespace App\Model;

use App\Lib\Database;
use App\Lib\Response;

class PosLocateModel
{
    private $db;
    private $table = 'lc_pos';
    private $table_department = 'lc_department';
    private $table_district = 'lc_district';
    private $table_zone = 'lc_zone';
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
			$stm = $this->db->prepare("SELECT p.id, p.name, p.description, p.id_department, p.latitude, p.longitude, d.name lc_department, p.id_district, dt.name district_name, p.id_zone, z.name zone_name, p.addres, p.phone, p.mail from $this->table p INNER JOIN  $this->table_department d on p.id_department = d.id INNER JOIN $this->table_district dt on p.id_district = dt.id INNER JOIN $this->table_zone z on p.id_zone = z.id order by p.id_zone");
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

    public function byZone($id_zone)
    {
        try
        {
            $result = array(); 
            $stm = $this->db->prepare("SELECT * FROM $this->table WHERE id_zone = ?"); 
            $stm->execute(array($id_zone));

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

    public function byDistrict($id_district)
    {
        try
        {
            $result = array(); 
            $stm = $this->db->prepare("SELECT * FROM $this->table WHERE id_district = ?"); 
            $stm->execute(array($id_district));

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
                            name            = ?,
                            description     = ?,
                            id_department     = ?,
                            id_district     = ?,
                            id_zone         = ?,
                            latitude        = ?,
                            longitude       = ?,
                            addres          = ?,
                            phone           = ?,
                            mail            = ?,
                            updated         = ?,
                        WHERE id = ?";
                
                $this->db->prepare($sql)
                     ->execute(
                        array( 
                            $data['name'],
                            $data['description'],
                            $data['id_department'],
                            $data['id_district'],
                            $data['id_zone'],
                            $data['latitude'],
                            $data['longitude'],
                            $data['addres'],
                            $data['phone'],
                            $data['mail'],
                            $data['id'],
                            date('Y-m-d G:H:i')
                        )
                    );
            }
            else
            {
                $sql = "INSERT INTO $this->description
                            ( name, description, id_department, id_district, id_zone, latitude, longitude, addres, phone, mail, inserted )
                            VALUES (?,?,?,?,?,?,?,?,?,?,?)";
                
                $this->db->prepare($sql)
                     ->execute(
                        array(
                            $data['name'],
                            $data['description'],
                            $data['id_department'],
                            $data['id_district'],
                            $data['id_zone'],
                            $data['latitude'],
                            $data['longitude'],
                            $data['addres'],
                            $data['phone'],
                            $data['mail'],
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