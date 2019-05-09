<?php

// Application middleware
$container = $app->getContainer(); 
$settings = $container->get('settings');
// e.g: $app->add(new \Slim\Csrf\Guard);
//verificar https y json
$app->add(function ($request, $response, $next) {
    $host = $request->getUri()->getHost();
    $cType = $request->getContentType();

    if (strpos('application/json', $cType) !== false) {
        if($host == 'ebiolibros.com' || $host == 'www.ebiolibros.com' )
        {
            if ($request->getUri()->getScheme() !== 'https') { 
            $data = array('response' => 'false', 'msg' => 'Strict use HTTPS Protocol');
            return $response
                ->withHeader("Content-Type", "application/json")
                ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

            }  
        }  
    }
    else
    {
        $data = array('response' => 'false', 'msg' => 'Strict use Content-Type application/json');
        return $response
                ->withHeader("Content-Type", "application/json")
                ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
 
    }
    return $next($request, $response);    
});
 
$container['errorHandler'] = function ($container) {
    return function ($request, $response, $exception) use ($container) {
        return $response->withStatus(500)
            ->withHeader("Content-Type", "application/json")
            ->write( json_encode( array("response" => false, "msg" => $exception->getMessage() ), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    };
};


$path = [
    "/secure/test", 
    "/secure/changeAmb",
    "/secure/baseAmb",
    "/learning/getAll/class/",
    "/learning/get/class/",
    "/learning/getAll/scoredLetters",
    "/learning/get/alumns/byClass/",
    "/learning/getAll/evaluationRange",
    "/learning/getAll/evaluation",
    "/learning/getAll/evaluation/", 
    "/learning/getAll/scores/", 
    "/learning/getAll/sessions/", 
    "/user/getJoinStatus/",
    "/user/alumn/",
    "/user/teacher/",
    "/user/get/",
    "/user/set/",
    "/resource",
    "/resource/",
    "/notification/",
    "/class",
    "/class/",
    "/book/registered",
    "/book/checkCode",
    "/pe/nav_private",
    "/pe/resources",
    "/pe/resources/",
    "/pe/resources/byBook/",
    "/pe/generateBIC",
    "/pe/bookgroup/getAll",
    "/pe/usertype/getAll",
    "/pe/codesBIC/generate",
    "/pe/codesBIC/get/",
    "/pe/codesBIC/enabledchange",
    "/pe/codesBIC/delete/"
];

$app->add(new \Tuupola\Middleware\JwtAuthentication([
    "path" => $path, 
    "attribute" => "decoded_token_data",
    "secret" => $settings["jwt"]["secret"],
    "algorithm" => ["HS256"],
    "relaxed" => ["localhost", "headers", "ebiolibros.com"],
    "error" => function ($response, $arguments) {
        $data["status"] = "error";
        $data["message"] = $arguments["message"];  
        $data["response"] =  false;
        return $response
            ->withHeader("Content-Type", "application/json")
            ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    }
]));

