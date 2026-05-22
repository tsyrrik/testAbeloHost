# Blog (PHP + Smarty + MySQL)

A small blog with categories and posts. Test assignment — pure PHP 8.1+, MySQL,
Smarty, no frameworks. Dockerized.

## Running it

```bash
cp .env.example .env
make up
make composer
make migrate
make seed-fresh
```

Then http://localhost:8080/.

Styles need a one-time `npm install` on the host (Dart Sass runs there, not in
the container), then `npm run scss`.

## Layout

- `public/index.php` — front controller, three routes wired up here.
- `src/Core/` — Container, Router, View (Smarty wrapper), PDO factory.
- `src/Controller/` — one per page: Home, Category, Article.
- `src/Repository/` — all SQL lives here.
- `src/Database/Migrations/` — plain `.sql`, applied by `bin/console migrate`.
- `src/Database/Seeders/` — fixture data for categories and articles.
- `templates/` — Smarty layout, pages, partials.
- `assets/scss/` — stylesheet sources.
- `tests/` — PHPUnit, two suites.

Routes: `/`, `/category/{slug}`, `/article/{slug}`.

## CLI

```bash
php bin/console migrate
php bin/console seed
php bin/console seed --fresh    # truncates first
```

Shortcuts: `make migrate`, `make seed`, `make seed-fresh`.

## Tests

Run them inside the PHP container — integration tests reach MySQL by service
hostname:

```bash
make test
```

Unit tests cover the paginator, the sanitizer, and the sort whitelist.
Integration tests run against a separate `blog_test` database (created by the
MySQL init script) and exercise `paginateByCategory` and `findRelated` against
a small fixture.

If `blog_test` doesn't exist yet (the MySQL volume might predate the init
script), do `make reset` once before `make test`. That wipes the dev database
too, so `make seed-fresh` after.

## AI usage

I used Claude to draft the initial Smarty templates and SCSS, and reworked the
layout by hand to match the brief's mockup. PHP, SQL, migrations, sanitizer,
seeders, and tests are my own work.
