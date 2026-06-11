<?php
try {
    $pdo = new PDO('mysql:host=83.228.206.120;port=3306;dbname=supermarche_db', 'supermarche_user', 'SuperMarcheSecureDB2026!');
    echo 'Connected successfully!';
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
