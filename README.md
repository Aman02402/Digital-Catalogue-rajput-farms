# Digital Catalogue – Rajput Farms

This repository contains a PHP-powered indoor plant catalogue for Rajput Farms featuring more than two hundred
curated plant profiles. Every listing details key uses, placement concepts, and climate suitability along with an
on-brand hosted illustration so the experience can run completely offline once deployed to a PHP server.

## Stack

- PHP 8+ with the built-in JSON and file APIs for data persistence
- Vanilla JavaScript front-end with progressive enhancement and modular filtering
- Responsive CSS powered by custom properties and modern layout primitives
- Static SVG imagery hosted under `public/images`

## Local development

1. Install PHP 8 or later.
2. Start the built-in PHP development server from the project root:

   ```bash
   php -S localhost:8080 -t public
   ```

3. Visit `http://localhost:8080` to explore the catalogue UI.

The API endpoints are exposed at:

- `GET /api/plants.php` – Paginated catalogue query with search & filters
- `POST /api/plants.php` – Submit a new plant entry saved to `data/plants.json`
- `GET /api/summary.php` – Summary statistics for the hero metrics

## Data persistence

The catalogue data is stored inside `data/plants.json`. New submissions append to this file through a safe
temporary-write workflow that avoids corruption.

## Imagery

Due to the execution environment used for development, external HTTP downloads are blocked. Placeholder
illustrations were generated and stored in `public/images`. Replace them with photographic assets by copying your
own JPG/PNG files into the same folder – the application automatically maps files when their names match plant
slugs (e.g. `areca-palm.jpg`).
