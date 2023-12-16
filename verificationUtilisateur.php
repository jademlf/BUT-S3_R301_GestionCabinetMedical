<?php
// Inclure le fichier de connexion à la base de données (connexion_bd.php)
include 'connexion_bd.php';
// Démarrez la session
session_start();

// Vérifiez si l'utilisateur est authentifié
if (!isset($_SESSION['id_utilisateur'])) {
    // Redirigez l'utilisateur vers la page de connexion s'il n'est pas authentifié
    header('Location: login.php');
    exit();
}
?>
