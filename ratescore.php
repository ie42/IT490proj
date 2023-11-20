<?php
include 'DbConnect.php';

$connection = new mysqli($servername, $username, $password, $dbname);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

function calculateRateScoreAndLeaderboard($connection) {
    // Your MySQL query
    $query = "SELECT Profiles.RateScore, Profiles.AccountID, Accounts.Username 
              FROM Profiles 
              CROSS JOIN Accounts 
              WHERE Accounts.AccountID = Profiles.AccountID 
              ORDER BY RateScore DESC 
              LIMIT 0, 10";
    
    $result = mysqli_query($connection, $query);

    if (!$result) {
        die('Error in query: ' . mysqli_error($connection));
    }
    $leaderboard = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $leaderboard[] = $row;
    }
    mysqli_free_result($result);
    
    return $leaderboard;
}

$topUsers = calculateRateScoreAndLeaderboard($connection);

$connection->close();

print_r($topUsers);

?>
