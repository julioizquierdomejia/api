<?php
use App\Model\SecurityModel;
use Firebase\JWT\JWT;
use Tuupola\Base62;

$app->group('/secure/', function () {
    
    $this->get('test', function ($req, $res, $args) {
      $token_data = $req->getAttribute("decoded_token_data")["sub"]; 
      $url = $req->getHeader('Referer')[0];
      $uriSegments = explode("/", $url); 
    
      $sm = new SecurityModel(); //$this->logger
      $verif = $sm->checkUrlValidTypeUser($token_data, $uriSegments[4]); 

      if($verif["valid"])
        return $res->withHeader("Content-Type", "application/json")
                  ->write(json_encode(array("text" => "User Logged :D", "success" => true, "token" => $token_data), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT))
                  ->withStatus(200);
      else
        return $res->withHeader("Content-Type", "application/json")
                  ->write(json_encode(array("Error" => "Access denied to url", "area_valid" => $verif["area_valid"]), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT))
                  ->withStatus(500);
    }); 
    $this->get('login', function ($req, $res) {
      $authPair = str_replace("Basic ", "", $req->getHeaderLine("HTTP_AUTHORIZATION") );       
                
      $options_jwt = $this->get('settings')['jwt']; 
      $uri = $req->getUri(); 

      
      $sm = new SecurityModel();
      
      return $res
         ->withHeader('Content-type',  'application/json')
         ->getBody()
         ->write(
          json_encode(
            $sm->login(
              $authPair, $options_jwt, $uri
            )
          )
      );
    });  

    $this->post('changeAmb', function ($req, $res, $args) {
      $token_data = $req->getAttribute("decoded_token_data"); 
      $options_jwt = $this->get('settings')['jwt']; 
      $uri = $req->getUri(); 

      $sm = new SecurityModel();

      return $res
         ->withHeader('Content-type', 'application/json')
         ->getBody()
         ->write(
          json_encode(
            $sm->changeAmb(
              $req->getParsedBody(), $options_jwt, $uri, $token_data
            )
          )
      );
    });

    
    $this->post('baseAmb', function ($req, $res, $args) {
      $token_data = $req->getAttribute("decoded_token_data"); 
      $options_jwt = $this->get('settings')['jwt']; 
      $uri = $req->getUri(); 

      $sm = new SecurityModel();

      return $res
         ->withHeader('Content-type', 'application/json')
         ->getBody()
         ->write(
          json_encode(
            $sm->baseAmb(
              $options_jwt, $uri, $token_data
            )
          )
      );
    });  

});
 