<?php
namespace MiniCrawler;

use PDO;
use PDOException;

class Database
{
    protected $pdo;
    protected $logger;

    public function __construct(string $file, Logger $logger)
    {
        $this->logger = $logger;
        try {
            $this->pdo = new PDO('sqlite:' . $file);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->init();
        } catch (PDOException $e) {
            $this->logger->error('DB error: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function init()
    {
        $sql = "CREATE TABLE IF NOT EXISTS products (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            url TEXT UNIQUE,
            title TEXT,
            price TEXT,
            availability TEXT,
            scraped_at TEXT
        );";
        $this->pdo->exec($sql);
    }

    public function saveProduct(string $url, array $data)
    {
        $sql = 'INSERT OR REPLACE INTO products (url, title, price, availability, scraped_at)
                VALUES (:url, :title, :price, :availability, :scraped_at)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':url' => $url,
            ':title' => $data['title'] ?? '',
            ':price' => $data['price'] ?? '',
            ':availability' => $data['availability'] ?? '',
            ':scraped_at' => $data['scraped_at'] ?? date('c'),
        ]);
        $this->logger->info("Saved product: $url");
    }
}
