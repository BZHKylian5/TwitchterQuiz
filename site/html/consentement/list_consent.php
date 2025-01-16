<?php
require_once("../../.SECURE/config.php");

header('Content-Type: application/json');

try {
    // Connexion à la base de données
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Requête pour récupérer les utilisateurs ayant donné leur consentement
    $sql = "SELECT username, user_id FROM user_twitch WHERE consent = 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    // Récupérer les résultats
    $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Vérifier s'il y a des résultats
    if (!empty($participants)) {
        echo json_encode(['status' => 'success', 'data' => $participants]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Aucun utilisateur n\'a donné son consentement']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Erreur de connexion à la base de données : ' . $e->getMessage()]);
    exit;
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Erreur inattendue : ' . $e->getMessage()]);
    exit;
}
?>
