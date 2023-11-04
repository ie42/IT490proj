// Include your database connection file
include 'databaseconnect.inc';

// Fetch the user's favorite movie and genre from the database
$sessionid = $userid; // Replace with the actual user's ID or a user identification method
$sql = "SELECT favorite_movie, favorite_genre FROM user_preferences WHERE user_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$userID]);
$userPreferences = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$userPreferences) {
    echo "User preferences not found.";
} else {
    $favoriteMovie = $userPreferences['favorite_movie'];
    $favoriteGenre = $userPreferences['favorite_genre'];

    // Set up an API request to TMDb (The Movie Database)
    $apiKey = 'ab0f6b84bbcbbd4066f4fee3eaba248c'; // Replace with your TMDb API key
    $baseUrl = 'https://api.themoviedb.org/3';
    $endpoint = '/movie/top_rated';
    $url = $baseUrl . $endpoint . '?api_key=' . $apiKey . '&with_genres=' . $favoriteGenre;

    // Make the API request
    $response = file_get_contents($url);
    $data = json_decode($response, true);

    if ($data) {
        $topMovies = $data['results'];

        // Output the top 10 recommended movies
        $movieCount = 0;
        foreach ($topMovies as $movie) {
            if ($movieCount >= 10) {
                break; // Exit the loop once 10 movies have been displayed
            }
            echo $movie['title'] . ' (Genre: ' . $favoriteGenre . ', Rating: ' . $movie['vote_average'] . ')<br>';
            $movieCount++;
        }
    } else {
        echo "Error fetching movie recommendations from TMDb.";
    }
}
