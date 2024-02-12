<?php

namespace RealviaApi\Logger;

class Logger {

    private string $file;
    private string $fileName;
    private string $logDirectory;

    public function __construct($directory) {
        $this->logDirectory = $directory;
        $this->initLogFile();

    }

    public function initLogFile()
    {
        $dir = BASE_DIR . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $this->logDirectory) . DIRECTORY_SEPARATOR;
        $this->fileName = $dir . date('Y-m-d') . '.log';

        if(!is_dir($dir)) {
            mkdir($dir, 775, true);
        }

        if(!file_exists($this->fileName)) {
            file_put_contents($this->fileName, 'Log file from ' . date("Y-m-d") . PHP_EOL . "------------------------");
        }

        $this->file = file_get_contents($this->fileName);
    }

    public function log($data)
    {
        $this->file .= PHP_EOL . PHP_EOL . PHP_EOL;
        $this->file .= '[Time] ' . date("H:i") . PHP_EOL;
        $this->file .= $data . PHP_EOL;
        $this->file .= '---';
        file_put_contents($this->fileName, $this->file);
    }

    public function logRequest()
    {
        $data = '[METHOD] ' . $_SERVER['REQUEST_METHOD'] . PHP_EOL;
        $data .= '[POST Data] ' . PHP_EOL;
        $data .= var_export($_POST, true) . PHP_EOL;
        $data .= '[GET Data] ' . PHP_EOL;
        $data .= var_export($_GET, true);

        $this->log($data);
    }
}