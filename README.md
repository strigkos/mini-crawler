# Mini PHP Web Crawler with SQLite

## What it does
- Reads a list of product URLs (from `urls.txt` or hardcoded)
- Fetches each URL using cURL (with one retry on failure)
- Parses HTML with **simple_html_dom** to extract:
  - Product title
  - Price (with currency symbol)
  - Availability
- Saves results into a SQLite database (`products.sqlite`)
- Logs warnings and failed requests into `log.txt`

## Requirements
- PHP 8+
- Composer (to install `simplehtmldom`)
- CLI access

## Setup

```bash
# 1. clone or unzip the repo
cd mini-php-crawler

# 2. install dependencies
composer install

# 3. make script executable (optional)
chmod +x fetcher.php
```

## Usage

```bash
# Use the provided sample URLs
php fetcher.php urls.txt

# Or run and write results to a custom DB/log:
php fetcher.php urls.txt products.sqlite log.txt
```

## Files of interest
- `fetcher.php` - CLI entry point
- `src/` - PHP classes (Crawler, Parser, Database, Logger)
- `urls.txt` - sample URLs (replace with your list)
- `products.sqlite` - sample SQLite DB (generated)
- `log.txt` - sample log file

## Notes
- The parser uses flexible CSS selectors and fallback heuristics, but scraping arbitrary sites is inherently brittle. Adjust selectors in `src/Parser.php` for your target site(s).
- If a site requires JS to render price or availability, consider using an external Puppeteer script (the code includes comments on how to call one via `shell_exec`).

