<?php
require_once("../../.SECURE/config.php");

// Connexion à la base de données
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connexion échouée : ' . $e->getMessage();
    exit;
}

// Récupérer les données POST
$data = json_decode(file_get_contents('php://input'), true);

// Vérifier que les données nécessaires sont présentes
if (isset($data['username']) && isset($data['user_id']) && isset($data['consent'])) {
    $username = $data['username'];
    $user_id = $data['user_id'];
    $consent = $data['consent'];

    // Insérer ou mettre à jour le consentement dans la table user_twitch
    $sql = "INSERT INTO user_twitch (username, user_id, consent) VALUES (:username, :user_id, :consent)
            ON DUPLICATE KEY UPDATE consent = :consent";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':consent', $consent);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Consentement enregistré']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Erreur lors de l\'enregistrement']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Données manquantes']);
}
?>
