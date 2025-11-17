<?php
namespace MiniCrawler;

class Crawler
{
    protected $db;
    protected $logger;
    protected $parser;

    public function __construct(Database $db, Logger $logger)
    {
        $this->db = $db;
        $this->logger = $logger;
        $this->parser = new Parser($logger);
    }

    public function processUrl(string $url): void
    {
        $this->logger->info("Processing: $url");
        $content = $this->fetchWithRetry($url, 2);
        if ( $content === false ) {
            $this->logger->error("Failed to fetch: $url");
            return;
        }

        try {
            $data = $this->parser->parse($content, $url);
        } catch (\Exception $e) {
            $this->logger->error("Parse error for $url: " . $e->getMessage());
            return;
        }

        // Validate fields and log missing ones
        $missing = [];
        foreach (['title','price','availability'] as $f) {
            if (empty($data[$f])) $missing[] = $f;
        }
        if (!empty($missing)) {
            $this->logger->warning(sprintf('Missing fields for %s: %s', $url, implode(', ', $missing)));
        }

        $this->db->saveProduct($url, $data);
    }

    protected function fetchWithRetry(string $url, int $attempts = 2)
    {
        $try = 0;
        while ( $try < $attempts) {
            $try++;
            $result = $this->fetch($url);
            if ($result !== false) return $result;
            $this->logger->warning("Fetch failed (attempt $try) for $url");
            // small backoff
            sleep(1);
        }
        return false;
    }

    protected function fetch(string $url)
    {
        // Use cURL
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_USERAGENT, 'MiniCrawler/1.0 (+https://example.local)');
        
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); // disable host check
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // disable peer verification
        
        $data = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        if ($data === false || $code >= 400) {
            $this->logger->info("HTTP code: $code; curl_err: $err");
            return false;
        }
        return $data;
    }
}
