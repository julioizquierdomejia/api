<?php
use App\Model\PosLocateModel;

$app->group('/pos/', function () {
    
    $this->get('test', function ($req, $res, $args) {
        return $res->getBody()
                   ->write('Hello Users');
    });
    
    $this->get('locate', function ($req, $res, $args) {
        $pm = new PosLocateModel();
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $pm->GetAll()
            )
        );
    });
    
    $this->get('get/{id}', function ($req, $res, $args) {
        $pm = new PosLocateModel();
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $pm->Get($args['id'])
            )
        );
    });

    $this->get('byZone/{id}', function ($req, $res, $args) {
        $pm = new PosLocateModel();
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $pm->byZone($args['id'])
            )
        );
    });

    $this->get('byDistrict/{id}', function ($req, $res, $args) {
        $pm = new PosLocateModel();
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $pm->byDistrict($args['id'])
            )
        );
    });
  
    $this->post('save', function ($req, $res) {
        $pm = new PosLocateModel();
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $pm->InsertOrUpdate(
                    $req->getParsedBody()
                )
            )
        );
    });
    
    $this->post('delete/{id}', function ($req, $res, $args) {
        $pm = new PosLocateModel();
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $pm->Delete($args['id'])
            )
        );
    });
    
});