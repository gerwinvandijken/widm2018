<?php

session_start();

$_SESSION['s_logged_n'] = '';
$_SESSION['s_name'] = '';
$_SESSION['s_username'] = '';

session_destroy();

header("Location: login.php");

?>