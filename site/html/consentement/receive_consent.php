<?php
require_once("../../.SECURE/config.php");

// Récupérer les données POST
$data = json_decode(file_get_contents('php://input'), true);

// Vérifier que les données nécessaires sont présentes
if (isset($data['username']) && isset($data['user_id']) && isset($data['consent'])) {
    $username = $data['username'];
    $user_id = $data['user_id'];
    $consent = $data['consent'];

    // Insérer ou mettre à jour le consentement dans la table user_twitch
    $sql = "INSERT INTO user_twitch (username, user_id, consent) VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE consent = ?";

    $stmt = $conn->prepare($sql);
    
    if ($stmt->execute([$username, $user_id, $consent, $consent])) {
        echo json_encode(['status' => 'success', 'message' => 'Consentement enregistré']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Erreur lors de l\'enregistrement']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Données manquantes']);
}
?>
