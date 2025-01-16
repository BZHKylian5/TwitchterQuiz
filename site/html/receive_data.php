<?php
// Configuration de la base de données
require_once("./config.php");

// Récupération des données envoyées par le bot
$data = json_decode(file_get_contents('php://input'), true);
$stmt = $conn->prepare("SELECT * from user_twitch WHERE user_id = :user_id");
$stmt -> execute(['user_id' => $data['user_id']]);
$user = $stmt -> fetch(PDO::FETCH_ASSOC);

if($user){
    if($user['username'] !== $data['username']){
        $stmt = $conn -> prepare("UPDATE user_twitch SET username = :username WHERE user_id = :user_id");
        $stmt -> execute(['username' => $data['username'], 'user_id' => $data['user_id']]);
    }
} else{
    $stmt = $conn -> prepare("INSERT INTO user_twitch(username) VALUE (:username)");
    $stmt -> execute(['username' => $data['username']]);
}

$datetime = new DateTime($data['timestamp']);
$formattedTimestamp = $datetime->format('Y-m-d H:i:s'); // Format MySQL

$stmt =  $conn -> prepare("INSERT INTO messages (username, message, user_id, timestamp) VALUE (:username, :message, :user_id, :timestamp)");
$stmt->execute([
    'username' => $data['username'], 
    'message' => $data['message'], 
    'user_id' => $data['user_id'], 
    'timestamp' => $formattedTimestamp
]);



?>
