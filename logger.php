<?php

if (!$_POST['msg']) {
  exit;
}

if ($_POST['msg'] == 'Script error.' && $_POST['line'] == '0') {
  exit;
}

$log = array();
$log[] = $_POST['href'];
$log[] = $_POST['url'];
$log[] = $_POST['msg'];
$log[] = $_POST['line'];
$log[] = $_POST['col'];
$log[] = $_SERVER['HTTP_USER_AGENT'];
$log[] = time();

$line = json_encode($log) . "\n";

# NOTE: Adjust this path and file permissions
file_put_contents('/var/log/window.onerror/all.log', $line, FILE_APPEND | LOCK_EX);

?>
