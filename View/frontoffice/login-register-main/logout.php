<?php
session_start();
session_destroy();
header("Location: user/view/frontoffice/login-rgister-main/login.php");
?>