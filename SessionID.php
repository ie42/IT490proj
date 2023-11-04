<?php
// sessionValidate.php

if (isset($_POST['sessionid'])) {
    $sessionid = $_POST['sessionid'];

    // Now you can use the $sessionid variable in your PHP code
    // for session validation or other purposes.
    
    // Example: Validate the session by checking if the session ID is valid.
    if (validateSession($sessionid)) {
        // Session is valid, return a success response.
        $response = array('returnCode' => '1');
        echo json_encode($response);
    } else {
        // Session is invalid, return an error response.
        $response = array('returnCode' => '0');
        echo json_encode($response);
    }
} else {
    // Handle the case when sessionid is not provided in the request.
    // You can return an error response or take appropriate action.
    // For example, redirect the user to the login page.
}
?>
/* This CODE NEEDS TO BNE ADDED TO FRONT END FOR THIS TO WORK
function validateSession() {
    sessionData.type = "validate_session";
    console.log(sessionData);

    $.ajax({
        url: 'sessionValidate.php',
        method: 'POST',
        data: sessionData,
        dataType: 'json',
        success: function (result) {
            if (result.returnCode != "1") {
                document.cookie = "sessionid=;username=;path='/'";
                location.href = "index.html";
            }
        },
        error: function () {
            console.log("Error validating session");
        }
    });
}

function logout() {
    sessionData.type = "logout";

    $.ajax({
        url: 'sessionValidate.php',
        method: 'POST',
        data: sessionData,
        dataType: 'json',
        success: function (result) {
            if (result.returnCode === "
*/
// THIS IS FOR USING SESSION ID TO USE THE DATA FOR A USER DATA SUCH AS RECOMMENDATIONS OR PROFILE DATA
