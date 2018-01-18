<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Firebase\JWT\JWT;

require '../vendor/autoload.php';
require '../src/config/db.php';

$conf=[
'settings'=>[
'displayErrorDetails'=>true,
],
];

$c= new \Slim\Container($conf);
$app = new \Slim\App($c);
 
 require '../src/middleware.php';
  require '../src/DI.php';
  $container["jwt"] = function ($container) {
    return new StdClass;
};
$app->get('/JWTToken',function($req,$res){
    
    $MyJWT=$this->JWT;
        $now = new DateTime();
        $future = new DateTime("now +1 minutes");
       // $server = $request->getServerParams();
        $payload = [
            "iat" => $now->getTimeStamp(),
            "exp" => $future->getTimeStamp(),
            "sub" =>"test for JWT",
        ];
        $secret = "supersecretkeyyoushouldnotcommittogithub";
        $token = $MyJWT->encode($payload, $secret, "HS512");
        $data["status"] = "ok";
        $data["token"] = $token;
         $res->withStatus(201)
            ->withHeader("Content-Type", "application/json")
            ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    
            return $res;
    
    
    
    });
// Register
$app->post('/register', function(Request $request, Response $response){
    $first_name = $request->getParam('name');
    $phone = $request->getParam('phone');
    $email = $request->getParam('email');
    $address = $request->getParam('address');

    $sql = "INSERT INTO users (name,email,password,adresse) VALUES
    (:name,:email,:phone,:address)";

    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();

        $stmt = $db->prepare($sql);

        $stmt->bindParam(':name', $first_name);
        $stmt->bindParam(':email',      $email);

        $stmt->bindParam(':phone',      $phone);
        $stmt->bindParam(':address',    $address);

        $stmt->execute();

        echo '{"notice": {"text": "Customer Added"}';

    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});


// Authenticate
$app->post('/authenticate', function(Request $request, Response $response){
    $name = $request->getParam('name');
    $password = $request->getParam('password');
    $sql = "SELECT * FROM users WHERE name = '$name'";
    
        try{
            // Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();
    
            $stmt = $db->query($sql);
            $customer = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($customer['password']==$password){
                $MyJWT=$this->JWT;
                $now = new DateTime();
                $future = new DateTime("now +20 minutes");
                $server = $request->getParams();
                $payload = [
                    "iat" => $now->getTimeStamp(),
                    "exp" => $future->getTimeStamp(),
                    "sub" =>$server,
                ];
                $secret = "supersecretkeyyoushouldnotcommittogithub";
                $token = $MyJWT->encode($payload, $secret, "HS512");
                $data["success"] = true;
                $data["token"] = $token;
                $data["user"]=$customer;
                $response->withStatus(201)
                    ->withHeader("Content-Type", "application/json")
                    ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
                    return $response;



            }
        } catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
    });












$app->get('/hello/{name}', function (Request $request, Response $response, array $args) {
    $name = $args['name'];
    $response->getBody()->write("Hello, $name");

    return $response;
});



$app->run();