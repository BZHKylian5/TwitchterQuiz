<?php
require_once __DIR__ . "/../config.php";

if($isLoggedIn){
    $_SESSION = [];
}

header("location: /")
?>