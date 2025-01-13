<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Auth
{
    // Verifies JWT
    public function handle(Closure $next)
    {
        try {
            if (!isset($_COOKIE["access_token"])) {
                throw new Exception("Unauthorized request: No token provided", 401);
            }

            //decodes the token and finds the user from the db by the id available in the decoded token
            $token = $_COOKIE["access_token"];
            $decodedToken = JWT::decode($token, new Key($_ENV["ACCESS_TOKEN_SECRET"], "HS256"));
            if ($decodedToken) {
                $data = (array) $decodedToken->data; // Ensure correct access to the token data
                $db = new Database();
                $conn = $db->getConnection();
                $userSchema = new UserSchema($conn);
                $user = $userSchema->read("id", $data["id"]); // Ensure correct method usage

                if ($user) {
                    $fieldsToRemove = ["password", "access_token"];

                    foreach ($fieldsToRemove as $field) {
                        if (isset($user[$field])) {
                            unset($user[$field]);
                        }
                    }
                    //calls the next middleware in the line and passes the controll to the next method in the line if the user exists
                    $next($user);
                } else {
                    throw new Exception("Invalid or Expired access token", 401);
                }
            } else {
                throw new Exception("Error decoding access token", 500);
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 500);
        }
    }
}
