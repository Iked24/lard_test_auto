<?php
session_start();

function getDbConnection() {
    $host = 'postgres';
    $dbname = getenv('POSTGRES_DB');
    $username = getenv('POSTGRES_USER');
    $password = getenv('POSTGRES_PASSWORD');

    try {
        $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}