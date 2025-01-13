<?php
include_once __DIR__ . '/../controllers/user.controller.php';
include_once __DIR__ . "/../middlewares/requestHandler.php";

class User_router
{
    private $controller;

    public function __construct($db)
    {
        $this->controller = new User($db);
    }

    //adds route specific middlewares and calls the handle method in request handler class to call all the middlewares and controller 
    public function handleRequest()
    {
        $request_method = $_SERVER["REQUEST_METHOD"];
        switch ($request_method) {
            case 'GET':
                if (isset($_GET["get-user-by-id"])) {
                    include_once __DIR__ . "/../middlewares/auth.php";
                    $requestHandler = new RequestHandler([$this->controller, 'getUserByID']);
                    $requestHandler->addMiddlewares([new Auth()]);
                    $requestHandler->handle();
                } else if (isset($_GET["get-current-user"])) {
                    include_once __DIR__ . "/../middlewares/auth.php";
                    $requestHandler = new RequestHandler([$this->controller, 'getCurrentUser']);
                    $requestHandler->addMiddlewares([new Auth()]);
                    $requestHandler->handle();
                }
                break;
            case 'POST':
                if (isset($_GET['register'])) {
                    $this->controller->registerUser();
                } elseif (isset($_GET['login'])) {
                    $this->controller->login();
                } elseif (isset($_GET["logout"])) {
                    include_once __DIR__ . "/../middlewares/auth.php";
                    $requestHandler = new RequestHandler([$this->controller, 'logout']);
                    $requestHandler->addMiddlewares([new Auth()]);
                    $requestHandler->handle();
                }
                break;
            case 'PATCH':
                if (isset($_GET['user-details'])) {
                    include_once __DIR__ . "/../middlewares/auth.php";
                    $requestHandler = new RequestHandler([$this->controller, 'updateUserDetails']);
                    $requestHandler->addMiddlewares([new Auth()]);
                    $requestHandler->handle();
                } elseif (isset($_GET['user-password'])) {
                    include_once __DIR__ . "/../middlewares/auth.php";
                    $requestHandler = new RequestHandler([$this->controller, 'updateUserPassword']);
                    $requestHandler->addMiddlewares([new Auth()]);
                    $requestHandler->handle();
                } elseif (isset($_GET['update-role'])) {
                    include_once __DIR__ . "/../middlewares/auth.php";
                    $requestHandler = new RequestHandler([$this->controller, 'updateUserRole']);
                    $requestHandler->addMiddlewares([new Auth()]);
                    $requestHandler->handle();
                } elseif (isset($_GET["add-membership"])) {
                    include_once __DIR__ . "/../middlewares/auth.php";
                    $requestHandler = new RequestHandler([$this->controller, 'addUserMembership']);
                    $requestHandler->addMiddlewares([new Auth()]);
                    $requestHandler->handle();
                }
                break;
            case 'DELETE':
                include_once __DIR__ . "/../middlewares/auth.php";
                $requestHandler = new RequestHandler([$this->controller, 'deleteUser']);
                $requestHandler->addMiddlewares([new Auth()]);
                $requestHandler->handle();
                break;
            default:
                new ApiError(405, "Method Not Allowed");
                break;
        }
    }
}
