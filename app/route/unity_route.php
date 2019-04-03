<?php
use App\Model\UnityModel;

$app->group('/unity/', function () {
    
    $this->get('test', function ($req, $res, $args) {
        return $res->getBody()
                   ->write('Hello Users');
    });
    
    $this->get('getAll', function ($req, $res, $args) {
        $um = new UnityModel();
        
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
        $um = new UnityModel();
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $um->Get($args['id'])
            )
        );
    });

    $this->get('getDemo/{idbook}', function ($req, $res, $args) {
        $um = new UnityModel();
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $um->GetDemo($args['idbook'])
            )
        );
    });

    $this->get('byBook/{id}', function ($req, $res, $args) {
        $um = new UnityModel();
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $um->byBook($args['id'])
            )
        );
    });

    $this->get('byNumber/{idbook}/{number}', function ($req, $res, $args) {
        $um = new UnityModel();
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $um->byNumber($args['idbook'], $args['number'])
            )
        );
    }); 

    
    
    $this->post('save', function ($req, $res) {
        $um = new UnityModel();
        
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
    
    $this->post('delete/{id}', function ($req, $res, $args) {
        $um = new UnityModel();
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $um->Delete($args['id'])
            )
        );
    });
    
});