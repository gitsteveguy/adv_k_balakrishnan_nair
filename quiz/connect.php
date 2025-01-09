<?php
$con = new mysqli('localhost', 'root', '', 'adv_k_balakrishnan_nair');
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}
