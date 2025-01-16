const tmi = require('tmi.js');

const tmi = require('axios');

import { CONST_CLIENT_ID, CONST_OAUTH_TOKEN, CONST_BROADCASTER_ID } from './.SECURE/config.js';

// Configuration du client Twitch
const client = new tmi.Client({
    options: { debug: true },
    connection: {
        reconnect: true,
        secure: true,
    },
    identity: {
        username: 'BZHKylian',
        password: 'oauth:' + CONST_OAUTH_TOKEN,
    },
    channels: ['bzhkylian'],
});

client.connect().catch(console.error);

// Message après connexion
client.on('connected', () => {
    client.say('#bzhkylian', `Bonjour tout le monde ! Pour participer, envoyez 👉 !participer 🎉 dans le chat. En envoyant cette commande, vous acceptez que vos données soient utilisées pour ce live interactif.`);
});

// Récupérer la liste des utilisateurs
async function getUserList() {
    try {
        const response = await axios.get('http://localhost/consentement/list_consent.php');
        return response.data.data || [];
    } catch (error) {
        console.error('Erreur lors de la récupération de la liste des utilisateurs :', error.message);
        return [];
    }
}

// Gestion des messages
client.on('message', async (channel, userstate, message, self) => {
    if (self) return; // Ignore les messages du bot

    try {
        const userList = await getUserList();
        const userId = userstate['user-id'];
        const isUserInList = userList.some(user => user.user_id === userId);

        // Commande : !participer
        if (message.toLowerCase() === '!participer') {
            if (!isUserInList) {
                await axios.post('http://localhost/consentement/receive_consent.php', {
                    username: userstate.username,
                    user_id: userId,
                    consent: true,
                });
                client.say(channel, `${userstate.username}, votre participation est enregistrée !`);
            } else {
                client.say(channel, `${userstate.username}, vous êtes déjà inscrit !`);
            }
            return;
        }

        // Commande : !supprimer
        if (message.toLowerCase() === '!supprimer') {
            if (isUserInList) {
                await axios.post('http://localhost/consentement/delete_consent.php', {
                    user_id: userId,
                });
                client.say(channel, `${userstate.username}, vos données ont été supprimées.`);
            } else {
                client.say(channel, `${userstate.username}, aucune donnée trouvée à supprimer.`);
            }
            return;
        }

        // Messages classiques (non commandes)
        if (isUserInList) {
            const now = new Date();
            const timestamp = now.toISOString();

            await axios.post('http://localhost/receive_data.php', {
                username: userstate.username,
                message: message.toUpperCase(),
                timestamp,
                user_id: userId,
            });

            client.say(channel, `${userstate.username}, votre message a été enregistré !`);
        }
    } catch (error) {
        console.error('Erreur lors de la gestion du message :', error.message);
        client.say(channel, `Une erreur s'est produite. Veuillez réessayer.`);
    }
});
