<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

date_default_timezone_set('Asia/Kolkata');

/*
|--------------------------------------------------------------------------
| READ ENV
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
| DATABASE CONFIG
|--------------------------------------------------------------------------
*/

$host = envValue('DB_HOST', '127.0.0.1');
$port = envValue('DB_PORT', '3306');
$db   = envValue('DB_DATABASE');
$user = envValue('DB_USERNAME');
$pass = envValue('DB_PASSWORD');

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

$fileName = $backupDir . "/{$db}_{$date}.sql";

/*
|--------------------------------------------------------------------------
| CONNECT DATABASE
|--------------------------------------------------------------------------
*/

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8");

/*
|--------------------------------------------------------------------------
| GENERATE SQL
|--------------------------------------------------------------------------
*/

$sqlScript = "";

/*
|--------------------------------------------------------------------------
| GET TABLES
|--------------------------------------------------------------------------
*/

$tables = [];

$result = $conn->query("SHOW TABLES");

while ($row = $result->fetch_row()) {
    $tables[] = $row[0];
}

foreach ($tables as $table) {

    /*
    |--------------------------------------------------------------------------
    | TABLE STRUCTURE
    |--------------------------------------------------------------------------
    */

    $createTable = $conn->query("SHOW CREATE TABLE `$table`")->fetch_row();

    $sqlScript .= "\n\n" . $createTable[1] . ";\n\n";

    /*
    |--------------------------------------------------------------------------
    | TABLE DATA
    |--------------------------------------------------------------------------
    */

    $rows = $conn->query("SELECT * FROM `$table`");

    while ($row = $rows->fetch_assoc()) {

        $columns = array_keys($row);

        $values = array_map(function ($value) use ($conn) {

            if ($value === null) {
                return "NULL";
            }

            return "'" . $conn->real_escape_string($value) . "'";

        }, array_values($row));

        $sqlScript .= "INSERT INTO `$table` (`"
            . implode('`,`', $columns)
            . "`) VALUES ("
            . implode(',', $values)
            . ");\n";
    }
}

/*
|--------------------------------------------------------------------------
| SAVE SQL FILE
|--------------------------------------------------------------------------
*/

file_put_contents($fileName, $sqlScript);

/*
|--------------------------------------------------------------------------
| COMPRESS
|--------------------------------------------------------------------------
*/

$gzFile = $fileName . '.gz';

$fp = gzopen($gzFile, 'w9');

gzwrite($fp, file_get_contents($fileName));

gzclose($fp);

unlink($fileName);

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

echo "Database backup completed successfully.";