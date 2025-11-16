<?php
namespace MiniCrawler;

class Logger
{
    protected $file;

    public function __construct(string $file)
    {
        $this->file = $file;
        // create file if not exists
        if (!file_exists($this->file)) {
            file_put_contents($this->file, '');
        }
    }

    protected function write(string $level, string $message)
    {
        $line = sprintf("[%s] %s: %s\n", date('c'), strtoupper($level), $message);
        file_put_contents($this->file, $line, FILE_APPEND | LOCK_EX);
    }

    public function info(string $msg) { $this->write('info', $msg); }
    public function warning(string $msg) { $this->write('warning', $msg); }
    public function error(string $msg) { $this->write('error', $msg); }
}
