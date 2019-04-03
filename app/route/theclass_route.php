<?php
use App\Model\TheClassModel; 

$app->group('/class/', function () {
    
    $this->get('test', function ($req, $res, $args) { 
        return $res->getBody()
                   ->write('Hello Users');
    });

    $this->get('get/{id_class}', function ($req, $res, $args) {
        $token_data = $req->getAttribute("decoded_token_data")["sub"];
        $cm = new TheClassModel($token_data);

        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $cm->Get($args['id_class'])
            )
        );
    });  

    $this->get('get/alumn/byCode/{code_class}', function ($req, $res, $args) {
        $token_data = $req->getAttribute("decoded_token_data")["sub"];
        $cm = new TheClassModel($token_data);

        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $cm->GetByCodeAlumn($args['code_class'])
            )
        );
    });  


    $this->get('get/teacher/byCode/{code_class}', function ($req, $res, $args) {
        $token_data = $req->getAttribute("decoded_token_data")["sub"];
        $cm = new TheClassModel($token_data);

        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $cm->GetByCodeTeacher($args['code_class'])
            )
        );
    });  

    $this->get('getAll/teacher', function ($req, $res, $args) {
        $token_data = $req->getAttribute("decoded_token_data")["sub"];
        $cm = new TheClassModel($token_data);
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $cm->GetAllTeacher()
            )
        );
    }); 

    $this->get('getAll/alumn', function ($req, $res, $args) {
        $token_data = $req->getAttribute("decoded_token_data")["sub"];
        $cm = new TheClassModel($token_data);
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $cm->GetAllAlumn()
            )
        );
    }); 

    $this->get('getAll/alumn/byBook/{id_book}', function ($req, $res, $args) {
        $token_data = $req->getAttribute("decoded_token_data")["sub"];
        $cm = new TheClassModel($token_data);
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $cm->GetAllAlumnByBook($args['id_book'])
            )
        );
    });  

    $this->get('get/alumns/byCode/{code_class}', function ($req, $res, $args) {
        $token_data = $req->getAttribute("decoded_token_data")["sub"];
        $cm = new TheClassModel($token_data);

        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $cm->GetAlumnsByCode($args['code_class'])
            )
        );
    });  

    $this->get('checkCode/{code_class}', function ($req, $res, $args) {
        $token_data = $req->getAttribute("decoded_token_data")["sub"];
        $cm = new TheClassModel($token_data);

        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $cm->checkCodeClass($args['code_class'])
            )
        );
    });  
      
});

$app->post('/class/join', function ($req, $res, $args) {
    $token_data = $req->getAttribute("decoded_token_data")["sub"];
    $cm = new TheClassModel($token_data);

    return $res
       ->withHeader('Content-type', 'application/json')
       ->getBody()
       ->write(
        json_encode(
          $cm->JoinA(
              $req->getParsedBody()
          )
        )
    );
});   

$app->post('/class', function($req, $res) {
  $token_data = $req->getAttribute("decoded_token_data")["sub"]; 
  $cm = new TheClassModel($token_data); 

  return $res
     ->withHeader('Content-type', 'application/json')
     ->getBody()
     ->write(
      json_encode(
          $cm->InsertOrUpdate(
              $req->getParsedBody()
          )
      )
  );
});
