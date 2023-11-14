<?php

function getWatchListData($accountId, $servername, $username, $password, $dbname) {
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM WatchList WHERE AccountID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $accountId);
    $stmt->execute();

    $result = $stmt->get_result();

    $watchListData = array();
    while ($row = $result->fetch_assoc()) {
        $watchListData[] = $row;
    }

    $stmt->close();
    $conn->close();

    return $watchListData;
}
function getWatchedListData($accountId, $servername, $username, $password, $dbname) {
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM WatchedList WHERE AccountID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $accountId);
    $stmt->execute();

    $result = $stmt->get_result();

    $watchListData = array();
    while ($row = $result->fetch_assoc()) {
        $watchListData[] = $row;
    }

    $stmt->close();
    $conn->close();

    return $watchListData;
}
function addToWatchList($accountId, $title, $year, $isMovie, $isTV) {
    global $servername, $username, $password, $dbname;

    $conn = new mysqli($servername, $username, $password, $dbname);

    $status = []; 

    if ($conn->connect_error) {
        $status['success'] = false;
        $status['message'] = "Connection failed: " . $conn->connect_error;
    } else {
        $sql = "INSERT INTO WatchList (AccountID, Name, Year, isMovie, isTV, TimeCreated, LastModified) VALUES (?, ?, ?, ?, ?, NOW(), NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isiii", $accountId, $title, $year, $isMovie, $isTV);

        if ($stmt->execute()) {
            $status['success'] = true;
            $status['message'] = "Added successfully.";
        } else {
            $status['success'] = false;
            $status['message'] = "Error: " . $stmt->error;
        }

        $stmt->close();
    }
    $conn->close();

    sendStatusToRabbitMQ(json_encode($status));

    return $status;
}

}
function addToWatchedList($accountId, $title, $year, $isMovie, $isTV) {
    global $servername, $username, $password, $dbname;
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $sql = "INSERT INTO WatchList (AccountID, Name, Year, isMovie, isTV, TimeCreated, LastModified) VALUES (?, ?, ?, ?, ?, NOW(), NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isiii", $accountId, $title, $year, $isMovie, $isTV);
    $stmt->execute();
    $stmt->close();
    $conn->close();
    if ($stmt->execute()) {
        $status['success'] = true;
        $status['message'] = "Added successfully.";
    } else {
        $status['success'] = false;
        $status['message'] = "Error: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();

sendStatusToRabbitMQ(json_encode($status));

return $status;
}


function searchMoviesAndTVShows($query) {
    $apiKey = 'ab0f6b84bbcbbd4066f4fee3eaba248c';
    $url = "https://api.themoviedb.org/3/search/multi?api_key={$apiKey}&query={$query}";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    $result = json_decode($response, true);
    if (isset($result['results'])) {
        return $result['results'];
    } else {
        return null;
    }
}

?>