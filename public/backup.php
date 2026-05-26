<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

date_default_timezone_set('Asia/Kolkata');

/*
|--------------------------------------------------------------------------
| READ ENV VALUES
|--------------------------------------------------------------------------
*/

function envValue($key, $default = null)
{
    $path = dirname(__DIR__) . '/.env';

    if (!file_exists($path)) {
        return $default;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {

        $line = trim($line);

        if (strpos($line, '#') === 0) {
            continue;
        }

        if (!strpos($line, '=')) {
            continue;
        }

        list($envKey, $value) = explode('=', $line, 2);

        if (trim($envKey) === $key) {
            return trim($value, "\"'");
        }
    }

    return $default;
}

/*
|--------------------------------------------------------------------------
| DATABASE DETAILS
|--------------------------------------------------------------------------
*/

$host = envValue('DB_HOST', '127.0.0.1');
$port = envValue('DB_PORT', '3306');
$user = envValue('DB_USERNAME');
$pass = envValue('DB_PASSWORD');
$db   = envValue('DB_DATABASE');

/*
|--------------------------------------------------------------------------
| BACKUP DIRECTORY
|--------------------------------------------------------------------------
*/

$backupDir = dirname(__DIR__) . '/storage/backups';

if (!file_exists($backupDir)) {
    mkdir($backupDir, 0755, true);
}

/*
|--------------------------------------------------------------------------
| FILE NAME
|--------------------------------------------------------------------------
*/

$date = date('Y-m-d_H-i-s');

$sqlFile = $backupDir . "/{$db}_{$date}.sql";

/*
|--------------------------------------------------------------------------
| MYSQLDUMP COMMAND
|--------------------------------------------------------------------------
*/

$command = "mysqldump "
    . "--host={$host} "
    . "--port={$port} "
    . "--user={$user} "
    . "--password={$pass} "
    . "{$db} > {$sqlFile}";

/*
|--------------------------------------------------------------------------
| EXECUTE
|--------------------------------------------------------------------------
*/

exec($command . " 2>&1", $output, $result);

/*
|--------------------------------------------------------------------------
| CHECK RESULT
|--------------------------------------------------------------------------
*/

echo "<pre>";

if ($result === 0 && file_exists($sqlFile)) {

    echo "Database backup created successfully.\n";

    /*
    |--------------------------------------------------------------------------
    | COMPRESS FILE
    |--------------------------------------------------------------------------
    */

    $gzFile = $sqlFile . '.gz';

    $fp = gzopen($gzFile, 'w9');

    gzwrite($fp, file_get_contents($sqlFile));

    gzclose($fp);

    unlink($sqlFile);

    echo "Compressed successfully.\n";

    /*
    |--------------------------------------------------------------------------
    | DELETE OLD FILES > 7 DAYS
    |--------------------------------------------------------------------------
    */

    foreach (glob($backupDir . '/*.gz') as $file) {

        if (time() - filemtime($file) > (7 * 24 * 60 * 60)) {

            unlink($file);
        }
    }

    echo "Old backups cleaned.\n";

    echo "Backup completed successfully.";

} else {

    echo "Backup failed.\n\n";

    print_r($output);
}

echo "</pre>";