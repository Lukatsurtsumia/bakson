# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

"Bakson" is a one-page, French-language, phone-first marketing site for a car-detailing business in Lyon. Stack: Laravel 13 (PHP 8.4), server-rendered Blade, Livewire 4 for the contact form, Alpine.js (bundled with Livewire) for the interactive sections, and Tailwind CSS v4, running on Laravel Sail (MySQL 8.4). There are no user accounts: the old Breeze/Inertia/Vue scaffolding was removed on purpose.

## Development Environment

- The app runs via Laravel Sail. Ports are customized in `.env` (`APP_PORT=8210`, `VITE_PORT=5210`, `FORWARD_DB_PORT=3360`); don't assume Laravel's defaults (80/5173/3306).
- Start the stack: `./vendor/bin/sail up -d --no-build` (the `sail-8.4/app` image already exists; a fresh `sail build` is slow), then `./vendor/bin/sail npm run dev` for Vite on :5210.
- **Any Artisan command that touches the database (`migrate`, `test`, `tinker`, etc.) must run through Sail**: `DB_HOST=mysql` only resolves inside the Sail Docker network. Node and npm only exist inside the container.
- When scripting `sail` from a heredoc or pipe, give it `< /dev/null`; otherwise `docker exec -i` swallows the rest of the script's stdin.
- **Production** is a Coolify deploy of the repo's `Dockerfile` (serversideup php 8.4 on :8080, MySQL resource, `AUTORUN_*` migrations + optimize on boot); `DEPLOY.md` has the env vars and the launch checklist. The assets stage copies `vendor/livewire/livewire/dist` because `app.js` bundles Livewire from it. `bootstrap/app.php` trusts all proxies and appends `SecurityHeaders`.

## Common Commands

- Build frontend assets: `./vendor/bin/sail npm run build`
- Format JS/CSS: `./vendor/bin/sail npm run format` (Prettier). Format PHP: `./vendor/bin/sail bin pint --dirty`
- Run all tests: `./vendor/bin/sail artisan test`; a single file: `./vendor/bin/sail artisan test tests/Feature/ContactFormTest.php`

## Architecture

- **Content lives in `config/site.php`**: business details (phone, e-mail, hours, map), the contact form's time slots (`morning`/`afternoon`/`evening`; the keys are stored in `contact_messages.preferred_time`, the labels and hours can change), hero slides, services and before/after projects. The phone, e-mail and photos are placeholders. `AppServiceProvider` shares `config('site.business')` with every view as `$business`.
- **Photos** are free-licence Unsplash images served from Unsplash's CDN; `App\Support\Unsplash` builds `src`/`srcset` URLs. Projects have `before => null`, so the slider dulls the "after" photo as a stand-in. Real before/after photos must replace them before launch.
- **Page composition**: `routes/web.php` has a single `Route::view('/', 'home')`. `resources/views/home.blade.php` uses the `<x-layouts.site>` layout (meta/OG tags, schema.org data, Vite, Livewire) and includes the sections in `resources/views/home/` (`hero`, `services`, `before-after`, `contact`). Shared Blade components live in `resources/views/components/` (`site-header`, `site-footer`, `mobile-action-bar`, `before-after-slider`, `brand-button`, `icon`).
- **Alpine components** are registered in `resources/js/app.js`, which bundles Livewire manually (`livewire.esm` + `Livewire.start()`, with `@livewireScriptConfig` in the layout). One file per component in `resources/js/components/`: `heroSlideshow`, `beforeAfterSlider`, `beforeAfterGallery` (carousel + fullscreen viewer; the phone's back button closes it), `mobileActionBar`, `siteHeader`.
- **Contact form**: `App\Livewire\ContactForm` + `resources/views/livewire/contact-form.blade.php`. Fields validate on blur with `wire:model.live.blur` (in Livewire 4, plain `.blur` no longer sends a request). On submit it checks a honeypot and a rate limit (5 per IP per 10 minutes), saves a `ContactMessage`, then e-mails `App\Mail\ContactMessageReceived` (Reply-To = the visitor) to `config('site.contact_recipient')` (`CONTACT_RECIPIENT`, falling back to `MAIL_FROM_ADDRESS`). A mail failure is reported but doesn't fail the visitor's submission. Keep Alpine off the Livewire component's root element.
- **Mail** goes through Gmail SMTP: `MAIL_USERNAME` is the Gmail address, `MAIL_PASSWORD` a Google app password.
- **Theme**: a custom `ember-*` color scale and `--font-display` (Playfair Display) in the `@theme` block of `resources/css/app.css`; `stone` for neutrals. Tailwind v4 uses `bg-linear-to-*` for gradients.

## Testing

- PHPUnit. `tests/Feature/HomePageTest.php` covers rendering, config details, SEO tags and the removed auth routes (404). `tests/Feature/ContactFormTest.php` covers save + mail, validation, honeypot, rate limit, mail failure and the mail content.
- `phpunit.xml` uses array cache/session/mail, a `sync` queue and the `testing` database, and sets `CONTACT_RECIPIENT=devis@bakson.test`.
- Server-side tests can't catch broken Alpine/JS; check interactive changes in a real browser at phone width.

---

The block below is managed by Laravel Boost (`boost.json` has `"guidelines": true`) and is regenerated by `php artisan boost:install`. Don't hand-edit it; add project-specific guidance above instead.

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

## Foundational Context

This application is a Laravel application running on PHP 8.4. Always use the APIs that match the installed major version of each package — do not assume a version.

Before relying on a package's API, confirm its installed version:
- PHP packages: run `vendor/bin/sail composer show --direct` to list direct dependencies with versions, or `vendor/bin/sail composer show <vendor/package>` for a single package.
- JS packages: check `package.json` for the installed versions.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If a frontend change doesn't show in the UI or you get a "Unable to locate file in Vite manifest" error, run `vendor/bin/sail npm run build` or ask the user to run `vendor/bin/sail npm run dev` or `vendor/bin/sail composer run dev`.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Use `search-docs` before changes that depend on Laravel ecosystem APIs, behavior, configuration, or version-specific syntax. Skip it for copy-only edits and other changes where package documentation is irrelevant. Reuse sufficient results already in context instead of searching again.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Project Rules

- This project contains committed, area-grouped rules in `.ai/rules` when that directory exists, including path-scoped framework guidelines under `.ai/rules/boost`. Before you enter plan mode or create/edit any file, you MUST first: open @.ai/rules/index.md (it maps file globs to rule files), read every rule file whose globs cover the path(s) in scope, and run `grep -rin 'keyword' .ai/rules` to catch what a path match alone misses. Do not write code until you have read and are following every matching rule. If `.ai/rules` does not exist, continue without it.

## Artisan

- Run Artisan commands directly via the command line (e.g., `vendor/bin/sail artisan route:list`). Use `vendor/bin/sail artisan list` to discover available commands and `vendor/bin/sail artisan [command] --help` to check parameters.
- Inspect routes with `vendor/bin/sail artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `vendor/bin/sail artisan config:show app.name`, `vendor/bin/sail artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `vendor/bin/sail artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `vendor/bin/sail artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== sail rules ===

# Laravel Sail

- This project runs inside Laravel Sail's Docker containers. You MUST execute all commands through Sail.
- Start services using `vendor/bin/sail up -d` and stop them with `vendor/bin/sail stop`.
- Open the application in the browser by running `vendor/bin/sail open`.
- Always prefix PHP, Artisan, Composer, and Node commands with `vendor/bin/sail`. Examples:
    - Run Artisan Commands: `vendor/bin/sail artisan migrate`
    - Install Composer packages: `vendor/bin/sail composer install`
    - Execute Node commands: `vendor/bin/sail npm run dev`
    - Execute PHP scripts: `vendor/bin/sail php [script]`
- View all available Sail commands by running `vendor/bin/sail` without arguments.

=== tests rules ===

# Test Enforcement

- Add or update tests for behavior and logic changes when a test provides meaningful regression coverage.
- Pure copy, styling, and layout-only changes do not require new or updated tests.
- When test coverage applies, run the affected tests and ensure they pass.
- Test the changed behavior and its important failure modes, but do not add tests beyond them.
- Read the `testing-best-practices` skill before writing tests.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `vendor/bin/sail artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `vendor/bin/sail artisan list` and check their parameters with `vendor/bin/sail artisan [command] --help`.
- If you're creating a generic PHP class, use `vendor/bin/sail artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `vendor/bin/sail artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `vendor/bin/sail artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

=== livewire/core rules ===

# Livewire

- Livewire allows you to build dynamic, reactive interfaces in PHP without writing JavaScript.
- You can use Alpine.js for client-side interactions instead of JavaScript frameworks.
- Keep state server-side so the UI reflects it. Validate and authorize in actions as you would in HTTP requests.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/sail bin pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/sail bin pint --test --format agent`, simply run `vendor/bin/sail bin pint --format agent` to fix any formatting issues.

=== phpunit/core rules ===

# PHPUnit

- This project uses PHPUnit. Create tests with `vendor/bin/sail artisan make:test --phpunit {name}`.
- Do not include the test suite directory in `{name}`. Use `SomeFeatureTest`, not `Feature/SomeFeatureTest`.
- Read the `testing-best-practices` skill for guidance on coverage, naming, structure, dependency isolation, and review.

## Running Tests

- Run the narrowest set of tests that covers the change. Pass a file path or `--filter=testName` to `vendor/bin/sail artisan test --compact`.
- Rerun a test after each change to it.
- Run `vendor/bin/sail bin phpunit` to call the test runner directly. It accepts the same file path and `--filter=testName` arguments.

</laravel-boost-guidelines>
