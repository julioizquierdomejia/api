<?php
use App\Model\StudystageModel;

$app->group('/studystage/', function () {
    
    $this->get('test', function ($req, $res, $args) {
        return $res->getBody()
                   ->write('Hello Users');
    });
    
    $this->get('getAll', function ($req, $res, $args) {
        $um = new StudystageModel(); 
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
        $um = new StudystageModel(); 
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $um->Get($args['id'])
            )
        );
    });

    $this->get('byCode/{code}', function ($req, $res, $args) {
        $um = new StudystageModel(); 
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $um->byCode($args['code'])
            )
        );
    });

    $this->post('save', function ($req, $res) {
        $um = new StudystageModel();
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $um->InsertOrUpdate(
                    $req->getParsedBody()
                )
            )
        );
    }); 
 
    
});

