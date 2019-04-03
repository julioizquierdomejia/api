<?php
use App\Model\ResourceModel;

$app->group('/resource/', function (){

    $this->get('test', function ($req, $res, $args) {
        return $res->getBody()
                   ->write('Hello Users');
    });

    $this->get('getAll', function ($req, $res, $args) {
        $token_data = $req->getAttribute("decoded_token_data")["sub"]; 
        $rm = new ResourceModel($token_data); 

        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $rm->GetAll()
            )
        );
    });
    
    $this->get('activity/getAll', function ($req, $res, $args) {
        $token_data = $req->getAttribute("decoded_token_data")["sub"]; 
        $rm = new ResourceModel($token_data); 
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $rm->GetAllActivitys()
            )
        );
    });

    $this->get('get/{id}', function ($req, $res, $args) {
        $token_data = $req->getAttribute("decoded_token_data")["sub"]; 
        $rm = new ResourceModel($token_data); 

        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $rm->Get($args['id'])
            )
        );
    }); 

    $this->get('checkHistory/{id}', function ($req, $res, $args) {
        $token_data = $req->getAttribute("decoded_token_data")["sub"]; 
        $rm = new ResourceModel($token_data); 

        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $rm->checkResourceHistory($args['id'])
            )
        );
    }); 
    

    $this->get('byType/{id_type}', function ($req, $res, $args) {
        $token_data = $req->getAttribute("decoded_token_data")["sub"]; 
        $rm = new ResourceModel($token_data); 
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $rm->byType($args['id_type'])
            )
        );
    });  

    $this->get('byBook/{id}', function ($req, $res, $args) {
        $token_data = $req->getAttribute("decoded_token_data")["sub"]; 
        $rm = new ResourceModel($token_data); 
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $rm->byBook($args['id'])
            )
        );
    });  

    $this->get('byUnity/{id}/{class_code}', function ($req, $res, $args) {
        $token_data = $req->getAttribute("decoded_token_data")["sub"]; 
        $rm = new ResourceModel($token_data); 

        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $rm->byUnity($args['id'], $args['class_code'])
            )
        );
    });

    $this->get('activity/byUnity/{id}', function ($req, $res, $args) {
        $token_data = $req->getAttribute("decoded_token_data")["sub"]; 
        $rm = new ResourceModel($token_data); 

        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $rm->byUnityActivity($args['id'])
            )
        );
    });

    $this->get('activity/question/{code}', function ($req, $res, $args) {
        $token_data = $req->getAttribute("decoded_token_data")["sub"]; 
        $rm = new ResourceModel($token_data); 

        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $rm->GetActivityQuestion($args['code'])
            )
        );
    });

    $this->get('activity/question/log/{code}', function ($req, $res, $args) {
        $token_data = $req->getAttribute("decoded_token_data")["sub"]; 
        $rm = new ResourceModel($token_data); 

        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $rm->GetLogQuestionEvaluation($args['code'])
            )
        );
    });

    $this->get('admin/byUnity/{id}', function ($req, $res, $args) {
        $token_data = $req->getAttribute("decoded_token_data")["sub"]; 
        $rm = new ResourceModel($token_data); 

        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $rm->byUnityAdmin($args['id'])
            )
        );
    });

    $this->get('activity/admin/byUnity/{id}', function ($req, $res, $args) {
        $token_data = $req->getAttribute("decoded_token_data")["sub"]; 
        $rm = new ResourceModel($token_data); 

        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $rm->byUnityAdminActivity($args['id'])
            )
        );
    });

    $this->get('activity/json/{id}/{code}', function ($req, $res, $args){ 
      $token_data = $req->getAttribute("decoded_token_data")["sub"]; 
      $rm = new ResourceModel($token_data); 

      $id_base = ($token_data->amb != 'basepe') ? $rm->getIdBase($args['code']) : $args['id'];

      $filename = $id_base . '_' . $args['code'] . "_fbdata.json";
      $path = "../../lib/media/pe-content/activitys/" . $filename;   
      return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $rm->getJson($path)
            )
        );
    });

    $this->get('activity/question/json/{id}/{code_question}', function ($req, $res, $args){ 
      $token_data = $req->getAttribute("decoded_token_data")["sub"]; 
      $rm = new ResourceModel($token_data);  
      $id_question = intval(substr($args['code_question'], 0, strpos($args['code_question'], "_"))); 

      $code_question = substr($args['code_question'], strpos($args['code_question'], "_") + 1 );
      $filename = $args['id'] . '_' . $id_question . '_' . $code_question . "_question.json";      
      $path = "../../lib/media/pe-content/activitys_questions/" . $filename;   
      //var_dump( $path );
      
      return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $rm->getJson($path)
            )
        );
    });  

    $this->get('activity/getIndicators/{id_resource}', function ($req, $res, $args) {
        $token_data = $req->getAttribute("decoded_token_data")["sub"]; 
        $rm = new ResourceModel($token_data); 

        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $rm->GetIndicators($args['id_resource'])
            )
        );
    });

    $this->post('activity/json/{id_combined}', function ($req, $res, $args){
      $token_data = $req->getAttribute("decoded_token_data")["sub"];  
      $rm = new ResourceModel($token_data); 

      $phpObj = json_decode(file_get_contents("php://input")); 
      $filename = $args['id_combined'] . "_fbdata.json";
      $path = "../../lib/media/pe-content/activitys/" . $filename; 
      $id_activity = explode( '_', $args['id_combined'] )[0]; 
      return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $rm->saveJson($id_activity, $path , $phpObj)
            )
        );
    });    
    
    $this->post('delete/{id}', function ($req, $res, $args) {
        $token_data = $req->getAttribute("decoded_token_data")["sub"]; 
        $rm = new ResourceModel($token_data); 
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $rm->Delete($args['id'])
            )
        );
    });


    $this->get('getAllType', function ($req, $res, $args) {
        $token_data = $req->getAttribute("decoded_token_data")["sub"]; 
        $rm = new ResourceModel($token_data); 
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $rm->GetAllType()
            )
        );
    });
    
    $this->get('getType/{id}', function ($req, $res, $args) {
        $token_data = $req->getAttribute("decoded_token_data")["sub"]; 
        $rm = new ResourceModel($token_data); 

        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $rm->GetType($args['id'])
            )
        );
    });

    $this->get('filesUpload/{id_resource}', function($req, $res, $args) {
      $token_data = $req->getAttribute("decoded_token_data")["sub"]; 
      $rm = new ResourceModel($token_data); 

      return $res
         ->withHeader('Content-type', 'application/json')
         ->getBody()
         ->write(
          json_encode(
            $rm->getResourseUpload(
              $args['id_resource']
            )
          )
      );
    }); 
});
 

$app->post('/resource', function($req, $res) {
      $token_data = $req->getAttribute("decoded_token_data")["sub"]; 
      $rm = new ResourceModel($token_data); 

      return $res
         ->withHeader('Content-type', 'application/json')
         ->getBody()
         ->write(
          json_encode(
              $rm->InsertOrUpdate(
                  $req->getParsedBody()
              )
          )
      );
    });

$app->post('/resource/activity', function($req, $res) {
      $token_data = $req->getAttribute("decoded_token_data")["sub"]; 
      $rm = new ResourceModel($token_data);

      return $res
         ->withHeader('Content-type', 'application/json')
         ->getBody()
         ->write(
          json_encode(
              $rm->InsertOrUpdate(
                  $req->getParsedBody()
              )
          )
      );
    });

$app->post('/resource/files', function($req, $res) {
      $token_data = $req->getAttribute("decoded_token_data")["sub"]; 
      $rm = new ResourceModel($token_data); 

      return $res
         ->withHeader('Content-type', 'application/json')
         ->getBody()
         ->write(
          json_encode(
              $rm->InsertOrUpdate(
                  $req->getParsedBody()
              )
          )
      );
    });
 

$app->post('/resource/activity/question/open', function ($req, $res) { 
    $token_data = $req->getAttribute("decoded_token_data")["sub"]; 
    $rm = new ResourceModel($token_data); 

    return $res
       ->withHeader('Content-type', 'application/json')
       ->getBody()
       ->write(
        json_encode(
            $rm->SaveQuestionOpen(
                $req->getParsedBody()
            )
        )
    );
});

$app->post('/resource/activity/question', function ($req, $res) {
    $token_data = $req->getAttribute("decoded_token_data")["sub"]; 
    $rm = new ResourceModel($token_data); 

    return $res
       ->withHeader('Content-type', 'application/json')
       ->getBody()
       ->write(
        json_encode(
            $rm->SaveQuestion(
                $req->getParsedBody()
            )
        )
    );
});

$app->post('/resource/activity/question/evaluation', function ($req, $res) {
    $token_data = $req->getAttribute("decoded_token_data")["sub"]; 
    $rm = new ResourceModel($token_data); 

    return $res
       ->withHeader('Content-type', 'application/json')
       ->getBody()
       ->write(
        json_encode(
            $rm->SaveQuestionEvaluation(
                $req->getParsedBody()
            )
        )
    );
});

$app->post('/resource/activity/upload', function ($req, $res) { 
    $token_data = $req->getAttribute("decoded_token_data")["sub"]; 
    $rm = new ResourceModel($token_data); 

    return $res
       ->withHeader('Content-type', 'application/json')
       ->getBody()
       ->write(
        json_encode(
            $rm->uploadImagesActivity(
              $req->getParsedBody()
            )
        )
    );
}); 

$app->post('/resource/status', function ($req, $res) { 
    $token_data = $req->getAttribute("decoded_token_data")["sub"]; 
    $rm = new ResourceModel($token_data); 

    return $res
       ->withHeader('Content-type', 'application/json')
       ->getBody()
       ->write(
        json_encode(
            $rm->SetStatus(
                $req->getParsedBody()
            )
        )
    );
});  