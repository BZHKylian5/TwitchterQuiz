DROP DATABASE IF EXISTS mydb;
CREATE DATABASE mydb;
USE mydb;

-- Table pour stocker les informations sur les images
CREATE TABLE picture (
    idPicture INT AUTO_INCREMENT PRIMARY KEY,
    url VARCHAR(255) NOT NULL,
    titre VARCHAR(255) NOT NULL
);

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
    idu INT NOT NULL,
    reponse VARCHAR(255) NOT NULL,
    CONSTRAINT question_fk_reponse FOREIGN KEY (idq) REFERENCES question(id) on delete CASCADE,
    CONSTRAINT userTwitch_fk_reponse FOREIGN KEY (idu) REFERENCES user_twitch(user_id) on delete CASCADE
);

-- Table pour les réponses correctes
CREATE TABLE reponseCorrect (
    id INT AUTO_INCREMENT PRIMARY KEY,
    idq INT NOT NULL,
    idr INT NOT NULL,
    idu INT NOT NULL,
    reponse VARCHAR(255) NOT NULL,
    CONSTRAINT reponseCorrect_fk_question FOREIGN KEY (idq) REFERENCES question(id) on delete cascade,
    CONSTRAINT reponseCorrect_fk_reponse FOREIGN KEY (idr) REFERENCES reponse(idr) on delete cascade,
    CONSTRAINT userTwitch_fk_reponseCorrect FOREIGN KEY (idu) REFERENCES user_twitch(user_id) on delete CASCADE
);

CREATE VIEW question{
    SELECT *,
        STRING_AGG(DISTINCT JSONB_BUILD_OBJECT(
        'idReponse', rc.id,
        'reponse', rc.reponse,
    )::TEXT, ';')
    
    FROM question q
    JOIN reponse r on q.idq = r.idq
    JOIN reponseCorrect rc on q.idq = rc.idq

}

/* Peuplement BDD */

INSERT INTO user (email, username, password) VALUES ('kylian.houedec.56@gmail.com', 'BZHKylian', '$2y$10$aFCbPOMHDo3TffYRjC0sO.f.RPekP535AvHtCoT4WIctRVqZXUyI.');

