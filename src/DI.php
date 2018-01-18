<?php
//get the app container
$containter=$app->getContainer();
//adding service to the contianer
$containter['JsonOperations']=function($containter){
$Json= new JsonService();
return $Json;

};

use \Firebase\JWT\JWT;

$containter['JWT']=function($containter){
$JWT= new \Firebase\JWT\JWT();
return $JWT;

};