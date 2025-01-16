DROP DATABASE IF EXISTS mydb;
CREATE DATABASE mydb;
USE mydb;

-- Table pour stocker les informations sur les images
CREATE TABLE picture (
    idPicture INT AUTO_INCREMENT PRIMARY KEY,
    url VARCHAR(255) DEFAULT 'asset/img/imageProfil/default.svg' NOT NULL,
    titre VARCHAR(255) NOT NULL
);

INSERT INTO picture (titre) VALUES ('image par default de l\'image de profil'); -- Ajuste les colonnes et valeurs selon ta structure


-- Table pour stocker les utilisateurs
CREATE TABLE user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    idPicture INT DEFAULT 1 NOT NULL,
    CONSTRAINT picture_fk_user FOREIGN KEY (idPicture) REFERENCES picture(idPicture)
);

-- Table pour stocker les informations liées à Twitch
CREATE TABLE user_twitch (
    user_id VARCHAR(255) PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    consent BOOLEAN NOT NULL DEFAULT false ,
    photoProfile int default 1 not null, 
    CONSTRAINT picture_fk_userTwitch FOREIGN KEY (photoProfile) REFERENCES picture(idPicture)
);

-- Table pour stocker les messages des utilisateurs
CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    user_id VARCHAR(255) NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    CONSTRAINT message_fk_userTwitch FOREIGN KEY (user_id) REFERENCES user_twitch(user_id)
);

-- Table pour les catégories de questions
CREATE TABLE categorie (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nomCateg VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT NOW() NOT NULL,
    updated_at TIMESTAMP DEFAULT NOW() ON UPDATE NOW() NOT NULL
);

-- Table pour les questions
CREATE TABLE question (
    id INT AUTO_INCREMENT PRIMARY KEY,
    idCateg INT NOT NULL,
    question TEXT NOT NULL,
    CONSTRAINT category_fk_question FOREIGN KEY (idCateg) REFERENCES categorie(id)
);

-- Table pour les réponses
CREATE TABLE reponse (
    idr INT AUTO_INCREMENT PRIMARY KEY,
    idq INT NOT NULL,
    idu VARCHAR(255) NOT NULL,
    reponse VARCHAR(255) NOT NULL,
    CONSTRAINT question_fk_reponse FOREIGN KEY (idq) REFERENCES question(id) ON DELETE CASCADE,
    CONSTRAINT userTwitch_fk_reponse FOREIGN KEY (idu) REFERENCES user_twitch(user_id) ON DELETE CASCADE
);

-- Table pour les réponses correctes
CREATE TABLE reponseCorrect (
    id INT AUTO_INCREMENT PRIMARY KEY,
    idq INT NOT NULL,
    idr INT NOT NULL,
    idu VARCHAR(255) NOT NULL,
    reponse VARCHAR(255) NOT NULL,
    CONSTRAINT reponseCorrect_fk_question FOREIGN KEY (idq) REFERENCES question(id) ON DELETE CASCADE,
    CONSTRAINT reponseCorrect_fk_reponse FOREIGN KEY (idr) REFERENCES reponse(idr) ON DELETE CASCADE,
    CONSTRAINT userTwitch_fk_reponseCorrect FOREIGN KEY (idu) REFERENCES user_twitch(user_id) ON DELETE CASCADE
);

CREATE VIEW question_reponses AS
SELECT 
    q.id AS question_id,
    q.question AS question_text,
    CONCAT('[', GROUP_CONCAT(
        DISTINCT JSON_OBJECT(
            'idReponse', r.idr,
            'reponse', r.reponse
        )
    ), ']') AS reponses,
    CONCAT('[', GROUP_CONCAT(
        DISTINCT JSON_OBJECT(
            'idReponseCorrecte', rc.id,
            'reponseCorrecte', rc.reponse
        )
    ), ']') AS reponses_correctes
FROM 
    question q
LEFT JOIN 
    reponse r ON q.id = r.idq
LEFT JOIN 
    reponseCorrect rc ON q.id = rc.idq
GROUP BY 
    q.id, q.question;


CREATE VIEW viewuser AS
SELECT 
    u.*,
    p.idpicture as picture,
    p.url,
    p.titre
FROM 
    user u
LEFT JOIN
    picture p on p.idPicture = u.idPicture;


/* Peuplement BDD */

INSERT INTO user (email, username, password) VALUES ('kylian.houedec.56@gmail.com', 'BZHKylian', '$2y$10$aFCbPOMHDo3TffYRjC0sO.f.RPekP535AvHtCoT4WIctRVqZXUyI.');

