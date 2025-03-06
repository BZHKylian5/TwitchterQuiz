<?php
require_once "config.php";

if (!$isLoggedIn) {
    header("location: /login/");
}

$stmt = $conn->prepare("SELECT * FROM viewuser WHERE id = ?");
$stmt->execute([$idUser]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <link rel="stylesheet" href="./asset/css/style.css">
</head>

<body>
    <?php require_once "componant/header.php"; ?>
    <main>
        <section class="navBtn">
            <a href="/createQuestion/">
                <div class="btnCreate">
                    Créer des Questions
                </div>
            </a>
            <a href="/createQuestion/">
                <div class="btnCreate">
                    Modifier des Questions
                </div>
            </a>

        </section>
    </main>
</body>

</html>