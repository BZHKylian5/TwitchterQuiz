<?php
// Configuration de la base de données
require_once("./.SECURE/config.php");

// Récupération des données envoyées par le bot
$data = json_decode(file_get_contents('php://input'), true);

// Vérification que les données nécessaires sont présentes
if (isset($data['username']) && isset($data['message']) && isset($data['user_id'])) {
    $username = $data['username'];
    $message = $data['message'];
    $user_id = $data['user_id'];

    try {
        // Vérifier si l'utilisateur existe dans la table user_twitch
        $sql = "SELECT username FROM user_twitch WHERE user_id = :user_id";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['user_id' => $user_id]);
        $result = $stmt->fetch();

        if ($result) {
            // Utilisateur existe, vérifier si le pseudo a changé
            if ($result['username'] !== $username) {
                // Mettre à jour le pseudo
                $sql = "UPDATE user_twitch SET username = :username WHERE user_id = :user_id";
                $stmt = $conn->prepare($sql);
                $stmt->execute(['username' => $username, 'user_id' => $user_id]);
            }
        } else {
            // Utilisateur n'existe pas, l'insérer
            $sql = "INSERT INTO user_twitch (user_id, username) VALUES (:user_id, :username)";
            $stmt = $conn->prepare($sql);
            $stmt->execute(['user_id' => $user_id, 'username' => $username]);
        }

        // Insérer le message dans la table messages
        $sql = "INSERT INTO messages (username, message, user_id) VALUES (:username, :message, :user_id)";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['username' => $username, 'message' => $message, 'user_id' => $user_id]);

        echo json_encode(['status' => 'success']);
    } catch (\PDOException $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Erreur lors de l\'insertion des données : ' . $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Données manquantes']);
}
?>
