<?php

/** RZM PHP Framework **/

# Connect to database
$mysqli = new mysqli(HOST, USER, PASSWORD, DATABASE);

if (!$mysqli->set_charset('utf8mb4')) {
    printf("Error loading character set utf8mb4: %s\n", $mysqli->error);
    exit;
}

?>