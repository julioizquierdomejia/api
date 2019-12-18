<?php
namespace App\Lib;
 
use PDO;

class Database {
	
    public static function StartUp()
    {
		if( trim($_SERVER['SERVER_NAME']) == 'localhost' || trim($_SERVER['SERVER_NAME']) == '192.168.1.90' ){
			$bd = 'ebiope';
			$user = 'root';
			$pass = '';
		}else{
			$bd = 'ebiolibr_data';
			$user = 'ebiolibr_data';
			$pass = 'HCUebio810ved*';
		}
		
        $pdo = new PDO('mysql:host=192.168.1.90;dbname='.$bd.';charset=utf8', $user, $pass);
        
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        
        return $pdo;
    }

    public static function StartUpPe()
    {
		if( trim($_SERVER['SERVER_NAME']) == 'localhost' || trim($_SERVER['SERVER_NAME']) == '192.168.1.90' ){
			$bd = 'ebiopedata';
			$user = 'root';
			$pass = '';
		}else{
			$bd = 'ebiolibr_demo'; 
			$user = 'ebiolibr_master'; 
			$pass = 'HCUebio810ved*master';
		}
		
        $pdo = new PDO('mysql:host=192.168.1.90;dbname='.$bd.';charset=utf8', $user, $pass);
        
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ); 
        return $pdo;
    }

    public static function StartUpMaster()
    {
		if( trim($_SERVER['SERVER_NAME']) == 'localhost' || trim($_SERVER['SERVER_NAME']) == '192.168.1.90' ){
			$bd = 'ebiomaster';
			$user = 'root';
			$pass = '';
		}else{
			$bd = 'ebiolibr_master';
			$user = 'ebiolibr_master';
			$pass = 'HCUebio810ved*master';
		}
		
        $pdo = new PDO('mysql:host=192.168.1.90;dbname='.$bd.';charset=utf8', $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ); 
        return $pdo;
    } 
    public static function StartUpArea($area)
    {
    	if(!$area){
    		$area = 'demo';
    	}

		if( trim($_SERVER['SERVER_NAME']) == 'localhost' || trim($_SERVER['SERVER_NAME']) == '192.168.1.90' ){
			if($area == 'data'){
				$bd = 'ebiope';
			}else{
				$bd = 'ebiolibr_' . $area; 
			}
			$user = 'root';
			$pass = '';
			
		}else{
			$bd = 'ebiolibr_' . $area;
			$user = 'ebiolibr_master';
			$pass = 'HCUebio810ved*master';
		}

		$pdo = false;
		try {
			$pdo = new PDO('mysql:host=192.168.1.90;dbname='.$bd.';charset=utf8', $user, $pass);
		    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ); 			
		} catch (Exception $e) {
			$pdo = false;
		}		
        
        return $pdo;
    }
    public static function StartUpBase()
    {
		if( trim($_SERVER['SERVER_NAME']) == 'localhost' || trim($_SERVER['SERVER_NAME']) == '192.168.1.90' ){
			$bd = 'ebiolr_basepe';
			$user = 'root';
			$pass = '';
		}else{
			$bd = 'ebiolibr_basepe';
			$user = 'ebiolibr_master';
			$pass = 'HCUebio810ved*master';
		}
		
        $pdo = new PDO('mysql:host=192.168.1.90;dbname='.$bd.';charset=utf8', $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ); 
        return $pdo;
    } 
}
/*
El comienzo....

Ubicados en el año 2008, cursando estudios de mi carrera informatica, todo normal para un joven de 19 años con varios compáñeros de clase vivendo la epoca de los celulares nokia y los pantalones con bota normal, soltero y sin muchas expectativas reales. Los principales amigos del circulo eran Marta, Elvira, Efrain , Roland, entre otros. En el 4to semestre de la carrera conoce a Fabiola, y su grupo de amigos que con el tiempo se consolido con el nuestro. En las vacaciones del semestre usando el famoso messenger de hotmail logre frecuentar contacto con elinea con Fabiola, ella tenia su novio Daniel*/