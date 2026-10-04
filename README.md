# Bakson

Phone-first marketing site for a car-detailing business in Lyon: hero slideshow, services,
before/after gallery, and a quote request form that is saved and e-mailed to the business.

Laravel 13 · Blade · Livewire 4 · Alpine.js · Tailwind CSS 4 · Laravel Sail (MySQL 8.4)

## Run locally

```bash
./vendor/bin/sail up -d --no-build
./vendor/bin/sail artisan migrate
./vendor/bin/sail npm run dev
```

The site is served at http://localhost:8210.

## Configuration

- Business details, slides, services and before/after projects: `config/site.php`.
- Quote e-mails go through Gmail SMTP. In `.env`, set `MAIL_USERNAME` to the Gmail address,
  `MAIL_PASSWORD` to a Google app password, and `CONTACT_RECIPIENT` to the inbox that should
  receive the requests.

## Tests

```bash
./vendor/bin/sail artisan test
```
