<?php

function getVal($key) {
    return array_key_exists($key, $_GET)
        ? $_GET[$key]
        : '';
}

function isValidRequest() {
    return getVal('msg')
        && !(getVal('msg') == 'Script error.' && getVal('line') == '0')
        && array_key_exists('HTTP_USER_AGENT', $_SERVER);
}

if (!isValidRequest()) {
    exit;
}

$data = array();
array_push($data,
    time(),
    getVal('msg'),
    getVal('url'),
    getVal('line'),
    getVal('col'),
    getVal('href'),
    $_SERVER['HTTP_USER_AGENT']
);

$line = json_encode($data) . "\n";

# NOTE: Adjust this path and file permissions
file_put_contents('/var/log/window.onerror/all.log', $line, FILE_APPEND | LOCK_EX);

?>
