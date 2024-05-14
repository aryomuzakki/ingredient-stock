<?php
    if (!isset($servername)) {
        include_once 'connection/config.php';     
    }

    function isLocalhost($whitelist = ['127.0.0.1', '::1']) {
        return in_array($_SERVER['REMOTE_ADDR'], $whitelist);
    }

    $mysqli = new mysqli($db_host,$db_username,$db_password,$db_name) or die($mysqli->error);
?>
