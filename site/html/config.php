<?php
session_start();
require_once __DIR__ . "/../.SECURE/config.php";

$isLoggedIn = isset($_SESSION["idUser"]);
if($isLoggedIn){
    
    $idUser = $_SESSION["idUser"];
}
?>