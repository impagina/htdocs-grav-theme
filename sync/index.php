<?php

// https://gist.github.com/milo/daed6e958ea534e4eba3 / github-webhook-handler.php

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

$hook_secret = file_get_contents('secret.txt');

if (!isset($_SERVER['HTTP_X_HUB_SIGNATURE'])) {
    throw new \Exception("HTTP header 'X-Hub-Signature' is missing.");
}

list($algo, $hash) = explode('=', $_SERVER['HTTP_X_HUB_SIGNATURE'], 2) + array('', '');

$raw_post = file_get_contents('php://input');
if (hash_equals($hash, hash_hmac($algo, $raw_post, $hook_secret))) {
    throw new \Exception('Hook secret does not match.');
}

$json = $rawPost ?: file_get_contents('php://input');

$payload = json_decode($json);


switch (strtolower($_SERVER['HTTP_X_GITHUB_EVENT'])) {
    case 'ping':
        echo 'pong';
        break;
    case 'push':
        shell_exec('cd  .. && git reset --hard HEAD && git pull' );
    case 'create':
        break;
    default:
        header('HTTP/1.0 404 Not Found');
        echo "Event:$_SERVER[HTTP_X_GITHUB_EVENT] Payload:\n";
        print_r($payload); # For debug only. Can be found in GitHub hook log.
        die();
}
