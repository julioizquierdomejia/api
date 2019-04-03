<?php
use App\Model\SerieModel;

$app->group('/bookseries/', function () {
    
    $this->get('test', function ($req, $res, $args) {
        return $res->getBody()
                   ->write('Hello Users');
    });
    
    $this->get('getAll', function ($req, $res, $args) {
        $sm = new SerieModel(); 
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
        $sm = new SerieModel(); 
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $sm->Get($args['id'])
            )
        );
    });

    $this->get('byCode/{code}', function ($req, $res, $args) {
        $sm = new SerieModel(); 
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $sm->byCode($args['code'])
            )
        );
    });

    $this->get('byStage/{id_stage}', function ($req, $res, $args) {
        $sm = new SerieModel(); 
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $sm->byStage($args['id_stage'])
            )
        );
    });
    
    $this->post('save', function ($req, $res) {
        $sm = new SerieModel();
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $sm->InsertOrUpdate(
                    $req->getParsedBody()
                )
            )
        );
    });
    
    $this->post('delete/{id}', function ($req, $res, $args) {
        $sm = new SerieModel();
        
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