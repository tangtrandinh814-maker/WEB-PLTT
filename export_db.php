<?php
/**
 * Export MySQL database to SQL file using PHP PDO
 */

$host = '127.0.0.1';
$port = '3306';
$dbname = 'news_classifier_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage() . "\n");
}

$output = "";
$output .= "-- Database Export: $dbname\n";
$output .= "-- Date: " . date('Y-m-d H:i:s') . "\n";
$output .= "-- PHP PDO Export\n\n";
$output .= "SET NAMES utf8mb4;\n";
$output .= "SET FOREIGN_KEY_CHECKS = 0;\n";
$output .= "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n\n";

// Create database if not exists
$output .= "CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;\n";
$output .= "USE `$dbname`;\n\n";

// Get all tables
$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

foreach ($tables as $table) {
    echo "Exporting table: $table\n";

    // Drop table
    $output .= "-- ----------------------------\n";
    $output .= "-- Table structure for $table\n";
    $output .= "-- ----------------------------\n";
    $output .= "DROP TABLE IF EXISTS `$table`;\n";

    // Create table
    $createTable = $pdo->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_ASSOC);
    $output .= $createTable['Create Table'] . ";\n\n";

    // Get data
    $rows = $pdo->query("SELECT * FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);

    if (count($rows) > 0) {
        $output .= "-- ----------------------------\n";
        $output .= "-- Records of $table\n";
        $output .= "-- ----------------------------\n";

        // Batch insert for performance
        $columns = array_keys($rows[0]);
        $columnList = implode('`, `', $columns);

        $batchSize = 100;
        $batches = array_chunk($rows, $batchSize);

        foreach ($batches as $batch) {
            $output .= "INSERT INTO `$table` (`$columnList`) VALUES\n";
            $values = [];
            foreach ($batch as $row) {
                $rowValues = [];
                foreach ($row as $value) {
                    if ($value === null) {
                        $rowValues[] = 'NULL';
                    } else {
                        $rowValues[] = $pdo->quote($value);
                    }
                }
                $values[] = '(' . implode(', ', $rowValues) . ')';
            }
            $output .= implode(",\n", $values) . ";\n";
        }
        $output .= "\n";
    }
}

$output .= "SET FOREIGN_KEY_CHECKS = 1;\n";

$filepath = __DIR__ . '/database/news_classifier_db.sql';
file_put_contents($filepath, $output);

echo "\nDatabase exported successfully to: $filepath\n";
echo "File size: " . round(filesize($filepath) / 1024, 2) . " KB\n";
