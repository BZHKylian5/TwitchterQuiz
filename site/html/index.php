<?php
require_once "config.php";

if(!$isLoggedIn){
    header("location: /login/");
}

$stmt = $conn->prepare("SELECT * FROM viewuser WHERE id = ?");
$stmt -> execute([$idUser]);
$result = $stmt -> fetch(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./asset/css/style.css">
</head>
<body>
    <header>
        <a href="/"><img id="logoSite" src="./asset/img/logo.png" alt="logo site" title="logo du site"></a>
        <?php if(!$isLoggedIn){
        ?>
            <div>
                <a href="/login/">Connexion</a>
            </div>
        <?php
        }else{
        ?>
            <div id="profilPicture">
                <img src="<?=$result['url']?>" title="<?=$result['titre']?>" alt="<?=$result['titre']?>">
            </div>
        <?php
        }?>
    </header>
    <main>
        
    </main>
</body>
</html>