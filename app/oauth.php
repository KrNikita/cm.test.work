<?php
/*
 * This is code for microsoft OAuth autorization. Taken from official microsoft documentation.
 * Microsoft Graph SDK used.
 */
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require "../conf.php";
require "../vendor/autoload.php";

$provider = new \League\OAuth2\Client\Provider\GenericProvider([
    'clientId'                => CLIENT_ID,
    'clientSecret'            => CLIENT_SECRET,
    'redirectUri'             => REDIRECT_URI,
    'urlAuthorize'            => AUTHORITY_URL . AUTHORIZE_ENDPOINT,
    'urlAccessToken'          => AUTHORITY_URL . TOKEN_ENDPOINT,
    'urlResourceOwnerDetails' => '',
    'scopes'                  => SCOPES
]);

if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['code']) && !isset($_GET['error'])) {
    $authorizationUrl = $provider->getAuthorizationUrl();

    // The OAuth library automaticaly generates a state value that we can
    // validate later. We just save it for now.
    $_SESSION['state'] = $provider->getState();

    header('Location: ' . $authorizationUrl);
    exit();
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['error'])) {
    // Answer from the authentication service contains an error.
    printf('Something went wrong while authenticating: [%s] %s', $_GET['error'], $_GET['error_description']);
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['code'])) {
    // Validate the OAuth state parameter
    if (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['state'])) {
        unset($_SESSION['state']);
        exit('State value does not match the one initially sent');
    }

    // With the authorization code, we can retrieve access tokens and other data.
    try {
        // Get an access token using the authorization code grant
        $accessToken = $provider->getAccessToken('authorization_code', [
            'code'     => $_GET['code']
        ]);
        $_SESSION['access_token'] = $accessToken->getToken();

        // The id token is a JWT token that contains information about the user
        // It's a base64 coded string that has a header, payload and signature
        $idToken = $accessToken->getValues()['id_token'];
        $decodedAccessTokenPayload = base64_decode(
            explode('.', $idToken)[1]
        );
        $jsonAccessTokenPayload = json_decode($decodedAccessTokenPayload, true);

        // The following user properties are needed in the next page
        $_SESSION['preferred_username'] = $jsonAccessTokenPayload['preferred_username'];
        $_SESSION['given_name'] = $jsonAccessTokenPayload['name'];

        header('Location: index.php');
        exit();
    } catch (League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
        printf('Something went wrong, couldn\'t get tokens: %s', $e->getMessage());
    }
}