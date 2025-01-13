<?php
//headers that will be sent to the client with the response
header("Content-Type: application/json; charset=UTF-8");  //response will be in json
header("Access-Control-Allow-Origin: https://crisscrosstamizh.in"); //change the url to the locally hosted page url that is sending the request
header("Access-Control-Allow-Headers: Content-Type, Authorization"); //requests with mentioned header are allowed
header("Access-Control-Allow-Methods: GET, POST, PATCH, DELETE, OPTIONS"); //mentioned http requests are allowed
header("Access-Control-Allow-Credentials: true"); //allows cross origin requests to send cookies to the server

include_once __DIR__ . "/config/database.php";
include_once __DIR__ . "/../vendor/autoload.php";

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . "/../");
$dotenv->load();


$db = new Database;  //database instance
$conn = $db->getConnection(); //databse connection instance

$request_method = $_SERVER['REQUEST_METHOD'];
$url = parse_url($_SERVER["REQUEST_URI"]);
$path = trim(str_replace('gymManagementSystem/api/v1', '', $url["path"]), '/');

//route handler, distributes requests to appropriate controller according to the path of the uri
switch ($path) {
    case 'users':
        include_once __DIR__ . "/routers/user.router.php";
        $user = new User_router($conn);
        $user->handleRequest();
        break;

    case 'memberships':
        include_once __DIR__ . '/controllers/membership.controller.php';
        $membership = new Membership($conn);
        $membership->handleRequest($request_method);
        break;

    case 'trainers':
        include_once __DIR__ . '/controllers/trainer.controller.php';
        $trainer = new Trainer($conn);
        $trainer->handleRequest($request_method);
        break;

    case 'classes':
        include_once __DIR__ . '/controllers/class.controller.php';
        break;

    case 'bookings':
        include_once __DIR__ . '/controllers/booking.controller.php';
        break;

    case 'payments':
        include_once __DIR__ . '/controllers/payment.controller.php';
        break;

    case 'attendence':
        include_once __DIR__ . '/controllers/attendence.controller.php';
        break;

    default:
        header("HTTP/1.0 405 Method Not Allowed");
        break;
}
