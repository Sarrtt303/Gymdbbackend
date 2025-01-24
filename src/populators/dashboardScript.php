<?php
echo "PDO Drivers: ";
print_r(PDO::getAvailableDrivers());



include_once __DIR__ . '/../config/database.php';// Ensure the database class is included
require_once __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();


class DataInsertion {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function insertAttendanceData($data) {
        try {
            $stmt = $this->conn->prepare("INSERT INTO attendance (date, time, trainer, session) VALUES (:date, :time, :trainer, :session)");
            
            foreach ($data as $entry) {
                $stmt->execute([
                    ':date' => $entry['date'],
                    ':time' => $entry['time'],
                    ':trainer' => $entry['trainer'],
                    ':session' => $entry['session']
                ]);
            }
            echo "Attendance data inserted successfully.\n";
        } catch (PDOException $e) {
            echo "Error inserting attendance data: " . $e->getMessage() . "\n";
        }
    }

    public function insertWorkoutData($data) {
        try {
            $stmt = $this->conn->prepare("INSERT INTO workout (name, details) VALUES (:name, :details)");
            
            foreach ($data as $workout) {
                $stmt->execute([
                    ':name' => $workout['name'],
                    ':details' => $workout['details']
                ]);
            }
            echo "Workout data inserted successfully.\n";
        } catch (PDOException $e) {
            echo "Error inserting workout data: " . $e->getMessage() . "\n";
        }
    }

    public function insertClassesData($data) {
        try {
            $stmt = $this->conn->prepare("INSERT INTO classes (name, date) VALUES (:name, :date)");
            
            foreach ($data as $class) {
                $stmt->execute([
                    ':name' => $class['name'],
                    ':date' => $class['date']
                ]);
            }
            echo "Classes data inserted successfully.\n";
        } catch (PDOException $e) {
            echo "Error inserting classes data: " . $e->getMessage() . "\n";
        }
    }

    public function insertGoalsData($data) {
        try {
            $stmt = $this->conn->prepare("INSERT INTO goals (description, deadline) VALUES (:description, :deadline)");
            
            foreach ($data as $goal) {
                $stmt->execute([
                    ':description' => $goal['description'],
                    ':deadline' => $goal['deadline']
                ]);
            }
            echo "Goals data inserted successfully.\n";
        } catch (PDOException $e) {
            echo "Error inserting goals data: " . $e->getMessage() . "\n";
        }
    }

    public function insertDietData($data) {
        try {
            $stmt = $this->conn->prepare("INSERT INTO diet (meal, details) VALUES (:meal, :details)");
            
            foreach ($data as $dietEntry) {
                $stmt->execute([
                    ':meal' => $dietEntry['meal'],
                    ':details' => $dietEntry['details']
                ]);
            }
            echo "Diet data inserted successfully.\n";
        } catch (PDOException $e) {
            echo "Error inserting diet data: " . $e->getMessage() . "\n";
        }
    }

    public function insertTrainerData($data) {
        try {
            $stmt = $this->conn->prepare("INSERT INTO trainer (name, specialization) VALUES (:name, :specialization)");
            
            $stmt->execute([
                ':name' => $data['name'],
                ':specialization' => $data['specialization']
            ]);
            
            echo "Trainer data inserted successfully.\n";
        } catch (PDOException $e) {
            echo "Error inserting trainer data: " . $e->getMessage() . "\n";
        }
    }

    public function insertAllData($dummyData) {
        $this->insertAttendanceData($dummyData['attendance']);
        $this->insertWorkoutData($dummyData['workout']);
        $this->insertClassesData($dummyData['classes']);
        $this->insertGoalsData($dummyData['goals']);
        $this->insertDietData($dummyData['diet']);
        $this->insertTrainerData($dummyData['trainer']);
    }
}

// Usage example
$dataInsertion = new DataInsertion();
$dummyData = [
    'attendance' => [
        ['date' => '2025-01-15', 'time' => '09:30 AM', 'trainer' => 'John Smith', 'session' => 'Weight Training'],
        ['date' => '2025-01-17', 'time' => '02:00 PM', 'trainer' => 'Sarah Wilson', 'session' => 'HIIT'],
        ['date' => '2025-01-19', 'time' => '11:00 AM', 'trainer' => 'John Smith', 'session' => 'Strength Training'],
        ['date' => '2025-01-20', 'time' => '04:30 PM', 'trainer' => 'Mike Johnson', 'session' => 'Cardio'],
        ['date' => '2025-01-22', 'time' => '07:00 AM', 'trainer' => 'Lisa Chen', 'session' => 'Yoga'],
        ['date' => '2025-01-24', 'time' => '06:00 PM', 'trainer' => 'David Rodriguez', 'session' => 'CrossFit'],
        ['date' => '2025-01-26', 'time' => '10:00 AM', 'trainer' => 'Emily Taylor', 'session' => 'Pilates'],
        ['date' => '2025-01-28', 'time' => '03:30 PM', 'trainer' => 'Alex Wong', 'session' => 'Swimming']
    ],
    'workout' => [
        ['name' => 'Bench Press', 'details' => '4 sets x 12 reps'],
        ['name' => 'Squats', 'details' => '3 sets x 15 reps'],
        ['name' => 'Deadlifts', 'details' => '5 sets x 5 reps'],
        ['name' => 'Overhead Press', 'details' => '3 sets x 10 reps'],
        ['name' => 'Pull-ups', 'details' => '4 sets x 8 reps'],
        ['name' => 'Dumbbell Rows', 'details' => '3 sets x 12 reps'],
        ['name' => 'Lunges', 'details' => '3 sets x 15 reps per leg'],
        ['name' => 'Plank', 'details' => '3 sets x 60 seconds']
    ],
    'classes' => [
        ['name' => 'Yoga Flow', 'date' => '2025-01-21'],
        ['name' => 'HIIT Training', 'date' => '2025-01-23'],
        ['name' => 'Spin Class', 'date' => '2025-01-25'],
        ['name' => 'Zumba', 'date' => '2025-01-27'],
        ['name' => 'Boxing Fitness', 'date' => '2025-01-29'],
        ['name' => 'Pilates Fundamentals', 'date' => '2025-01-31']
    ],
    'goals' => [
        ['description' => 'Increase bench press by 20kg', 'deadline' => '2025-03-01'],
        ['description' => 'Run 5km under 25 minutes', 'deadline' => '2025-02-15'],
        ['description' => 'Complete 10 unassisted pull-ups', 'deadline' => '2025-04-15'],
        ['description' => 'Reduce body fat percentage to 15%', 'deadline' => '2025-05-30'],
        ['description' => 'Master yoga headstand pose', 'deadline' => '2025-03-15']
    ],
    'diet' => [
        ['meal' => 'Breakfast', 'details' => 'Oatmeal with protein shake - 450 cal'],
        ['meal' => 'Lunch', 'details' => 'Grilled chicken salad - 550 cal'],
        ['meal' => 'Dinner', 'details' => 'Salmon with quinoa - 650 cal'],
        ['meal' => 'Pre-Workout Snack', 'details' => 'Banana with almond butter - 250 cal'],
        ['meal' => 'Post-Workout Protein', 'details' => 'Whey protein smoothie - 300 cal'],
        ['meal' => 'Evening Snack', 'details' => 'Greek yogurt with berries - 200 cal']
    ],
    'trainer' => [
        'name' => 'John Smith',
        'specialization' => 'Strength & Conditioning'
    ]
];
$dataInsertion->insertAllData($dummyData);