<?php
require_once('dbConnect.php'); // Include your database connection code

function updateProfileSettings($sessionID, $data) {
    $mysqli = dbConnect(); // Establish a database connection

    // Ensure that the user associated with the session ID exists
    $checkQuery = "SELECT UserID FROM Profiles WHERE SessionID = ?";
    if ($checkStmt = $mysqli->prepare($checkQuery)) {
        $checkStmt->bind_param("s", $sessionID);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows === 0) {
            $checkStmt->close();
            return ["status" => 404, "message" => "User not found"]; // User not found
        }

        $checkStmt->close();
    }

    // Update the user's settings
    $updateQuery = "UPDATE Profiles SET FavoriteDirector = ?, FavoriteGenre = ?, FavoriteMovie = ?, FavoriteActor = ?, Bio = ? WHERE SessionID = ?";
    
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

// Get the session ID from cookies
$sessionID = $_COOKIE['session_id']; // Assuming the session ID is stored in a cookie

if ($sessionID) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!empty($data)) {
            $result = updateProfileSettings($sessionID, $data);
            http_response_code($result['status']);
            echo json_encode(["message" => $result['message']]);
            exit;
        }
    }
    echo "Invalid or missing data."; // Handle the case of missing or invalid data
} else {
    echo "Session ID not found in cookies."; // Handle the case where the session ID is not found
}

$mysqli->close(); // Close the database connection
?>
