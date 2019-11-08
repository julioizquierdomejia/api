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

    $this->get('tablesmantenience/byCode/{code}', function ($req, $res, $args) {
        $um = new AdminModel();
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $um->GetTableMantenienceByCode($args['code'])
            )
        );
    });
  
    $this->get('getTableData/{code}', function ($req, $res, $args) {
        $um = new AdminModel();
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $um->GetTableData($args['code'])
            )
        );
    });

    $this->get('getTableData/{code}/{id}', function ($req, $res, $args) {
        $um = new AdminModel();
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $um->GetTableDataEdit( $args['code'], $args['id'] )
            )
        );
    }); 

    $this->get('fileExists/{type}/{code}/{name}', function ($req, $res, $args) {
        $um = new AdminModel();
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $um->fileExists( $args['type'] , $args['code'], $args['name'] )
            )
        );
    }); 
    
});


$app->post('/admin/save', function ($req, $res) {
    $token_data = $req->getAttribute("decoded_token_data")["sub"]; 
    $am = new AdminModel($token_data); 

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

$app->post('/admin/media', function($req, $res) {
    $token_data = $req->getAttribute("decoded_token_data")["sub"]; 
    $am = new AdminModel($token_data);  

    return $res
       ->withHeader('Content-type', 'application/json')
       ->getBody()
       ->write(
        json_encode(
            $am->uploadMedia(
                $req->getParsedBody()
            )
        )
    );
  });