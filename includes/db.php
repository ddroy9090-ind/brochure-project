<?php

$DB_HOST = 'localhost';
$DB_USER = 'root';        // Hostinger par yeh alag hoga (e.g. 'u123456_user')
$DB_PASS = '';            // Hostinger par yeh apna password
$DB_NAME = 'hh_cms';

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}
$conn->set_charset('utf8mb4');
