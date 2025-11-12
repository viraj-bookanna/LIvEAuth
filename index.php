<?php
session_start();
date_default_timezone_set('Asia/Colombo');

function generate_token(){
    $ts = floor(time()/30);
    $secret = 'CsecR7&^da-{=scsI';
    return hash_hmac('md5', $ts, $secret);
}
function authenticate($username, $password){
    $users = [
        'guest' => '$2y$10$nSF8Fo4pKzVkp1YOtaOqWOGGthMTsMcSjU8V0GWwfuIzi3nlENyT6',
    ];
    if(password_verify($password, $users[$username])){
        return true;
    }
    return false;
}
function addQueryToUrl($url, $key, $value) {
    return $url . (strpos($url, '?') === false ? '?' : '&') . urlencode($key) . '=' . urlencode($value);
}
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents('php://input'), 1);
    if(json_last_error() === JSON_ERROR_NONE && authenticate($data['username'] ?? '', $data['password'] ?? '')){
        $_SESSION['live_auth'] = true;
        $token = generate_token();
        echo json_encode([
            'success' => true,
            'message' => 'Authentication success',
            'data' => [
                'url' => addQueryToUrl($data['url'] ?? '/', 'live_auth_token', $token),
                'token' => $token
            ]
        ]);
        exit;
    }
    echo json_encode(['success' => false, 'message' => 'Authentication failed']);
    exit;
}
elseif($_SESSION['live_auth'] ?? false){
    $url = addQueryToUrl($_GET['url'] ?? '/', 'live_auth_token', generate_token());
    http_response_code(302);
    header("Location: {$url}");
    exit;
}
readfile("liveauth.html");
