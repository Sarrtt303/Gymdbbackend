<?php
include_once __DIR__ . '/../controllers/booking.controller.php';

class BookingRouter {
    private $controller;

    public function __construct($db) {
        $this->controller = new BookingController($db);
    }

    public function handleRequest() {
        $request_method = $_SERVER["REQUEST_METHOD"];

        switch ($request_method) {
            case 'POST':
                $this->controller->createSession();
                break;
            default:
                new ApiError(405, "Method Not Allowed");
                break;
        }
    }
}
?>
