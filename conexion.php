<?php
function getPDO() {
    $config = require 'config.php';

    $dsn = "mysql:host={$config['host']};dbname={$config['db']};charset={$config['charset']}";

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        return new PDO($dsn, $config['user'], $config['pass'], $options);
    } catch (PDOException $e) {
        echo 'Error de conexión: ' . $e->getMessage();
        exit;
    }
}
