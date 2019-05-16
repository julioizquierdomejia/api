<?php
namespace App\Model; 

class GeneralConfig
{ 
	public $bd_base_pe = 'basepe';

    public $table_notification = 'notification';
    public $table_notification_type = 'notification_type'; 
    public $table_notification_extra = 'notification_extra'; 

    public $bdbase = 'basepe';

    public $table_book_code = 'book_code'; 

    public $table_user_master = 'user_master';
    public $table_user_master_type = 'user_master_type';
    public $table_user_master_scholl = 'user_master_scholl';
    public $table_user = 'user';
    public $table_user_type = 'user_type';
    public $table_user_join_type = 'user_join_type';
    public $table_user_join_type_user = 'user_join_type_user';
    public $table_class = 'class';
    public $table_class_master = 'class_master';
    public $table_user_class = 'user_class';
    public $table_resource = 'resource';
    public $table_book = 'book';
    public $table_grade = 'grade';
    public $table_serie = 'book_series';
    public $table_studystage = 'studystage';
    public $table_scholls = 'scholls';
    public $table_book_group = 'book_group_master';
    public $table_users_group = 'users_group';
    public $table_users_group_user = 'users_group_user';

    public $table_resources = 'resource';
    public $table_resources_type = 'resource_type';
    public $table_resources_upload = 'resource_uploads';
    public $table_resources_indicator = 'resource_indicator';
    
    public $table_sessions = 'learn_sessions';
    public $table_competitions = 'competitions';
    public $table_capacitys = 'capacitys';
    public $table_indicators = 'indicators'; 
    public $table_calification_type = 'calification_type';
    public $table_evaluation_range = 'calification_range';
    public $table_scored_letters = 'scored_letters';

    public $table_nav_private = 'nav_menu_item_private';
    public $table_nav = 'nav_menu_item';
    public $table_nav2 = 'nav2_menu_item';
    public $table_config = 'config_pe';
    public $table_dd_nav = 'dropdown_nav_item';
    public $table_dd_nav2 = 'dropdown_nav2_item'; 
    public $resources_general_table = 'upload_materials'; 

    public $table_question = 'activity_question';
    public $table_question_join = 'activity_question_join';
    public $table_question_evaluate = 'activity_question_evaluate';
    public $table_question_evaluate_log = 'activity_question_evaluate_log';


    public $path_upload = '../../lib/media/upload/';
    public $path_imgs_head = '../../lib/media/pe-content/img_head_activity/';   

    public $path_upload_pecontent = '../../lib/media/pe-content/upload/';
    public $path_upload_image_activity = '../../lib/media/pe-content/upload/activity/';
    public $path_upload_activitys = '../../lib/media/pe-content/activitys/';
    public $path_upload_activitys_question = '../../lib/media/pe-content/activitys_questions/';

    public $response;


    //types notifications        
    public $alumn_class_join = 1;
    public $alumn_book_join = 2; 
    public $teacher_book_join = 3; 
    public $resolve_activity_join = 4;
    public $evaluation_realized_teacher = 5;

    public $token_data;

	public $gc_configEntityPublicData = array(
		"alumn" => array(
			"fields" =>  "CONCAT('Alumno: ',first_name,' ',last_name) detail1, CONCAT('Correo: ',email)  detail2"
			),
		"teacher" => array(
			"fields" =>  "CONCAT('Profesor: ',first_name,' ',last_name) detail1, CONCAT('Correo: ',email)  detail2"
			),
		"class" => array(
			"fields" =>  "CONCAT('Clase: ',name) detail1, CONCAT('Codigo: ',code)detail2"
			),
		"book" => array(
			"fields" =>  "CONCAT('Libro: ',name) detail1, CONCAT('Codigo: ',code)detail2"
			),
		"resource" => array(
			"fields" =>  "CONCAT('Recurso / Actividad: ',name) detail1, CONCAT('Pagina: ',page) detail2"
			),
	);

	//configuracion de generacion de codigo de clase
	public $lengh_letter_code_class = 1;
	public $lengh_number_code_class = 4;

    //configuracion de generacion de codigo BOOK ID CARD
    public $lengh_code_BIC = 4;

    public function getAllAmbsUser($db, $idm)
    {
        $totalAmbs = array(); 
        $resultAmbsScholls = (object)[];
        $stm = $db->prepare("SELECT sc.id, sc.amb, ums.id_user_link FROM $this->table_user_master_scholl ums INNER JOIN $this->table_scholls sc on ums.id_scholl = sc.id  WHERE id_user_master = ?"); 
        $stm->execute(array($idm));
        $resultAmbsScholls = $stm->fetchAll();
        foreach ($resultAmbsScholls as $key => $value) { 
            array_push($totalAmbs, $value);
        }
        return $totalAmbs;
    } 
}