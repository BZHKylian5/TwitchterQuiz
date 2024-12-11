# Utiliser l'image officielle PHP avec Apache
FROM php:8.2-apache

# Mettre à jour les paquets et installer Node.js, npm, yarn, et autres dépendances nécessaires
RUN apt-get update && apt-get install -y \
    curl \
    gnupg2 \
    lsb-release \
    && curl -sL https://deb.nodesource.com/setup_16.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g yarn \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install mysqli pdo pdo_mysql

# Copier les fichiers PHP dans le répertoire du serveur Apache
COPY ./site/html /var/www/html
COPY ./site/.SECURE /var/www/.SECURE

# Installer les dépendances Node.js pour le bot Twitch
WORKDIR /usr/local/bin/scriptsNode

# Copier le package.json dans le répertoire du conteneur
COPY ./scriptsNode/package.json /usr/local/bin/scriptsNode/package.json

COPY ./scriptsNode/.SECURE /usr/local/bin/scriptsNode/.SECURE

# Installer les dépendances avec yarn
RUN yarn install --cwd /usr/local/bin/scriptsNode  # Utiliser yarn pour installer les dépendances

# Copier le script Node.js (botTwitch.js) dans le conteneur
COPY ./scriptsNode/botTwitch.js /usr/local/bin/scriptsNode/botTwitch.js

# Activer le module Apache mod_rewrite
RUN a2enmod rewrite

# Exposer le port 80 pour le serveur Apache
EXPOSE 80

# Commande pour démarrer Apache et le bot Node.js
CMD ["bash", "-c", "yarn start /usr/local/bin/scriptsNode/botTwitch.js & apache2-foreground"]
