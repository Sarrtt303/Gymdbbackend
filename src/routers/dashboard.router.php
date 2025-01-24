<?php
include_once __DIR__ . '/../controllers/dashboard.controller.php';

class DashboardRouter
{
    private $controller;
    public function __construct($db)
    {
        $this->controller = new DashboardController($db);
    }

    public function handleRequest()
    {
        $request_method = $_SERVER["REQUEST_METHOD"];
        $tableName = $_GET['table'] ?? null;
        $id = $_GET['id'] ?? null;

        switch ($request_method) {
            case 'GET':
                if ($tableName) {
                    $this->controller->getTableData($tableName);
                }
                break;
            case 'POST':
                if ($tableName) {
                    $this->controller->insertData($tableName);
                }
                break;
            case 'PATCH':
                if ($tableName && $id) {
                    $this->controller->updateData($tableName, $id);
                }
                break;
            case 'DELETE':
                if ($tableName && $id) {
                    $this->controller->deleteData($tableName, $id);
                }
                break;
            default:
                new ApiError(405, "Method Not Allowed");
                break;
        }
    }
}