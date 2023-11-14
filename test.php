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

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "INSERT INTO WatchList (AccountID, Name, Year, isMovie, isTV, TimeCreated, LastModified) VALUES (?, ?, ?, ?, ?, NOW(), NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isiii", $accountId, $title, $year, $isMovie, $isTV);
    $stmt->execute();

    $stmt->close();
    $conn->close();
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
}


function searchMoviesAndTVShows($query) {
    $apiKey = 'YOUR_TMDB_API_KEY';
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




function handleRabbitMQMessages($configFile) {
    $config = parse_ini_file($configFile);
    $host = $config['host'];
    $port = $config['port'];
    $user = $config['user'];
    $pass = $config['password'];
    $exchange = $config['exchange'];
    $queue = $config['queue'];
    $consumerTag = $config['consumerTag'];
    $connection = new PhpAmqpLib\Connection\AMQPStreamConnection($host, $port, $user, $pass);
    $channel = $connection->channel();
    $channel->exchange_declare($exchange, 'direct', false, true, false);
    $channel->queue_declare($queue, false, true, false, false);
    $channel->queue_bind($queue, $exchange);
    $callback = function ($msg) {
        $accountId = json_decode($msg->body, true)['accountId'];
        $config = parse_ini_file('dbconnection.inc');
        $watchListData = getWatchListData($accountId, $config['servername'], $config['username'], $config['password'], $config['dbname']);
        echo json_encode($watchListData);
        $msg->ack(); 
    };
    $channel->basic_consume($queue, $consumerTag, false, false, false, false, $callback);
    while ($channel->is_consuming()) {
        $channel->wait();
    }
    $channel->close();
    $connection->close();
}
handleRabbitMQMessages('testRabbitMQ.ini');

?>