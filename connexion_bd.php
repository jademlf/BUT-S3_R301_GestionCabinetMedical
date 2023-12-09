<?php
$server = 'localhost';
$login = 'root';
$mdp = '';
$db = 'projet_php';

try {
    $linkpdo = new PDO("mysql:host=$server;dbname=$db;charset=utf8", $login, $mdp);
} catch (PDOException $e) {
    die('Erreur : ' . $e->getMessage());
}
?>
