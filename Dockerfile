# syntax=docker/dockerfile:1
#
# Production image for Coolify. Multi-stage:
#   1. vendor  -> PHP dependencies (no dev)
#   2. assets  -> compiled Vite/Tailwind assets (app.js bundles Livewire from vendor/)
#   3. runtime -> serversideup nginx + php-fpm, serving public/ on :8080
#
# Boot-time Laravel tasks (migrate, config/route/view cache) are run by serversideup's
# autorun when the AUTORUN_* env vars are set in Coolify. See DEPLOY.md.

# ---------- 1) PHP dependencies ----------
FROM composer:2 AS vendor
WORKDIR /app
COPY database/ database/
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --no-progress \
        --prefer-dist --no-scripts --optimize-autoloader

# ---------- 2) Front-end assets ----------
FROM node:22-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund
# resources/js/app.js imports Livewire's ESM build straight from vendor/.
COPY --from=vendor /app/vendor/livewire/livewire/dist vendor/livewire/livewire/dist
# .gitignore keeps Tailwind's class scanner out of vendor/, the same as in development.
COPY .gitignore vite.config.js ./
COPY resources/ resources/
COPY public/ public/
RUN npm run build

# ---------- 3) Runtime ----------
FROM serversideup/php:8.4-fpm-nginx
WORKDIR /var/www/html
USER www-data

# App source (filtered by .dockerignore), then dependencies and built assets on top.
COPY --chown=www-data:www-data . .
COPY --chown=www-data:www-data --from=vendor /app/vendor ./vendor
COPY --chown=www-data:www-data --from=assets /app/public/build ./public/build

# Rebuild the package manifest (composer install ran with --no-scripts).
RUN php artisan package:discover --ansi
