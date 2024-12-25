import tmi from 'tmi.js';
import axios from 'axios';
import { CONST_CLIENT_ID, CONST_OAUTH_TOKEN, CONST_BROADCASTER_ID } from './.SECURE/config.js';

// Stockage temporaire des participants
const participantData = [];

// Configuration du client Twitch
const client = new tmi.Client({
    options: { debug: true },
    connection: {
        reconnect: true,   // Reconnecter automatiquement en cas de déconnexion
        secure: true       // Utiliser une connexion sécurisée
    },
    identity: {
        username: 'BZHKylian',
        password: 'oauth:' + CONST_OAUTH_TOKEN
    },
    channels: ['bzhkylian'],
});

client.connect().catch(console.error);

// Envoi d'un message après la connexion
client.on('connected', () => {
    client.say('#bzhkylian', 'Bonjour tout le monde ! Pour participer, envoyez 👉   !participer   🎉 dans le chat. En envoyant cette commande, vous acceptez que vos données (pseudo, ID Twitch, réponse) soient utilisées uniquement pour ce live interactif. Ces données seront supprimées à la fin du live. ❌ Vous pouvez demander à retirer vos données à tout moment en envoyant 👉   !supprimer  .🗑️');
});

// Fonction pour récupérer la liste des utilisateurs depuis list_content.php
async function getUserList() {
    try {
        const response = await axios.get('http://localhost/consentement/list_consent.php');
        return response.data; // Supposons que la réponse est un tableau des utilisateurs
    } catch (error) {
        console.error('Erreur lors de la récupération de la liste des utilisateurs :', error.message);
        return [];
    }
}

// Gestion des messages
client.on('message', async (channel, userstate, message, self) => {
    if (self) return; // Ignore les messages du bot lui-même

    try {
        // Vérification que le message n'est pas une commande système
        if (message.toLowerCase() !== '!participer' && message.toLowerCase() !== '!supprimer') {
            const userList = await getUserList();
            const isUserInList = userList.some(user => user.userId === userstate['user-id']);
            
            if (isUserInList) {
                const now = new Date();
                const timestamp = now.toISOString();

                await axios.post('http://localhost/receive_data.php', {
                    username: userstate.username,
                    message: message.toUpperCase(),
                    timestamp: timestamp,
                    user_id: userstate['user-id'],
                });

                client.say(channel, `${userstate.username}, votre message a été enregistré !`);
            }
        }

        // Gestion de la commande !participer
        if (message.toLowerCase() === '!participer') {
            // Vérifie si l'utilisateur n'est pas déjà dans la liste
            const alreadyExists = participantData.some(user => user.userId === userstate['user-id']);
            if (!alreadyExists) {
                participantData.push({
                    username: userstate.username,
                    userId: userstate['user-id'],
                    consent: true,
                });

                // Envoi des données de consentement au script PHP
                await axios.post('http://localhost/consentement/receive_consent.php', {
                    username: userstate.username,
                    user_id: userstate['user-id'],
                    consent: true,
                });

                client.say(channel, `${userstate.username}, votre participation est enregistrée !`);
            } else {
                client.say(channel, `${userstate.username}, vous êtes déjà inscrit !`);
            }
        }

        // Gestion de la commande !supprimer
        if (message.toLowerCase() === '!supprimer') {
            const index = participantData.findIndex(user => user.userId === userstate['user-id']);
            if (index !== -1) {
                participantData.splice(index, 1); // Supprime l'utilisateur
                client.say(channel, `${userstate.username}, vos données ont été supprimées.`);
                
                // Supprime les données dans la base de données via PHP (facultatif)
                await axios.post('http://localhost/consentement/delete_consent.php', {
                    user_id: userstate['user-id'],
                });
            } else {
                client.say(channel, `${userstate.username}, aucune donnée trouvée à supprimer.`);
            }
        }

    } catch (error) {
        console.error('Erreur lors de l\'envoi des données ou de la suppression du message :', error.response ? error.response.data : error.message);
    }
});
