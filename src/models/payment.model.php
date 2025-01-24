<?php

class PaymentSchema
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function createPaymentsTable()
    {
        try {
            $query = "SHOW TABLES LIKE 'payments'";
            $stmt = $this->db->query($query);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!$result) {
                $query = "
                CREATE TABLE IF NOT EXISTS payments(
                    id int AUTO_INCREMENT NOT NULL,
                    uid int NOT NULL,
                    amount int NOT NULL,
                    payment_date DATE NOT NULL,
                    payment_method ENUM('Credit Card', 'Debit Card', 'Internet Banking', 'UPI') NOT NULL,
                    membership_id int NOT NULL,
                    PRIMARY KEY (id),
                    FOREIGN KEY (uid) REFERENCES users(id) ON DELETE CASCADE,
                    FOREIGN KEY (membership_id) REFERENCES memberships(id) ON DELETE CASCADE
                )";

                if ($this->db->exec($query)) {
                    echo "Payments table created successfully";
                }
            }
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }

    public function createPayment($uid, $amount, $payment_date, $payment_method, $membership_id)
    {
        try {
            $query = "
                INSERT INTO payments (uid, amount, payment_date, payment_method, membership_id)
                VALUES (:uid, :amount, :payment_date, :payment_method, :membership_id)";
            $stmt = $this->db->prepare($query);

            $stmt->bindParam(':uid', $uid);
            $stmt->bindParam(':amount', $amount);
            $stmt->bindParam(':payment_date', $payment_date);
            $stmt->bindParam(':payment_method', $payment_method);
            $stmt->bindParam(':membership_id', $membership_id);

            $stmt->execute();

            return $this->getPaymentById($this->db->lastInsertId());
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }

    public function getPaymentById($id)
    {
        try {
            $query = "SELECT * FROM payments WHERE id = :id";
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

    public function getAllPayments()
    {
        try {
            $query = "SELECT * FROM payments";
            $stmt = $this->db->query($query);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }

    public function updatePayment($id, $uid, $amount, $payment_date, $payment_method, $membership_id)
    {
        try {
            $query = "
                UPDATE payments
                SET uid = :uid, amount = :amount, payment_date = :payment_date, payment_method = :payment_method, membership_id = :membership_id
                WHERE id = :id";
            $stmt = $this->db->prepare($query);

            $stmt->bindParam(":uid", $uid);
            $stmt->bindParam(":amount", $amount);
            $stmt->bindParam(":payment_date", $payment_date);
            $stmt->bindParam(":payment_method", $payment_method);
            $stmt->bindParam(":membership_id", $membership_id);
            $stmt->bindParam(":id", $id);

            $stmt->execute();

            return $this->getPaymentById($id);
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }

    public function deletePayment($id)
    {
        try {
            $query = "DELETE FROM payments WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id", $id);

            return $stmt->execute();
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }

    public function getPaymentsByUserId($uid)
    {
        try {
            $query = "SELECT * FROM payments WHERE uid = :uid";
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
