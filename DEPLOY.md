# Deploying Bakson to Coolify

The repo ships a production `Dockerfile` (Composer dependencies + built Vite assets +
`serversideup/php` nginx/php-fpm on port **8080**), so Coolify builds everything itself.
The site needs one MySQL database, for quote requests, sessions and the cache.

## 1. The code on GitHub

The repository is **public** (<https://github.com/Lukatsurtsumia/bakson>), so Coolify can
clone it without credentials: choose **Public Repository** and paste the URL. Secrets never
go in the repository; they live only in Coolify's environment variables.

## 2. Create the app in Coolify

1. **+ New → Resource → your repository**, branch `main`.
2. **Build pack: Dockerfile** (detected automatically). **Port: 8080.** Health check path: `/up`.
3. **+ New → Database → MySQL** in the same project. Note its internal host (the container
   name), database, user and password.

## 3. Environment variables

Paste these into the app's environment variables, click **Save**, then deploy. Coolify
only applies saved variables, on the next deploy.

```env
APP_NAME=Bakson
APP_ENV=production
APP_DEBUG=false
APP_KEY=                      # ./vendor/bin/sail artisan key:generate --show, paste the base64:… value
APP_URL=https://bakson.fr
APP_LOCALE=fr
APP_FALLBACK_LOCALE=fr
LOG_CHANNEL=stderr            # errors show up in Coolify's logs
LOG_LEVEL=warning

DB_CONNECTION=mysql
DB_HOST=                      # the MySQL resource's container name
DB_PORT=3306
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
CACHE_STORE=database
QUEUE_CONNECTION=sync         # quote e-mails are sent during the request; no worker needed

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=                # the Gmail address
MAIL_PASSWORD=                # a Google app password (see section 5), not the normal password
MAIL_FROM_ADDRESS=            # the same Gmail address
MAIL_FROM_NAME=Bakson
CONTACT_RECIPIENT=            # the inbox that receives quote requests

# Cloudflare Turnstile, the anti-bot check on the form (see section 6). Empty = off.
TURNSTILE_SITE_KEY=
TURNSTILE_SECRET_KEY=

# Run by the serversideup image on every boot: migrations, then config/route/view caching.
AUTORUN_ENABLED=true
AUTORUN_LARAVEL_MIGRATION=true
AUTORUN_LARAVEL_OPTIMIZE=true
```

## 4. Domain and HTTPS

Set the domain (e.g. `https://bakson.fr`) on the app; Coolify issues a Let's Encrypt
certificate. Behind Cloudflare's orange cloud, use SSL mode **Full (strict)**.
`bootstrap/app.php` already trusts the proxy, so URLs, assets and Livewire use https.

## 5. Gmail app password

1. Turn on 2-Step Verification on the Google account.
2. Create an app password at <https://myaccount.google.com/apppasswords> and copy the
   16-character key into `MAIL_PASSWORD`.
3. Keep `MAIL_FROM_ADDRESS` equal to the Gmail address, or Gmail rewrites the sender.

Check it after deploying: send the form once and confirm the e-mail arrives. If it doesn't,
the request is still saved and the SMTP error is in Coolify's logs.

## 6. Spam protection on the form

Always on: a hidden honeypot field, a minimum fill time (3 s), links refused in the name and
message, a duplicate guard, 5 requests per visitor per 10 minutes, and 30 per hour for the
whole site (this keeps Gmail's sending limit safe).

**Turnstile** (Cloudflare's free, mostly invisible CAPTCHA) stops bots that drive a real
browser. To switch it on:

1. Cloudflare dashboard → **Turnstile → Add widget**: name `Bakson`, hostname `bakson.fr`,
   widget mode **Managed**.
2. Copy the **site key** and **secret key** into `TURNSTILE_SITE_KEY` / `TURNSTILE_SECRET_KEY`
   in Coolify, save, redeploy.

For local testing, Cloudflare's test keys always pass: site `1x00000000000000000000AA`,
secret `1x0000000000000000000000000000000AA`.

## Gotchas

- Coolify **skips the build** when an image already exists for the same commit. Push a new
  commit, or force a rebuild, after changing the Dockerfile.
- Without `APP_KEY` the site answers 500 ("No application encryption key").
- The image is rebuilt on every deploy; nothing written inside the container survives.
  The site writes nothing to disk that needs keeping (everything is in MySQL).

## Before launch

- [ ] Real phone number and e-mail in `config/site.php` (they're placeholders).
- [ ] Confirm the city: the copy says **Lyon**.
- [ ] Real photos, above all real before/after pairs: the current "avant" images are
      the "après" photos dulled with CSS.
- [ ] Register the `bakson.fr` domain (free on 2026-10-04).
- [ ] Turnstile keys set in Coolify (section 6).
- [ ] *Mentions légales* and a privacy policy page: required for a French business site
      that collects personal data through a form.
- [ ] A consent step before loading the Google Maps embed (CNIL), or replace it with a
      static map image.
