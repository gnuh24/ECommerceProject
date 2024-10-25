<?php
require_once __DIR__ . '/googleLoginConfig.php'; // Include the configuration file
require_once __DIR__ . '/../../../vendor/autoload.php'; // Include Composer's autoloader

// use Google_Service_Oauth2;


if (isset($_GET['code'])) {
    echo "<h1>Code:" . $_GET['code'] . "</h1>";

    try {
        // Fetch the access token
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']); // Pass it directly as a string

        if (is_null($token)) {
            throw new Exception("Error: Token is NULL");
        }
        echo "<h1>Trước khi isset token error</h1>";


        // Check if token contains an error
        if (isset($token['error'])) {
            throw new Exception("Error fetching access token: " . $token['error']);
        }
        echo "<h1>Sau khi isset token error</h1>";


        $client->setAccessToken($token['access_token']);

        // Now that we have the access token, we can fetch the user's information
        echo "<h1> Trước khi Goolge Service called </h1>";
        $oauth2 = new Google_Service_Oauth2($client);
        $userInfo = $oauth2->userinfo->get();
        echo "<h1> Sau khi Goolge Service called </h1>";

        // Display user information
        echo "<h2>Thông tin người dùng:</h2>";
        echo "<p><strong>Email:</strong> " . htmlspecialchars($userInfo->email) . "</p>";
        echo "<p><strong>Tên:</strong> " . htmlspecialchars($userInfo->name) . "</p>";
        echo "<p><strong>Hình đại diện:</strong></p>";
        echo "<img src='" . htmlspecialchars($userInfo->picture) . "' alt='User Picture' style='width: 100px; height: 100px;'><br>";
        echo "<p><strong>Địa chỉ:</strong> " . htmlspecialchars($userInfo->locale) . "</p>";
    } catch (Exception $e) {
        echo "An error occurred: " . $e->getMessage();
        exit;
    }
} else {
    echo "Authorization code not set.";
}
