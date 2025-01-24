<?php
include_once __DIR__ . '/../utils/apiResponse.php';
include_once __DIR__ . "/../utils/apiError.php";
include_once __DIR__ . "/../models/dashboard.model.php";

class DashboardController
{
    private $dashboardSchema;

    public function __construct($db)
    {
        $this->dashboardSchema = new DashboardSchema($db);
        $this->initializeDatabase();
    }

    // Initialize database by creating all necessary tables
    private function initializeDatabase()
    {
        $this->dashboardSchema->createDashboardTables();
    }

    // Fetch data from a specific table
    public function getTableData($tableName)
    {
        try {
            $data = $this->dashboardSchema->getTableData($tableName);
            new ApiResponse(200, "Data retrieved successfully", $data); // 200 OK
        } catch (Exception $e) {
            new ApiError($e->getCode() ?: 500, $e->getMessage());
        }
    }

    // Insert data into a specific table
    public function insertData($tableName)
    {
        try {
            $data = json_decode(file_get_contents("php://input"), true);

            if (!empty($data)) {
                $insertedId = $this->dashboardSchema->insertData($tableName, $data);
                new ApiResponse(201, "Data inserted successfully", ['id' => $insertedId]); // 201 Created
            } else {
                throw new Exception("No data provided", 422); // 422 Unprocessable Entity
            }
        } catch (PDOException $e) {
            new ApiError(503, "Database error: " . $e->getMessage()); // 503 Service Unavailable
        } catch (Exception $e) {
            new ApiError($e->getCode() ?: 500, $e->getMessage());
        }
    }

    // Update data in a specific table
    public function updateData($tableName, $id)
    {
        try {
            $data = json_decode(file_get_contents("php://input"), true);

            if (!empty($data) && !empty($id)) {
                $this->dashboardSchema->updateData($tableName, $data, $id);
                new ApiResponse(200, "Data updated successfully"); // 200 OK
            } else {
                throw new Exception("Incomplete data provided", 422); // 422 Unprocessable Entity
            }
        } catch (PDOException $e) {
            new ApiError(503, "Database error: " . $e->getMessage()); // 503 Service Unavailable
        } catch (Exception $e) {
            new ApiError($e->getCode() ?: 500, $e->getMessage());
        }
    }

    // Delete data from a specific table
    public function deleteData($tableName, $id)
    {
        try {
            if (!empty($id)) {
                $this->dashboardSchema->deleteData($tableName, $id);
                new ApiResponse(200, "Data deleted successfully"); // 200 OK
            } else {
                throw new Exception("ID not provided", 422); // 422 Unprocessable Entity
            }
        } catch (PDOException $e) {
            new ApiError(503, "Database error: " . $e->getMessage()); // 503 Service Unavailable
        } catch (Exception $e) {
            new ApiError($e->getCode() ?: 500, $e->getMessage());
        }
    }
}
// GET: ?table=users
// POST: ?table=users
// PATCH: ?table=users&id=1
// DELETE: ?table=users&id=1