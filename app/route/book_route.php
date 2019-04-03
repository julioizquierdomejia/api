<?php
use App\Model\BookModel;

$app->group('/book/', function () {
    
    $this->get('test', function ($req, $res, $args) {
        return $res->getBody()
                   ->write('Hello Users');
    });
    
    $this->get('getAll', function ($req, $res, $args) {
        $um = new BookModel();
        
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
        $um = new BookModel();
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $um->Get($args['id'])
            )
        );
    });

    $this->get('registered', function ($req, $res, $args) {
        $token_data = $req->getAttribute("decoded_token_data")["sub"];
        $um = new BookModel($token_data);
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $um->GetRegistered()
            )
        );
    });

    $this->get('byCode/{code}', function ($req, $res, $args) {
        $um = new BookModel(); 
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $um->byCode($args['code'])
            )
        );
    });

    $this->get('byGrade/{id}', function ($req, $res, $args) {
        $um = new BookModel(); 
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $um->byGrade($args['id'])
            )
        );
    });

    $this->get('bySerie/{id_serie}', function ($req, $res, $args) {
        $um = new BookModel(); 
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $um->bySerie($args['id_serie'])
            )
        );
    }); 

    $this->get('bySerie/orderByGrade/{id_serie}', function ($req, $res, $args) {
        $um = new BookModel(); 
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $um->bySerie($args['id_serie'], 'id_grade')
            )
        );
    });

     $this->get('bySerieCS/orderByGrade/{id_serie}', function ($req, $res, $args) {
        $um = new BookModel(); 
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $um->bySerieCS($args['id_serie'], 'id_grade, name')
            )
        );
    });
    
    $this->post('save', function ($req, $res) {
        $um = new BookModel();
        
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
        $um = new BookModel();
        
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