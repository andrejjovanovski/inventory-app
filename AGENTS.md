# AGENTS.md

## Overview

- Laravel 12 application for inventory and member/group management.
- Admin UI is built with Filament 4 resources under `app/Filament/Resources`.
- Core domains currently include members, groups, categories, items, transactions, events, attendances, users, roles, and permissions.

## Stack

- PHP 8.2
- Laravel 12
- Filament 4
- Vite 7
- Tailwind CSS 4
- PHPUnit 11
- Larastan
- Laravel Pint

## Repository Layout

- `app/Filament/Resources`: Filament resources, pages, tables, schemas, and relation managers.
- `app/Models`: Eloquent models for the main business entities.
- `app/Http/Controllers`: HTTP controllers, including member verification flow.
- `app/Http/Requests`: Form request validation classes.
- `app/Policies`: Authorization policies.
- `database/migrations`: Schema history.
- `database/seeders`: Seed data, including admin and domain seeders.
- `resources/views`: Blade views and email templates.
- `resources/css`, `resources/js`: Frontend assets compiled by Vite.
- `tests/Feature`, `tests/Unit`: PHPUnit tests.

## Common Commands

- Install PHP dependencies: `composer install`
- Install JS dependencies: `npm install`
- Start the local dev stack: `composer dev`
- Alternate local start script: `./start.sh`
- Run tests: `composer test`
- Run a single test file: `php artisan test tests/Feature/ViewRenderingTest.php`
- Run formatting: `./vendor/bin/pint`
- Run static analysis: `./vendor/bin/phpstan analyse`
- Build frontend assets: `npm run build`

## Development Notes

- `composer dev` starts `php artisan serve`, `php artisan queue:listen --tries=1`, `php artisan pail --timeout=0`, and `npm run dev` concurrently.
- There is also a custom `php artisan dev:serve` command in `app/Console/Commands/DevServe.php` that runs the Laravel server and queue worker with grouped logging.
- Filament resources are split into page, schema, table, and relation-manager classes. Follow the existing resource structure instead of inlining large form/table definitions into a single file.
- Authorization is policy-driven. Check `app/Policies` before changing access behavior.
- Member verification email rendering and flow already have feature coverage; extend tests when touching verification or email views.
- The root `README.md` is currently a scratch note, not a source of truth for project setup.

## Change Guidance

- Prefer matching existing Laravel and Filament patterns already used in the repo.
- Keep validation in form requests or Filament schema definitions, depending on where the flow currently lives.
- When changing schema or data relationships, update migrations, seeders, and affected Filament relation managers together.
- When changing models used in admin tables/forms, verify impacted resources under `app/Filament/Resources`.
- Add or update PHPUnit coverage for behavior changes, especially around rendering, verification, and resource workflows.

## First Places To Inspect

- `composer.json` for scripts and PHP tooling
- `package.json` and `vite.config.js` for frontend tooling
- `app/Filament/Resources` for admin behavior
- `app/Models` and `database/migrations` for data model changes
- `tests/Feature` for existing behavioral coverage
