<?php

file_put_contents(
    dirname(__DIR__) . '/storage/backups/cron-test.txt',
    "Cron executed at: " . date('Y-m-d H:i:s') . PHP_EOL,
    FILE_APPEND
);

echo "Cron Test Success";