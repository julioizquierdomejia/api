<?php
use App\Model\UserModel;
use Firebase\JWT\JWT;
use Tuupola\Base62;

$app->group('/user/', function () {
    
    $this->get('test', function ($req, $res, $args) {
      return $res->getBody()
        ->write('Hello Users');
    });  

    $this->get('getAll/groups', function ($req, $res) { 
      $token_data = $req->getAttribute("decoded_token_data")["sub"];
      $um = new UserModel($token_data);
      
      return $res
         ->withHeader('Content-type', 'application/json')
         ->getBody()
         ->write(
          json_encode(
            $um->getType()
          )
      );
    });  

    $this->get('getAll/groups/byCodeClass/{code_class}', function ($req, $res, $args) { 
      $token_data = $req->getAttribute("decoded_token_data")["sub"];
      $um = new UserModel($token_data);

      return $res
         ->withHeader('Content-type', 'application/json')
         ->getBody()
         ->write(
          json_encode(
            $um->GetAllGroupsByCodeClass($args["code_class"])
          )
      );
    }); 

    $this->get('get/extraInfo', function ($req, $res) { 
      $token_data = $req->getAttribute("decoded_token_data")["sub"];
      $um = new UserModel($token_data);
      
      return $res
         ->withHeader('Content-type', 'application/json')
         ->getBody()
         ->write(
          json_encode(
            $um->GetExtraInfo()
          )
      );
    }); 

    

    $this->get('getType', function ($req, $res) { 
      $um = new UserModel();
      
      return $res
         ->withHeader('Content-type', 'application/json')
         ->getBody()
         ->write(
          json_encode(
            $um->getType()
          )
      );
    });  

      $this->get('getJoinType', function ($req, $res) { 
      $um = new UserModel();
      
      return $res
         ->withHeader('Content-type', 'application/json')
         ->getBody()
         ->write(
          json_encode(
            $um->getJoinType()
          )
      );
    });  

    $this->get('getJoinStatus/{class_code}', function ($req, $res, $args) { 
      $token_data = $req->getAttribute("decoded_token_data")["sub"];
      $um = new UserModel($token_data);
      
      return $res
         ->withHeader('Content-type', 'application/json')
         ->getBody()
         ->write(
          json_encode(
            $um->getJoinStatus( $args["class_code"] )
          )
      );
    });  

    $this->post('validateJoin', function ($req, $res) { 
      $um = new UserModel(); 
      return $res
         ->withHeader('Content-type', 'application/json')
         ->getBody()
         ->write(
          json_encode(
            $um->validateJoin( $req->getParsedBody() )
          )
      );
    }); 

    $this->get('alumn/{alumn_code}', function ($req, $res, $args) { 
      $token_data = $req->getAttribute("decoded_token_data")["sub"];
      $um = new UserModel($token_data);
      
      return $res
         ->withHeader('Content-type', 'application/json')
         ->getBody()
         ->write(
          json_encode(
            $um->getAlumn( $args["alumn_code"] )
          )
      );
    });  

});

$app->post('/user', function ($req, $res) { 
  $um = new UserModel(); 
  return $res
     ->withHeader('Content-type', 'application/json')
     ->getBody()
     ->write(
      json_encode(
        $um->register( $req->getParsedBody() )
      )
  );
});

$app->post('/user/set/tutorialClass', function ($req, $res) { 
  $token_data = $req->getAttribute("decoded_token_data")["sub"];
  $um = new UserModel($token_data);
  $data = $req->getParsedBody();
  $data["field"] = 'tutorialClass';

  return $res
     ->withHeader('Content-type', 'application/json')
     ->getBody()
     ->write(
      json_encode(
        $um->UpdateField( $data )
      )
  );
}); 
 

 $app->post('/user/groups', function ($req, $res) { 
  $token_data = $req->getAttribute("decoded_token_data")["sub"];
  $um = new UserModel($token_data); 

  return $res
     ->withHeader('Content-type', 'application/json')
     ->getBody()
     ->write(
      json_encode(
        $um->InserOrUpdateGroup( $req->getParsedBody() )
      )
  );
}); 

$app->delete('/user/groups/{code_class}/{id_user_group}', function ($req, $res, $args) { 
  $token_data = $req->getAttribute("decoded_token_data")["sub"];
  $um = new UserModel($token_data); 

  return $res
     ->withHeader('Content-type', 'application/json')
     ->getBody()
     ->write(
      json_encode(
        $um->deleteUsersGroup( $args["id_user_group"], $args["code_class"], true )
      )
  );
}); 
 
 