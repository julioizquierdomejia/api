<?php
use App\Model\WebConfigModel;
use App\Model\PeConfigModel;

$app->group('/pe/', function () {
    
    $this->get('test', function ($req, $res, $args) {
        return $res->getBody()
                   ->write('Hello Users');
    });
    
    $this->get('nav_private', function ($req, $res, $args) {
        $pcn = new PeConfigModel();
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $pcn->GetPrivateNav()
            )
        );
    }); 

    $this->get('nav', function ($req, $res, $args) {
        $pcn = new WebConfigModel();
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $pcn->GetAllPeNav()
            )
        );
    });

    $this->get('nav2', function ($req, $res, $args) {
        $pcn = new WebConfigModel();
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $pcn->GetAllPeNav2()
            )
        );
    }); 

    $this->get('config', function ($req, $res, $args) {
        $pcn = new PeConfigModel();
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $pcn->GetAllConfig()
            )
        );
    }); 
     
    $this->get('resources/byBook/{id_book}/{class_code}', function ($req, $res, $args) {
        $token_data = $req->getAttribute("decoded_token_data")["sub"]; 
        $pcn = new PeConfigModel($token_data);
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $pcn->GetResourcesByBook($args['id_book'],$args['class_code'])
            )
        );
    }); 

    $this->get('scholl/byCode/{code}', function ($req, $res, $args) {
        $pcn = new PeConfigModel(); 
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $pcn->GetSchollByCode($args['code'])
            )
        );
    }); 

    $this->get('imgsHead', function ($req, $res, $args) {
        $pcn = new PeConfigModel(); 
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $pcn->GetImgsHead()
            )
        );
    }); 

     $this->get('generateTree/{id_book}', function ($req, $res, $args) {
        $pcn = new PeConfigModel();
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $pcn->GenerateTree($args['id_book'])
            )
        );
    });
});

$app->post('/pe/resources', function ($req, $res) { 
    $token_data = $req->getAttribute("decoded_token_data")["sub"]; 
    $pcn = new PeConfigModel($token_data);

    return $res
       ->withHeader('Content-type', 'application/json')
       ->getBody()
       ->write(
        json_encode(
            $pcn->createResource(
                $req->getParsedBody()
            )
        )
    );
});


$app->post('/pe/resources/link', function ($req, $res) { 
    $token_data = $req->getAttribute("decoded_token_data")["sub"]; 
    $pcn = new PeConfigModel($token_data);

    return $res
       ->withHeader('Content-type', 'application/json')
       ->getBody()
       ->write(
        json_encode(
            $pcn->createResourceLink(
                $req->getParsedBody()
            )
        )
    );
});
 