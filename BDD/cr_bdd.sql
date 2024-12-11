CREATE TABLE user_twitch (
    user_id VARCHAR(255) PRIMARY KEY,  -- ID de l'utilisateur Twitch
    username VARCHAR(255) NOT NULL     -- Nom d'utilisateur
);

CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,  -- Identifiant unique pour chaque message
    username VARCHAR(255) NOT NULL,     -- Nom d'utilisateur
    message TEXT NOT NULL,              -- Contenu du message
    user_id VARCHAR(255) NOT NULL,      -- ID de l'utilisateur
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,  -- Date et heure du message
    CONSTRAINT fk_user_id FOREIGN KEY (user_id) REFERENCES user_twitch(user_id)  -- Clé étrangère
);
