# Mini PHP Web Crawler with SQLite


## Requirements
- PHP 8+ / Web Server
- CLI access
- Composer (to install `simplehtmldom`)

## Setup

```bash
# 1. clone or unzip the repo
cd mini-crawler

# 2. install dependencies
composer install

```

## Usage

```bash
# Use the provided sample URLs
php app.php urls.txt

# Or run and write results to a custom DB/log:
php app.php urls.txt products.sqlite log.txt
```

## What it does
- Reads a list of product URLs (from `urls.txt` or hardcoded)
- Fetches each URL using cURL (with one retry on failure)
- Parses HTML with **simple_html_dom** to extract:
  - Product title
  - Price (with currency symbol)
  - Availability
- Saves results into a SQLite database (`products.sqlite`)
- Logs warnings and failed requests into `log.txt`

## Files to view / edit
- `urls.txt` - Sample URLs list
- `products.sqlite` - SQLite DB
- `log.txt` - Log file

## Notes
The parser uses flexible CSS selectors and fallback heuristics, but the collection of arbitrary websites is not reliable. 
We must adjust selectors in `src/Parser.php` for every target site(s).

