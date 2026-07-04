<?php
$host     = 'localhost';
$port     = '5432';
$dbname   = 'dev';
$user     = 'postgres';
$password = 'Aissya';

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log('Erreur de connexion BDD : ' . $e->getMessage());
    die('Service momentanément indisponible. Veuillez réessayer plus tard.');
}
?>
