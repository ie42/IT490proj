<?php
require_once('dbConnect.php'); // Include your database connection code

function getUserProfileData($sessionID) {
    $mysqli = dbConnect(); // Establish a database connection

    // Query to retrieve user data based on the session ID
    $query = "SELECT FavoriteDirector, FavoriteGenre, FavoriteMovie, FavoriteActor, Bio FROM Users WHERE SessionID = ?";
    
    if ($stmt = $mysqli->prepare($query)) {
        $stmt->bind_param("s", $sessionID);
        $stmt->execute();
        $stmt->bind_result($favoriteDirector, $favoriteGenre, $favoriteMovie, $favoriteActor, $bio);
        $stmt->fetch();
        $stmt->close();

        // Format the data into an associative array
        $userData = [
            "FavoriteDirector" => $favoriteDirector,
            "FavoriteGenre" => $favoriteGenre,
            "FavoriteMovie" => $favoriteMovie,
            "FavoriteActor" => $favoriteActor,
            "Bio" => $bio,
        ];

        return $userData;
    }

    return null; // Return null if user not found or an error occurred
}

// Get the session ID from cookies or from the request data
$sessionID = $_COOKIE['session_id'] ?? $_POST['session_id']; // Assuming the session ID is stored in a cookie or POST request data

if ($sessionID) {
    $userData = getUserProfileData($sessionID);

    if ($userData) {
        // Now you can access and display each piece of data individually
        echo json_encode($userData);
    } else {
        echo json_encode(["message" => "User not found or an error occurred."]);
    }
} else {
    echo json_encode(["message" => "Session ID not found in cookies or request data."]);
}

$mysqli->close(); // Close the database connection
?>
