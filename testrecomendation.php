<?php
// Include your database connection file
include 'databaseconnect.inc';

function fetchUserProfile($sessionID) {
    // Fetch the user's favorite movie and genre from the database
    $sql = "SELECT Profiles.FavoriteMovie, Profiles.FavoriteGenre, Profiles.FavoriteActor, Profiles.FavoriteDirector FROM Profiles WHERE user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$sessionID]); // Use the session ID to identify the user
    $userPreferences = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$userPreferences) {
        echo "User preferences not found.";
    } else {
        $favoriteMovie = $userPreferences['FavoriteMovie'];
        $favoriteGenre = $userPreferences['FavoriteGenre'];
        $favoriteActor = $userPreferences['FavoriteActor'];
        $favoriteDirector = $userPreferences['FavoriteDirector'];

        // Set up API requests based on user preferences
        $apiKey = 'ab0f6b84bbcbbd4066f4fee3eaba248c'; // Replace with your TMDb API key
        $baseUrl = 'https://api.themoviedb.org/3';

        // Make separate API requests for genre-based, actor/director-based, and all three criteria-based recommendations
        $genreTopMovies = getTopMovies($baseUrl, '/movie/top_rated', $apiKey, $favoriteGenre);
        $actorDirectorTopMovies = getTopMovies($baseUrl, '/discover/movie', $apiKey, $favoriteActor, $favoriteDirector);
        $criteriaTopMovies = getTopMovies($baseUrl, '/discover/movie', $apiKey, $favoriteActor, $favoriteMovie, $favoriteDirector);

        // Display the top 10 recommended movies for each category
        displayRecommendedMovies($genreTopMovies, 'Genre');
        displayRecommendedMovies($actorDirectorTopMovies, 'Actor and Director');
        displayRecommendedMovies($criteriaTopMovies, 'Actor, Movie, and Director');
    }
}

function getTopMovies($baseUrl, $endpoint, $apiKey, $criteria1, $criteria2 = null, $criteria3 = null) {
    // Set up an API request
    $url = $baseUrl . $endpoint . '?api_key=' . $apiKey . '&with_genres=' . $criteria1;
    if ($criteria2) {
        $url .= '&with_cast=' . $criteria2 . '&with_crew=' . $criteria3;
    }

    // Make the API request
    $response = file_get_contents($url);
    $data = json_decode($response, true);
    return $data;
}

function displayRecommendedMovies($movieData, $source) {
    if ($movieData && isset($movieData['results'])) {
        $topMovies = $movieData['results'];

        // Output the top 10 recommended movies
        $movieCount = 0;
        echo "<h3>Top 10 Recommended Movies Based on $source</h3>";
        foreach ($topMovies as $movie) {
            if ($movieCount >= 10) {
                break; // Exit the loop once 10 movies have been displayed
            }
            echo $movie['title'] . ' (Rating: ' . $movie['vote_average'] . ')<br>';
            $movieCount++;
        }
    } else {
        echo "Error fetching movie recommendations based on $source.";
    }
}

function processRequest() {
    // Get the session ID from cookies or from the request data
    $sessionID = $_COOKIE['session_id'] ?? $_POST['session_id']; // Assuming the session ID is stored in a cookie or POST request data

    if ($sessionID) {
        fetchUserProfile($sessionID);
    } else {
        echo "Session ID not found in cookies or request data."; // Handle the case where the session ID is not found
    }
}

processRequest();
?>
