<?php
require_once __DIR__ . "/../config.php";


if($isLoggedIn){
    header('location: /');
} else{
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $pseudo = $_POST["pseudo"];
        $password = $_POST["password"];

        $stmt = $conn -> prepare("SELECT * FROM user WHERE username = ? OR email = ?");
        $stmt->execute([$pseudo, $pseudo]);
        $user = $stmt -> fetch(PDO::FETCH_ASSOC);

        if($user && password_verify($password, $user["password"])){
            $_SESSION["idUser"] = $user["id"];
            header("location: /");
        } else{
            $error = "Identifiant incorrect";
        }

    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
</head>
<body>
    <?php
    if(isset($error)){
    ?>
        <p style="color: red;"><?= $error ?></p>
    <?php
    }
    ?>
    <form action="/login/" method="post">
        <div>
            <label for="pseudo">Pseudo ou Email :</label>
            <input name="pseudo" type="text">
        </div>
        <div>
            <label for="password">Mot de passe :</label>
            <input name="password" type="password">
        </div>
        <div>
            <input type="submit" value="Connexion">
        </div>
    </form>
</body>
</html>