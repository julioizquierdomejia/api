<?php
use App\Model\WebConfigModel;

$app->group('/web/', function () {
    
    $this->get('test', function ($req, $res, $args) {
        return $res->getBody()
                   ->write('Hello Users');
    });
    
    $this->get('nav', function ($req, $res, $args) {
        $wcm = new WebConfigModel();
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $wcm->GetAllNav()
            )
        );
    });

    $this->get('nav2', function ($req, $res, $args) {
        $wcm = new WebConfigModel();
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $wcm->GetAllNav2()
            )
        );
    });

    $this->get('config', function ($req, $res, $args) {
        $wcm = new WebConfigModel();
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $wcm->GetAllConfig()
            )
        );
    });

    $this->get('circle_pe', function ($req, $res, $args) {
        $wcm = new WebConfigModel();
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $wcm->GetCirclePE()
            )
        );
    }); 

    $this->get('circle_pe/byCode/{code}', function ($req, $res, $args) {
        $wcm = new WebConfigModel();
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $wcm->GetCirclePEByCode($args['code'])
            )
        );
    });

    $this->get('circle_pe/especial/{id}', function ($req, $res, $args) {
        $wcm = new WebConfigModel();
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $wcm->GetCirclePEEspecial($args['id'])
            )
        );
    }); 

    $this->get('staff/capaciters', function ($req, $res, $args) {
        $wcm = new WebConfigModel(); 
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $wcm->GetStaffCapaciters()
            )
        );
    }); 
    
    $this->get('slider/{area}', function ($req, $res, $args) {
        $wcm = new WebConfigModel();
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $wcm->GetSlider($args['area'])
            )
        );
    }); 

    $this->post('sendContact', function ($req, $res, $args){  
        $wcm = new WebConfigModel();  
        return $res
             ->withHeader('Content-type', 'application/json')
             ->getBody()
             ->write(
              json_encode(
                  $wcm->sendContact( 
                    $req->getParsedBody()
                  )
              )
        );
    });    
    
    
});