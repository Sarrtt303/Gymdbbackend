<?php

class RequestHandler
{

    public $controller;
    public $middlewares = [];

    //adds the appropriate controller to the local variable
    public function __construct($controller)
    {
        $this->controller = $controller;
    }

    //method to add all required middlewares as an array in the order required for execution
    public function addMiddlewares($middlewares)
    {
        $this->middlewares = $middlewares;
    }

    //runs all middleware functions before passing the control to the appropriate controller
    public function handle()
    {
        try {
            $currentUser = []; //adds the logged in user's data by the auth middleware

            //stores the function reference of appropriate controller method to be called at the end in the $next
            $next = function ($currentUser) {
                return call_user_func($this->controller, $currentUser);
            };

            //stores the reference of handle method of each middleware in the next variable and passes it to the next middleware in line
            foreach (array_reverse($this->middlewares) as $middleware) {
                $next = function ($currentUser) use ($middleware, $next) {
                    return call_user_func([$middleware, "handle"], $next, $currentUser);
                };
            }

            //calls the first middleware in the list, generally auth middleware, 
            return $next($currentUser);
        } catch (Exception $th) {
            new ApiError($th->getCode(), $th->getMessage());
        }
    }
}
