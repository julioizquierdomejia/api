<?php
use App\Model\LearningModel;

$app->group('/learning/', function () {
    
    $this->get('test', function ($req, $res, $args) {
        $this->logger->info("_Something interesting happened"); 
        return $res->getBody()
                   ->write('Hello Users');
    });
     

    $this->get('getAll/competitions', function ($req, $res, $args) {
        $lm = new LearningModel();
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $lm->GetAllcompetitions()
            )
        ); 
    });

     $this->get('getAll/capacitys', function ($req, $res, $args) {
        $lm = new LearningModel();
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $lm->GetAllCapacitys()
            )
        );
    });
    

    $this->get('getAll/indicators', function ($req, $res, $args) {
        $lm = new LearningModel();
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $lm->GetAllIndicators()
            )
        );
    });

    $this->get('get/competitions/{id}', function ($req, $res, $args) {
        $lm = new LearningModel();
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $lm->GetCompetitios($args['id'])
            )
        );
    });

    $this->get('get/capacitys/{id}', function ($req, $res, $args) {
        $lm = new LearningModel();
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $lm->GetCapacitys($args['id'])
            )
        );
    }); 

    $this->get('get/capacitys/byCompetition/{id_competitions}', function ($req, $res, $args) {
        $lm = new LearningModel();
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $lm->GetCapacitysByCompetition($args['id_competitions'])
            )
        );
    });

    $this->get('get/indicators/{id}', function ($req, $res, $args) {
        $lm = new LearningModel();
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $lm->GetIndicators($args['id'])
            )
        );
    });

    $this->get('get/indicators/byCapacitys/{id_capacitys}', function ($req, $res, $args) {
        $lm = new LearningModel();
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $lm->GetIndicatorsByCapacity($args['id_capacitys'])
            )
        );
    });  

     $this->get('getAll/sessions/byBook/{id_book}', function ($req, $res, $args) {
        $token_data = $req->getAttribute("decoded_token_data")["sub"];
        $lm = new LearningModel($token_data); 

        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $lm->GetSessionsByBook($args['id_book'])
            )
        );
    });  

     $this->get('getAll/evaluation/type', function ($req, $res, $args) {
        $token_data = $req->getAttribute("decoded_token_data")["sub"];
        $lm = new LearningModel($token_data);
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $lm->GetAllEvaluationType()
            )
        );
    });

    $this->get('getAll/scoredLetters', function ($req, $res, $args) {
        $token_data = $req->getAttribute("decoded_token_data")["sub"];
        $lm = new LearningModel($token_data);
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $lm->GetScoredLetters()
            )
        );
    }); 
    
    $this->get('getAll/evaluationRange', function ($req, $res, $args) {
        $token_data = $req->getAttribute("decoded_token_data")["sub"]; 
        $lm = new LearningModel($token_data);
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $lm->GetEvaluationRange()
            )
        );
    }); 

    $this->get('getAll/evaluation', function ($req, $res, $args) {
        $token_data = $req->getAttribute("decoded_token_data")["sub"]; 
        $lm = new LearningModel($token_data);
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $lm->GetAllEvaluation()
            )
        );
    }); 

    $this->get('getAll/evaluation/pending', function ($req, $res, $args) {
        $token_data = $req->getAttribute("decoded_token_data")["sub"]; 
        $lm = new LearningModel($token_data);
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $lm->GetAllEvaluationByStatus(1)
            )
        );
    }); 


    $this->get('getAll/evaluation/calificated', function ($req, $res, $args) {
        $token_data = $req->getAttribute("decoded_token_data")["sub"]; 
        $lm = new LearningModel($token_data);
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $lm->GetAllEvaluationByStatus(2)
            )
        );
    }); 

    $this->get('getAll/evaluation/pending/byClass/{code}', function ($req, $res, $args) {
        $token_data = $req->getAttribute("decoded_token_data")["sub"]; 
        $lm = new LearningModel($token_data); 
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $lm->GetEvaluationByClassByStatus($args['code'], 1)
            )
        );
    });  


    $this->get('getAll/evaluation/pending/byClass/detail/{code}', function ($req, $res, $args) {
        $token_data = $req->getAttribute("decoded_token_data")["sub"]; 
        $lm = new LearningModel($token_data);         
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $lm->GetEvaluationByClassByStatusDetail($args['code'], 1)
            )
        );
    }); 

    $this->get('getAll/evaluation/calificated/byClass/detail/{code}', function ($req, $res, $args) {
        $token_data = $req->getAttribute("decoded_token_data")["sub"]; 
        $lm = new LearningModel($token_data);  
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $lm->GetEvaluationByClassByStatusDetail($args['code'], 2)
            )
        );
    }); 


    $this->get('getAll/scores/byClass/{code}', function ($req, $res, $args) {
        $token_data = $req->getAttribute("decoded_token_data")["sub"]; 
        $lm = new LearningModel($token_data);
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $lm->GetAllScoresByClass($args['code'])
            )
        );
    }); 

    $this->get('getAll/scores/byClass/{code}/{id_unity}', function ($req, $res, $args) {
        $token_data = $req->getAttribute("decoded_token_data")["sub"]; 
        $lm = new LearningModel($token_data);
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $lm->GetAllScoresByClassByUnity($args['code'], $args['id_unity'])
            )
        );
    }); 
}); 