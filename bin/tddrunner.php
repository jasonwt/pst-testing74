<?php

require_once __DIR__ . "/../vendor/autoload.php";

if (is_null($argv[1]))
    throw new Exception("No path provided");

$path = $argv[1];

/**
 * TDDRunner
 * 
 * @package 
 */
class TDDRunner {
    private string $rootPath = "";

    public function __construct(string $rootPath) {
        $rootPath = realpath(trim($rootPath));

        if ($rootPath === false)
            throw new Exception("Invalid path: $rootPath");

        $this->rootPath = $rootPath;

        $startTime = time();

        echo "\nStarted: " . date("Y-m-d H:i:s", $startTime) . "\n\n";

        echo "rootPath: $rootPath\n\n";

        $this->processPath($rootPath);

        $endTime = time();

        echo "\nFinished: " . date("Y-m-d H:i:s", $endTime) . "\n\n";
        echo "Duration: " . ($endTime - $startTime) . " seconds\n\n";
    }

    private function processPath(string $path) {
        $path = realpath(trim($path));

        // check for /vendor/ in path and return if found
        if (stripos($path, '/vendor/') !== false)
            return;
        
        if ($path === false)
            throw new Exception("Invalid path: $path");
    
        if (is_dir($path)) {
            $pathItems = scandir($path);
    
            foreach ($pathItems as $pathItem) {
                $fullPath = $path . "/" . $pathItem;
    
                if ($pathItem == "." || $pathItem == "..")
                    continue;
    
                if (!is_readable($path . "/" . $pathItem))
                    continue;
    
                $this->processPath($fullPath);
            }
        } else if (is_file($path)) {
            $pathInfo = pathinfo($path);
            $fileExtension = $pathInfo["extension"] ?? "";

            if (strtoupper($fileExtension) === "PHP") {
                $fileContents = file_get_contents($path);

                if (preg_match('/\/\*\s*TDD\s*\*\//', $fileContents) === 0)
                    return;
                
                echo "Testing: '" . substr($path, strlen($this->rootPath), ) . "'\n";

                include_once($path);
            } 
        }
    }
}

$runner = new TDDRunner($path);
