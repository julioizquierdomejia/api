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
        $um = new AdminModel();
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $um->GetMenu($args['id'])
            )
        );
    });

    $this->get('groupmenu', function ($req, $res, $args) {
        $um = new AdminModel();
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $um->GetAllGroupMenu()
            )
        );
    });
    
    $this->get('groupmenu/{id}', function ($req, $res, $args) {
        $um = new AdminModel();
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $um->GetGroupMenu($args['id'])
            )
        );
    });

    $this->get('tablesmantenience', function ($req, $res, $args) {
        $um = new AdminModel();
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $um->GetAllTablesMantenience()
            )
        );
    });

    $this->get('tablesmantenience/{id}', function ($req, $res, $args) {
        $um = new AdminModel();
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $um->GetTableMantenience($args['id'])
            )
        );
    });

    
 
    
});