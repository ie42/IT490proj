<?php

// Include code for your database connection here from the DbConnect.php
include 'DbConnect.php';


$servername = "localhost";//servername
$username = "root"; //username
$password = ""; //the password
$dbname = "RateScore"; //database name

$connection = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Function for calculating rate score and leaderboard
function calculateRateScoreAndLeaderboard($connection) {
    // Your MySQL query
    $query = "SELECT Profiles.RateScore, Profiles.AccountID, Accounts.Username 
              FROM Profiles 
              CROSS JOIN Accounts 
              WHERE Accounts.AccountID = Profiles.AccountID 
              ORDER BY RateScore DESC 
              LIMIT 0, 10";

    // Execute the query
    $result = mysqli_query($connection, $query);

    // Check for errors
    if (!$result) {
        die('Error in query: ' . mysqli_error($connection));
    }

    // Fetch the results
    $leaderboard = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $leaderboard[] = $row;
    }

    // Close the result set
    mysqli_free_result($result);

    // Return the leaderboard
    return $leaderboard;
}

// Example usage
$topUsers = calculateRateScoreAndLeaderboard($connection);

// Close the connection
$connection->close();

// Print or use $topUsers as needed
print_r($topUsers);

?>
