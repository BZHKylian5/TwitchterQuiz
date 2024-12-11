import tmi from 'tmi.js';
import axios from 'axios';
import { CONST_CLIENT_ID, CONST_OAUTH_TOKEN, CONST_BROADCASTER_ID } from './.SECURE/config.js';


// Configurations pour l'API Twitch
const API_BASE_URL = 'https://api.twitch.tv/helix/moderation';
const CLIENT_ID = CONST_CLIENT_ID; // Remplacez par votre Client-ID
const OAUTH_TOKEN = CONST_OAUTH_TOKEN; // Remplacez par votre token OAuth
const BROADCASTER_ID = CONST_BROADCASTER_ID; // Remplacez par l'ID de la chaîne
const MODERATOR_ID = CONST_BROADCASTER_ID; // Remplacez par l'ID du modérateur (peut être le même que BROADCASTER_ID)

// Configuration du client Twitch
const client = new tmi.Client({
    options: { debug: true },
    connection: {
        reconnect: true,   // Reconnecter automatiquement en cas de déconnexion
        secure: true       // Utiliser une connexion sécurisée
    },

    identity: {
        username: 'BZHKylian',
        password: 'oauth:' + OAUTH_TOKEN
    },

    channels: ['bzhkylian'],
});

client.connect().catch(console.error);

client.on('message', async (channel, userstate, message, self) => {
    if (self) return; // Ignore les messages du bot lui-même
    try {
        // Obtenir l'heure actuelle
        const now = new Date();
        const timestamp = now.toISOString(); // Format de l'heure ISO 8601

        // Envoyer les données au serveur
        await axios.post('http://localhost/receive_data.php', {
            username: userstate.username,
            message: message.toUpperCase(),
            timestamp: timestamp,
            user_id: userstate['user-id']  // Assurez-vous que ce champ existe dans userstate
        });

    } catch (error) {
        console.error('Erreur lors de l\'envoi des données ou de la suppression du message :', error.response ? error.response.data : error.message);
    }

});

