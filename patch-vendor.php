#!/usr/bin/env php
<?php
/**
 * Patch vendor/laravel/framework/config/database.php
 * Replace PDO::MYSQL_ATTR_SSL_CA and \Pdo\Mysql::ATTR_SSL_CA with integer 1012
 * This fixes "Class Pdo\Mysql not found" error on PHP < 8.4
 * and deprecation warnings on PHP >= 8.4
 *
 * This script runs automatically after every composer install/update.
 */

$file = __DIR__ . '/vendor/laravel/framework/config/database.php';

if (! file_exists($file)) {
    echo "  [Patch] vendor database.php not found, skipping.\n";
    return;
}

$content = file_get_contents($file);
$original = $content;

// Replace both PDO::MYSQL_ATTR_SSL_CA and \Pdo\Mysql::ATTR_SSL_CA with 1012
$content = str_replace('PDO::MYSQL_ATTR_SSL_CA', '1012', $content);
$content = str_replace('\Pdo\Mysql::ATTR_SSL_CA', '1012', $content);

if ($content !== $original) {
    file_put_contents($file, $content);
    echo "  [Patch] Fixed PDO::MYSQL_ATTR_SSL_CA -> 1012 in vendor/laravel/framework/config/database.php\n";
} else {
    echo "  [Patch] vendor database.php already clean.\n";
}
