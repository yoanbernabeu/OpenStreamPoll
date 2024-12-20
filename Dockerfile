# install PHP dependencies for development
FROM composer:2 AS composer_dev
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-scripts --no-progress --prefer-dist

# install PHP dependencies for production
FROM composer:2 AS composer_prod
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-scripts --no-progress --prefer-dist --no-dev

# install Node dependencies
FROM node:20 AS node
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm install

# build assets
FROM dunglas/frankenphp AS build
WORKDIR /app
COPY --from=composer_dev /app/vendor /app/vendor
COPY --from=node /app/node_modules /app/node_modules
COPY . /app/
RUN APP_ENV=prod php bin/console tailwind:build --minify
RUN APP_ENV=prod php bin/console importmap:install
RUN APP_ENV=prod php bin/console asset-map:compile

# build the final image
FROM dunglas/frankenphp

ENV SERVER_NAME=your-app.com
ENV APP_RUNTIME=Runtime\\FrankenPhpSymfony\\Runtime
ENV APP_ENV=prod
ENV FRANKENPHP_CONFIG="worker ./public/index.php"

COPY . /app/
COPY --from=composer_prod /app/vendor /app/vendor
COPY --from=node /app/node_modules /app/node_modules
COPY --from=build /app/public/assets /app/public/assets
