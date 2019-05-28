<?php
use App\Model\LearningModel;

$app->group('/learning/', function () {
    
    $this->get('test', function ($req, $res, $args) {
        //$this->logger->info("_Something interesting happened"); 
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

    $this->get('getAll/categories/base', function ($req, $res) {
        $token_data = $req->getAttribute("decoded_token_data")["sub"];
        $lm = new LearningModel($token_data); 
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $lm->GetAllCategoriesBase()
            )
        );
    });

    $this->get('get/categories/base/{id}', function ($req, $res, $args) {
        $token_data = $req->getAttribute("decoded_token_data")["sub"];
        $lm = new LearningModel($token_data); 
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $lm->GetCategoriesBase($args['id'])
            )
        );
    });

    $this->get('getAll/categories/class', function ($req, $res) {
        $token_data = $req->getAttribute("decoded_token_data")["sub"];
        $lm = new LearningModel($token_data); 
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $lm->GetAllCategoriesClass()
            )
        );
    });

    $this->get('get/categories/class/{id}', function ($req, $res, $args) {
        $token_data = $req->getAttribute("decoded_token_data")["sub"];
        $lm = new LearningModel($token_data); 
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $lm->GetCategoriesClass($args['id'])
            )
        );
    });

    $this->get('get/categories/class/byClass/{class_code}', function ($req, $res, $args) {
        $token_data = $req->getAttribute("decoded_token_data")["sub"];
        $lm = new LearningModel($token_data); 
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $lm->GetCategoriesClassByClass($args['class_code'])
            )
        );
    });

    $this->get('getAll/periods/base', function ($req, $res) {
        $token_data = $req->getAttribute("decoded_token_data")["sub"];
        $lm = new LearningModel($token_data); 
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $lm->GetAllCategoriesBase()
            )
        );
    });

    $this->get('get/periods/base/{id}', function ($req, $res, $args) {
        $token_data = $req->getAttribute("decoded_token_data")["sub"];
        $lm = new LearningModel($token_data); 
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $lm->GetPeriodsBase($args['id'])
            )
        );
    });

    $this->get('getAll/periods/base/detail/byPeriod/{id}', function ($req, $res, $args) {
        $token_data = $req->getAttribute("decoded_token_data")["sub"];
        $lm = new LearningModel($token_data); 
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $lm->GetPeriodsBaseDetailByPeriod($args['id'])
            )
        );
    }); 

    $this->get('getAll/periods/class', function ($req, $res) {
        $token_data = $req->getAttribute("decoded_token_data")["sub"];
        $lm = new LearningModel($token_data); 
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $lm->GetAllPeriodsClass()
            )
        );
    });

    $this->get('get/periods/class/{id}', function ($req, $res, $args) {
        $token_data = $req->getAttribute("decoded_token_data")["sub"];
        $lm = new LearningModel($token_data); 
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $lm->GetPeriodsClass($args['id'])
            )
        );
    });



    $this->get('get/periods/class/byClass/{class_code}', function ($req, $res, $args) {
        $token_data = $req->getAttribute("decoded_token_data")["sub"];
        $lm = new LearningModel($token_data); 
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $lm->GetPeriodsClassByClass($args['class_code'])
            )
        );
    });

    $this->get('get/periods/class/detail/{id}', function ($req, $res, $args) {
        $token_data = $req->getAttribute("decoded_token_data")["sub"];
        $lm = new LearningModel($token_data); 
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $lm->GetPeriodsClassDetail($args['id'])
            )
        );
    }); 
    

    $this->get('getAll/periods/class/detail/byClass/{class_code}', function ($req, $res, $args) {
        $token_data = $req->getAttribute("decoded_token_data")["sub"];
        $lm = new LearningModel($token_data); 
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $lm->GetPeriodsClassDetailByClass($args['class_code'])
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

     $this->get('getAll/evaluation/pending/alumn/byClass/{code}', function ($req, $res, $args) {
        $token_data = $req->getAttribute("decoded_token_data")["sub"]; 
        $lm = new LearningModel($token_data); 
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $lm->GetEvaluationAlumnByClassByStatus($args['code'], 1)
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


    $this->get('getAll/scores/byClass/teacher/{code}', function ($req, $res, $args) {
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

    $this->get('getAll/scores/byClass/teacher/{code}/{id_unity}', function ($req, $res, $args) {
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

    $this->get('getAll/scores/byClass/alumn/{code}', function ($req, $res, $args) {
        $token_data = $req->getAttribute("decoded_token_data")["sub"]; 
        $lm = new LearningModel($token_data); 
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $lm->GetAllScoresByClassAlumn($args['code'])
            )
        );
    });  

    $this->get('getAll/scores/byClass/alumn/{code}/{id_unity}', function ($req, $res, $args) {
        $token_data = $req->getAttribute("decoded_token_data")["sub"]; 
        $lm = new LearningModel($token_data);
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $lm->GetAllScoresByClassAlumnByUnity($args['code'], $args['id_unity'])
            )
        );
    }); 
}); 