<?php
use App\Model\NotificationModel;

$app->group('/notification/', function () {
    
    $this->get('test', function ($req, $res, $args) { 
        return $res->getBody()
                   ->write('Hello Users');
    });

    $this->get('getAll', function ($req, $res, $args) {
        $token_data = $req->getAttribute("decoded_token_data")["sub"];
        $nm = new NotificationModel($token_data);
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $nm->GetAll($token_data)
            )
        ); 
    });
     

  $this->get('getAll/user', function ($req, $res, $args) {
      $token_data = $req->getAttribute("decoded_token_data")["sub"];
      $nm = new NotificationModel($token_data);
        
     return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $nm->GetAllByUser($args['id_user'])
            )
      ); 
  });
 
  $this->get('get/{id_notification}/{amb}', function ($req, $res, $args) {
      $token_data = $req->getAttribute("decoded_token_data")["sub"];
      $nm = new NotificationModel($token_data);
      
      return $res
         ->withHeader('Content-type', 'application/json')
         ->getBody()
         ->write(
          json_encode(
              $nm->Get($args['id_notification'], $args['amb'])
          )
      ); 
  });
 
    
});