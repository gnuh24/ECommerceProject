<?php
require_once __DIR__ . '/../../../vendor/autoload.php'; // Correct concatenation

// Create Google Client
$client = new Google_Client();
$client->setClientId('YOUR_GOOGLE_CLIENT_ID'); // Google Client ID
$client->setClientSecret('YOUR_GOOGLE_CLIENT_SECRET'); // Google Client Secret
$client->setRedirectUri('http://localhost/your_project_folder/google-callback.php'); // Redirect URL
$client->addScope('email');
$client->addScope('profile');

// Get Google Auth URL
$loginUrl = $client->createAuthUrl();

// Display Google Login Button
echo "<a href='$loginUrl'><button>Login with Google</button></a>";
