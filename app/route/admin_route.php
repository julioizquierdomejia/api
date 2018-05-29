<?php
use App\Model\AdminModel;

$app->group('/admin/', function () {
    
    $this->get('test', function ($req, $res, $args) {
        return $res->getBody()
                   ->write('Hello Users');
    });
    
    $this->get('menu', function ($req, $res, $args) {
        $um = new AdminModel();
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $um->GetAllMenu()
            )
        );
    });
    
    $this->get('menu/{id}', function ($req, $res, $args) {
        $um = new BookModel();
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $um->GetMenu($args['id'])
            )
        );
    });
 
    
});