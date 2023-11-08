<?php
require_once('dbConnect.php'); // Include your database connection code

function updateProfileSettings($sessionID, $data) {
    $mysqli = dbConnect(); // Establish a database connection

    // Update the user's settings
    $updateQuery = "UPDATE Users SET FavoriteDirector = ?, FavoriteGenre = ?, FavoriteMovie = ?, FavoriteActor = ?, Bio = ? WHERE SessionID = ?";
    
    if ($updateStmt = $mysqli->prepare($updateQuery)) {
        $updateStmt->bind_param("ssssss", $data['FavoriteDirector'], $data['FavoriteGenre'], $data['FavoriteMovie'], $data['FavoriteActor'], $data['Bio'], $sessionID);
        
        if ($updateStmt->execute()) {
            $updateStmt->close();
            return ["status" => 200, "message" => "Settings updated successfully"];
        }
        
        $updateStmt->close();
    }

    return ["status" => 500, "message" => "Failed to update settings"]; // Failed to update settings
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!empty($data) && isset($data['session_id'])) {
        $sessionID = $data['session_id'];
        $result = updateProfileSettings($sessionID, $data);
        http_response_code($result['status']);
        echo json_encode(["message" => $result['message']]);
        exit;
    }
    echo "Invalid or missing data."; // Handle the case of missing or invalid data
} else {
    echo "Invalid request method."; // Handle requests other than POST
}

$mysqli->close(); // Close the database connection
?>
