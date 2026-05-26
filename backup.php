<?php

/*
|--------------------------------------------------------------------------
| Laravel Database Backup Script
|--------------------------------------------------------------------------
| - Takes automatic database backup
| - Stores inside storage/backups
| - Keeps only last 7 days backups
| - Uses Laravel .env credentials
|--------------------------------------------------------------------------
*/

date_default_timezone_set('Asia/Kolkata');

/*
|--------------------------------------------------------------------------
| READ .ENV VALUES
|--------------------------------------------------------------------------
*/

function envValue($key, $default = null)
{
    $path = __DIR__ . '/.env';

    if (!file_exists($path)) {
        return $default;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {

        $line = trim($line);

        // Skip comments
        if (strpos($line, '#') === 0) {
            continue;
        }

        // Skip invalid lines
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
| DATABASE DETAILS FROM .ENV
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

$backupDir = __DIR__ . '/storage/backups';

/*
|--------------------------------------------------------------------------
| CREATE BACKUP DIRECTORY
|--------------------------------------------------------------------------
*/

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

$gzFile  = $sqlFile . '.gz';

/*
|--------------------------------------------------------------------------
| MYSQL DUMP COMMAND
|--------------------------------------------------------------------------
*/

$command = sprintf(
    'mysqldump --host=%s --port=%s --user=%s --password=%s %s > %s',
    escapeshellarg($host),
    escapeshellarg($port),
    escapeshellarg($user),
    escapeshellarg($pass),
    escapeshellarg($db),
    escapeshellarg($sqlFile)
);

/*
|--------------------------------------------------------------------------
| EXECUTE BACKUP
|--------------------------------------------------------------------------
*/

system($command, $result);

/*
|--------------------------------------------------------------------------
| VERIFY BACKUP
|--------------------------------------------------------------------------
*/

if ($result === 0 && file_exists($sqlFile)) {

    /*
    |--------------------------------------------------------------------------
    | COMPRESS SQL FILE
    |--------------------------------------------------------------------------
    */

    $sqlContent = file_get_contents($sqlFile);

    file_put_contents(
        $gzFile,
        gzencode($sqlContent, 9)
    );

    /*
    |--------------------------------------------------------------------------
    | REMOVE RAW SQL FILE
    |--------------------------------------------------------------------------
    */

    unlink($sqlFile);

    /*
    |--------------------------------------------------------------------------
    | DELETE FILES OLDER THAN 7 DAYS
    |--------------------------------------------------------------------------
    */

    $files = glob($backupDir . '/*.gz');

    foreach ($files as $file) {

        if (is_file($file)) {

            $fileAge = time() - filemtime($file);

            // 7 days
            if ($fileAge > (7 * 24 * 60 * 60)) {

                unlink($file);
            }
        }
    }

    echo "Backup completed successfully.";

} else {

    echo "Backup failed.";
}
?>