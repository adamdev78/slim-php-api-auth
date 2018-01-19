<?php


use \Firebase\JWT\JWT;
use \Slim\Middleware\HttpBasicAuthentication\AuthenticatorInterface;

//basic auth 

class AuthenticatorClass implements AuthenticatorInterface{

public function __invoke(array $arguments){
$user=$arguments['user'];
$password=$arguments['password'];

if($user=='ramy'&&$password=="1234"){

    return true;
}
else{

    return false;
}

}


}

$container = $app->getContainer();

$container["jwt"] = function ($container) {
    return new StdClass;
};

$app->add( new \Slim\Middleware\HttpBasicAuthentication(
[
'path'=>'/token','authenticator'=> new AuthenticatorClass()
]

));


$app->add(new \Slim\Middleware\JwtAuthentication([
	 "path" => ["/"],
    "passthrough" => ["/authenticate"],
    "secret" => "supersecretkeyyoushouldnotcommittogithub",
    "callback" => function ($request, $response, $arguments) use ($container) {
        $container["jwt"] =$arguments["decoded"] ;
        echo(json_encode($arguments["decoded"]));
    },
    "error" => function ($request, $response, $arguments) {
        $data["status"] = "error";
        $data["message"] = $arguments["message"];
        return $response
            ->withHeader("Content-Type", "application/json")
            ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    }
]));


$app->add(new \Tuupola\Middleware\CorsMiddleware([
    "origin" => ["*"],
    "methods" => ["GET", "POST", "PUT", "PATCH", "DELETE"],
    "headers.allow" => ["Accept", "Content-Type"],
    "headers.expose" => [],
    "credentials" => false,
    "cache" => 0,
]));








































//middleware
/*
$app->add(function($req,$res,$next){

$res->getBody()->write("befor");
$res=$next($req,$res);
$res->getBody()->write("after");

return $res;




});
*/



