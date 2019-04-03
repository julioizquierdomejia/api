<?php
use App\Model\SchollModel;

$app->group('/scholl/', function () {
    
    $this->get('test', function ($req, $res, $args) {
        return $res->getBody()
                   ->write('Hello Users');
    });

    $this->get('getAll', function ($req, $res, $args) {
        $sm = new SchollModel();
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $sm->GetAll()
            )
        );
    });
     
    
    $this->get('get/{id}', function ($req, $res, $args) {
        $sm = new SchollModel();
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $sm->Get($args['id'])
            )
        );
    }); 
    
    $this->post('delete/{id}', function ($req, $res, $args) {
        $sm = new PosLocateModel();
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $sm->Delete($args['id'])
            )
        );
    });
    
});