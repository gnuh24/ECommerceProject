
<?php
require_once __DIR__ . '/../../../vendor/autoload.php'; // Adjust the path as necessary

$client = new Google_Client();
$client->setClientId('715538051843-gupli0th89obal4rve8rnuoi65inlccp.apps.googleusercontent.com'); // Google Client ID
$client->setClientSecret('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'); // Google Client Secret
$client->setRedirectUri('http://localhost/ECommerceProject/Views/MemberUI/Login/googleCallback.php'); // Redirect URL
$client->addScope('email');
$client->addScope('profile');

?>