<?php

class BookingSchema
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function createBookingsTable()
    {
        try {
            $query = "SHOW TABLES LIKE 'bookings'";
            $stmt = $this->db->query($query);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!$result) {
                $query = "
                CREATE TABLE IF NOT EXISTS bookings (
                    id int AUTO_INCREMENT NOT NULL,
                    uid int NOT NULL,
                    trainer_id int,
                    class_id int,
                    booking_date DATE NOT NULL,
                    status ENUM('confirmed', 'canceled') NOT NULL DEFAULT 'confirmed',
                    PRIMARY KEY (id),
                    FOREIGN KEY (uid) REFERENCES users(id) ON DELETE CASCADE,
                    FOREIGN KEY (trainer_id) REFERENCES trainers(id) ON DELETE CASCADE,
                    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE
                )";

                if ($this->db->exec($query)) {
                    echo "Bookings table created successfully";
                }
            }
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }

    public function createBooking($data)
    {
        ['uid' => $uid, 'trainer_id' => $trainer_id, 'class_id' => $class_id, 'booking_date' => $booking_date, 'status' => $status] = $data;

        try {
            $query = "
                INSERT INTO bookings (uid, trainer_id, class_id, booking_date, status)
                VALUES (:uid, :trainer_id, :class_id, :booking_date, :status)";
            $stmt = $this->db->prepare($query);

            $stmt->bindParam(':uid', $uid);
            $stmt->bindParam(':trainer_id', $trainer_id);
            $stmt->bindParam(':class_id', $class_id);
            $stmt->bindParam(':booking_date', $booking_date);
            $stmt->bindParam(':status', $status);

            $stmt->execute();

            return $this->getBookingById($this->db->lastInsertId());
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }

    public function getBookingById($id)
    {
        try {
            $query = "SELECT * FROM bookings WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }

    public function getAllBookings()
    {
        try {
            $query = "SELECT * FROM bookings";
            $stmt = $this->db->query($query);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }

    public function updateBooking($id, $data)
    {
        ['uid' => $uid, 'trainer_id' => $trainer_id, 'class_id' => $class_id, 'booking_date' => $booking_date, 'status' => $status] = $data;

        try {
            $query = "
                UPDATE bookings 
                SET uid = :uid, trainer_id = :trainer_id, class_id = :class_id, booking_date = :booking_date, status = :status
                WHERE id = :id";
            $stmt = $this->db->prepare($query);

            $stmt->bindParam(":uid", $uid);
            $stmt->bindParam(":trainer_id", $trainer_id);
            $stmt->bindParam(":class_id", $class_id);
            $stmt->bindParam(":booking_date", $booking_date);
            $stmt->bindParam(":status", $status);
            $stmt->bindParam(":id", $id);

            $stmt->execute();

            return $this->getBookingById($id);
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }

    public function deleteBooking($id)
    {
        try {
            $query = "DELETE FROM bookings WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id", $id);

            return $stmt->execute();
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }

    public function getBookingsByUser($uid)
    {
        try {
            $query = "SELECT * FROM bookings WHERE uid = :uid";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":uid", $uid);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }
}
