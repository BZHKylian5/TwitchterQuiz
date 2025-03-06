<?php 
$stmt = $conn->prepare("SELECT * FROM viewuser WHERE id = ?");
$stmt -> execute([$idUser]);
$result = $stmt -> fetch(PDO::FETCH_ASSOC);

?>

<header>
    <a href="/"><img id="logoSite" src="/asset/img/logo.png" alt="logo site" title="logo du site"></a>
    <?php if (!$isLoggedIn) {
    ?>
        <div>
            <a href="/login/">Connexion</a>
        </div>
    <?php
    } else {
    ?>
        <div id="profilPicture">
            <img src="/<?= $result['url'] ?>" title="<?= $result['titre'] ?>" alt="<?= $result['titre'] ?>">
        </div>
    <?php
    } ?>
</header>