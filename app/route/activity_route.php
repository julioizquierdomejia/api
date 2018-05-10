<?php
use App\Model\ActivityModel;

$app->group('/activity/', function () {
    
    $this->get('test', function ($req, $res, $args) {
        return $res->getBody()
                   ->write('Hello Users');
    });
    
    $this->get('getAll', function ($req, $res, $args) {
        $am = new ActivityModel();
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $um->GetAll()
            )
        );
    });
    
    $this->get('get/{id}', function ($req, $res, $args) {
        $am = new ActivityModel();
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $um->Get($args['id'])
            )
        );
    });

    $this->get('byBook/{id}', function ($req, $res, $args) {
        $am = new ActivityModel();
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $am->byBook($args['id'])
            )
        );
    }); 

    $this->get('byUnity/{id}', function ($req, $res, $args) {
        $am = new ActivityModel(); 
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $am->byUnity($args['id'])
            )
        );
    }); 

    $this->post('json/{id}', function ($req, $res, $args){
      $phpObj = json_decode(file_get_contents("php://input")); 
      $filename = $args['id'] . "_fbdata.json";
      $path = "../../lib/media/content/activitys/" . $filename; 

      $am = new ActivityModel();
      return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $am->saveJson($path , $phpObj)
            )
        );
    });    
    
    $this->post('delete/{id}', function ($req, $res, $args) {
        $am = new ActivityModel();
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $am->Delete($args['id'])
            )
        );
    });
    
});

$app->post('/activity', function ($req, $res) { 
    $am = new ActivityModel(); 
    return $res
       ->withHeader('Content-type', 'application/json')
       ->getBody()
       ->write(
        json_encode(
            $am->InsertOrUpdate(
                $req->getParsedBody()
            )
        )
    );
});