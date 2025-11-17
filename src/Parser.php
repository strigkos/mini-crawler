<?php
namespace MiniCrawler;

use simplehtmldom\HtmlWeb;
use simplehtmldom\HtmlDocument;

class Parser
{
    protected $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Parse HTML and return associative array with title, price (raw), availability.
     */
    public function parse(string $html, string $url): array
    {
        // Using simple_html_dom (composer package simplehtmldom)
        $dom = new HtmlDocument();
        $dom->load($html);

        // Heuristics - try common selectors; you should adjust these per site
        $title = $this->firstText($dom, [
            'title',
            'meta[property="og:title"]', 
            '.product-title',
            '[itemprop="name"]',
            'h1',
        ]);

        $price = $this->firstText($dom, [
            '.price',
            '.product-price',
            '.woocommerce-Price-amount',
            '[class*=\"price\"]',
            '[itemprop="price"]'
        ]);

        $availability = $this->firstText($dom, [
            '.availability',
            '.stock',
            '.product-stock',
            '[class*=\"availability\"]',
            '[itemprop="availability"]',
        ]);

        // Clean up
        $title = $this->clean($title);
        $price = $this->clean($price);
        $availability = $this->clean($availability);

        return [
            'title' => $title,
            'price' => $price,
            'availability' => $availability ?: 'In Stock',
            'scraped_at' => date('c'),
        ];
    }

    protected function firstText(HtmlDocument $dom, array $selectors)
    {
        foreach ($selectors as $sel) {
            // handle meta selectors specially
            if (strpos($sel, 'meta') === 0 || strpos($sel, 'meta[') === 0 || strpos($sel, 'meta') !== false && strpos($sel, 'og:') !== false) {
                // try og meta property
                $m = $dom->find('meta[property="og:title"]', 0);
                if ($m && isset($m->content)) return $m->content;
            }
            $el = $dom->find($sel, 0);
            if ($el) {
                // for elements like price that might be nested
                return $el->plaintext;
            }
        }
        return '';
    }

    protected function clean($text)
    {
        $text = trim($text);
        // normalize whitespace
        $text = preg_replace('/\s+/u', ' ', $text);
        return $text;
    }
}
