<?php
require_once "config.php";

if(!$isLoggedIn){
    header("location: /login/");
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <header>
        <a href="/"><img id="logoSite" src="" alt="logo site" title="logo du site"></a>
        <div>
            <a href="/login/">Connexion</a>
        </div>
    </header>
    <main>
        
    </main>
</body>
</html>