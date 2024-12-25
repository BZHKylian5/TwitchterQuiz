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

// Vérifier que l'ID de l'utilisateur est présent
if (isset($data['user_id'])) {
    $user_id = $data['user_id'];

    // Supprimer les données de consentement dans la base de données
    $sql = "DELETE FROM user_twitch WHERE user_id = ?";

    $stmt = $conn->prepare($sql);
    
    if ($stmt->execute([$user_id])) {
        echo json_encode(['status' => 'success', 'message' => 'Consentement supprimé']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la suppression']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'ID de l\'utilisateur manquant']);
}
?>
