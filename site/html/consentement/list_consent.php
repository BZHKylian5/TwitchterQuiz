<?php
require_once("../../.SECURE/config.php");

// Connexion à la base de données
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Connexion échouée : ' . $e->getMessage()]);
    exit;
}

// Récupérer la liste des utilisateurs ayant donné leur consentement
$sql = "SELECT username, user_id FROM user_twitch WHERE consent = 1";
$stmt = $conn->prepare($sql);
$stmt->execute();

// Récupérer tous les résultats sous forme de tableau
$participants = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Vérifier s'il y a des résultats
if ($participants) {
    // Renvoi des résultats au format JSON
    echo json_encode(['status' => 'success', 'data' => $participants]);
} else {
    // Aucun utilisateur n'a donné son consentement
    echo json_encode(['status' => 'error', 'message' => 'Aucun utilisateur n\'a donné son consentement']);
}
?>
