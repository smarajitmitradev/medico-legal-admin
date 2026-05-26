<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Backup Test Running...</h2>";  

echo "<pre>";

echo "Current DIR: " . __DIR__ . "\n";

$envPath = dirname(__DIR__) . '/.env';

echo "ENV Path: " . $envPath . "\n";

if(file_exists($envPath)) {
    echo ".env FOUND\n";
} else {
    echo ".env NOT FOUND\n";
}

$backupDir = dirname(__DIR__) . '/storage/backups';

echo "Backup Dir: " . $backupDir . "\n";

if(!file_exists($backupDir)) {

    mkdir($backupDir, 0755, true);

    echo "Backup directory created\n";
}

file_put_contents(
    $backupDir . '/test.txt',
    'Cron Working : ' . date('Y-m-d H:i:s')
);

echo "Test file created successfully\n";

echo "</pre>";