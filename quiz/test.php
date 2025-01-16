<?php
require_once("./header.php");
$hashed_password = password_hash('w421#@%&e2', PASSWORD_DEFAULT);
echo $hashed_password;
