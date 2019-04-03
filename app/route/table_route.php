<?php
use App\Model\TableModel;

$app->group('/table/', function () {
    
    $this->get('test', function ($req, $res, $args) {
        return $res->getBody()
                   ->write('Hello Table');
    }); 
    
    $this->get('get/{table}', function ($req, $res, $args) {
        $tm = new TableModel(); 
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $tm->Get($args['table'])
            )
        );
    });

     $this->get('get/{table}/{parameter}/{filter}', function ($req, $res, $args) {
        $tm = new TableModel(); 
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $tm->Get($args['table'],$args['parameter'],$args['filter'] )
            )
        );
    });
 
});