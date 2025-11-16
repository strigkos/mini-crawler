#!/usr/bin/env php
<?php
require __DIR__ . '/vendor/autoload.php';

use MiniCrawler\Crawler;
use MiniCrawler\Database;
use MiniCrawler\Logger;

$argc = $_SERVER['argc'];
$argv = $_SERVER['argv'];

if ($argc < 2) {
    echo "Usage: php fetcher.php urls.txt [products.sqlite] [log.txt]\n";
    exit(1);
}

$urlsFile = $argv[1];
$dbFile = $argv[2] ?? 'products.sqlite';
$logFile = $argv[3] ?? 'log.txt';

$logger = new Logger($logFile);
$db = new Database($dbFile, $logger);
$crawler = new Crawler($db, $logger);

$urls = [];
if (!file_exists($urlsFile)) {
    $logger->error("URLs file not found: $urlsFile");
    exit(1);
}
$lines = file($urlsFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($lines as $line) {
    $line = trim($line);
    if ($line === '' || strpos($line, '#') === 0) continue;
    $urls[] = $line;
}

if (empty($urls)) {
    $logger->warning('No URLs to process.');
    exit(0);
}

foreach ($urls as $url) {
    $crawler->processUrl($url);
}

echo "Done. Saved to: $dbFile. Logs: $logFile\n";
