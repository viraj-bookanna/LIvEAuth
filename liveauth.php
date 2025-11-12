<?php
// this file requires an existing session

define('LIVE_AUTH_SERVER_URL', 'https://indika.rf.gd/liveauth');
function generate_token(){
    $ts = floor(time()/30);
    $secret = 'CsecR7&^da-{=scsI';
    return hash_hmac('md5', $ts, $secret);
}
function addQueryToUrl($url, $key, $value) {
    return $url . (strpos($url, '?') === false ? '?' : '&') . urlencode($key) . '=' . urlencode($value);
}
function live_auth_authenticate(){
    if(isset($_GET['live_auth_token'])){
        if($_GET['live_auth_token']==generate_token()){
            return true;
        }
        return false;
    }
    $current_page_url = ($_SERVER['HTTPS']=='on' ? "https":"http")."://{$_SERVER['SERVER_NAME']}{$_SERVER['REQUEST_URI']}";
    $url = addQueryToUrl(LIVE_AUTH_SERVER_URL, 'url', $current_page_url);
    http_response_code(302);
    header("Location: {$url}");
    exit;
}
