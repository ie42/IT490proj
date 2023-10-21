<?php

// Connect to your MySQL database. Replace these values with your database credentials.
require = ('dbConnection.inc');
// Check for database connection errors
if ($mysqli->connect_error) {
    die("Database connection failed: " . $mysqli->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['username']) || !isset($data['password'])) {
        http_response_code(400);
        echo json_encode(["message" => "Missing username or password"]);
        exit;
    }

    $username = $data['username'];
    $password = $data['password'];

    // Query the database to retrieve the hashed password for the given username
    $query = "SELECT password FROM users WHERE username = ?";
    
    if ($stmt = $mysqli->prepare($query)) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($storedPassword);
        $stmt->fetch();
        $stmt->close();
    }

    if (empty($storedPassword) || !password_verify($password, $storedPassword)) {
        http_response_code(401);
        echo json_encode(["message" => "Invalid credentials"]);
        exit;
    }

    // Authentication successful - generate a JWT token
    $tokenPayload = [
        "username" => $username,
        "exp" => time() + 3600  // Token expires in 1 hour
    ];
    $token = jwt_encode($tokenPayload, $secretKey);

    http_response_code(200);
    echo json_encode(["token" => $token]);
}

function jwt_encode($data, $key) {
    $header = json_encode(["alg" => "HS256", "typ" => "JWT"]);
    $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
    $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode($data)));
    $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $key, true);
    $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
    return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
}

// Close the database connection when done
$mysqli->close();
