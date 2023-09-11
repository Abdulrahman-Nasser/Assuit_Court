<?php
$null = null;
if (isset($_POST['export'])) {
    // Database configuration
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $databaseName = 'court'; // Database name - Modify if needed

    // Create a connection to MySQL
    $mysqli = new mysqli($host, $username, $password, $databaseName);
    if ($mysqli->connect_error) {
        die('Connection failed: ' . $mysqli->connect_error);
    }

    // Get the current date and time

    $filename =  "court_" . date('Y:m:d') . ".sql";

    // Get the server and database information
    $serverVersion = $mysqli->server_info;
    $phpVersion = phpversion();

    // Start generating the SQL dump
    $dump = "-- phpMyAdmin SQL Dump\n";
    $dump .= "-- version 5.2.1\n";
    $dump .= "-- https://www.phpmyadmin.net/\n";
    $dump .= "--\n";
    $dump .= "-- Host: 127.0.0.1\n";
    $dump .= "-- Server version: $serverVersion\n";
    $dump .= "-- PHP Version: $phpVersion\n";
    $dump .= "\n";
    $dump .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
    $dump .= "START TRANSACTION;\n";
    $dump .= "SET time_zone = \"+00:00\";\n";
    $dump .= "\n";

    // Create the 'admins' table
    $dump .= "--\n";
    $dump .= "-- Table structure for table `admins`\n";
    $dump .= "--\n";
    $dump .= "\n";
    $dump .= "CREATE TABLE `admins` (\n";
    $dump .= "  `id` int(11) NOT NULL AUTO_INCREMENT,\n";
    $dump .= "  `name` varchar(40) NOT NULL,\n";
    $dump .= "  `password` text NOT NULL,\n";
    $dump .= "  `role_id` int(11) NOT NULL DEFAULT 1,\n";
    $dump .= "  `department_role_id` int(11) NOT NULL DEFAULT 1,\n";
    $dump .= "  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),\n";
    $dump .= "  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),\n";
    $dump .= "  PRIMARY KEY (`id`),\n";
    $dump .= "  KEY `role_id` (`role_id`),\n";
    $dump .= "  KEY `department_role_id` (`department_role_id`)\n";
    $dump .= ") ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;\n";
    $dump .= "\n";

    // Get the table structures and data
    $result = $mysqli->query("SHOW FULL TABLES");
    while ($row = $result->fetch_assoc()) {
        $table = $row['Tables_in_' . $databaseName];

        if ($row['Table_type'] == 'VIEW') {
        } else {

            // Get the table structure
            $structureQuery = $mysqli->query("SHOW CREATE TABLE `$table`");
            $structureRow = $structureQuery->fetch_row();

            $dump .= "--\n";
            $dump .= "-- Table structure for table `$table`\n";
            $dump .= "--\n";
            $dump .= "\n";

            // Skip adding the foreign key constraints for the 'admins' table
            if ($table !== 'admins') {
                $dump .= $structureRow[1] . ";\n";
            }

            $dump .= "\n";

            // Get the table data
            $dataQuery = $mysqli->query("SELECT * FROM `$table`");
            if ($dataQuery->num_rows > 0) {
                $dump .= "--\n";
                $dump .= "-- Dumping data for table `$table`\n";
                $dump .= "--\n";
                $dump .= "\n";

                while ($row = $dataQuery->fetch_assoc()) {
                    $dump .= "INSERT INTO `$table` VALUES (";
                    $values = array_map(function ($value) use ($mysqli) {
                        if (is_null($value)) {
                            return 'NULL';
                        } else {
                            return "'" . $mysqli->real_escape_string($value) . "'";
                        }
                    }, array_values($row));
                    $dump .= implode(', ', $values);
                    $dump .= ");\n";
                }

                $dump .= "\n";
            }
        }
    }

    // Add the view creation at the end of the file
    $dump .= "--\n";
    $dump .= "-- DROP TABLE IF EXISTS `admin_details`;\n";
    $dump .= "--\n";
    $dump .= "CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `admin_details` AS SELECT `admins`.`id` AS `id`, `admins`.`name` AS `name`, `admins`.`password` AS `password`, `roles`.`description` AS `description`, `departmen_role`.`role` AS `department_description`, `admins`.`role_id` AS `role_id`, `admins`.`department_role_id` AS `department_role_id` FROM ((`admins` join `roles`) join `departmen_role` on(`admins`.`role_id` = `roles`.`id` and `admins`.`department_role_id` = `departmen_role`.`id`));\n";
    $dump .= "\n";


    // Add the foreign key constraints for the admins table at the end of the file
    $dump .= "--\n";
    $dump .= "-- ALTER TABLE `admins`\n";
    $dump .= "--\n";
    $dump .= "ALTER TABLE `admins` ADD CONSTRAINT `admins_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`),\n";
    $dump .= "ADD CONSTRAINT `admins_ibfk_2` FOREIGN KEY (`department_role_id`) REFERENCES `departmen_role` (`id`);\n";

    // Commit the transaction and output the SQL dump
    $dump .= "COMMIT;\n";

    // Prompt download for the exported SQL file
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    echo $dump;
    exit;
}
