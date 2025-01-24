<?php
include_once __DIR__ . '/../models/booking.model.php';

class BookingController {
    private $model;

    public function __construct($db) {
        $this->model = new BookingModel($db);
    }

    // Create a session
    public function createSession() {
        // Get input data from the request
        $data = json_decode(file_get_contents("php://input"), true);

        // Validate input
        if (!isset($data['trainer_name']) || !isset($data['session_date']) || !isset($data['session_time'])) {
            new ApiError(400, "Missing required fields: trainer_name, session_date, and session_time");
            return;
        }

        $trainerName = $data['trainer_name'];
        $sessionDate = $data['session_date'];
        $sessionTime = $data['session_time'];

        // Call the model to insert the session
        try {
            $sessionId = $this->model->insertSession($trainerName, $sessionDate, $sessionTime);
            echo json_encode([
                "success" => true,
                "message" => "Session created successfully",
                "session_id" => $sessionId
            ]);
        } catch (Exception $e) {
            new ApiError(500, "Failed to create session: " . $e->getMessage());
        }
    }
}
?>
