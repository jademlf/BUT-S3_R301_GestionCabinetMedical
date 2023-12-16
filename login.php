<?php
// Inclure le fichier de connexion à la base de données (connexion_bd.php)
include 'connexion_bd.php';

// Vérifier si l'utilisateur est déjà connecté en vérifiant la présence de la variable de session
session_start();
if (isset($_SESSION['id_utilisateur'])) {
    // Si l'utilisateur est déjà connecté, rediriger vers index.php
    header('Location: index.php');
    exit();
}

// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Appeler votre script d'authentification (authentifier.php ou autre)
    include 'authentifier.php'; // Assurez-vous d'adapter le nom du fichier selon votre structure
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <link rel="stylesheet" type="text/css" href="connexion.css">
</head>
<body>
    <h1>Connexion</h1>
    <form action="login.php" method="post">
        <label for="nom_utilisateur">Nom d'utilisateur :</label>
        <input type="text" id="nom_utilisateur" name="nom_utilisateur" required><br>

        <label for="mot_de_passe">Mot de passe :</label>
        <input type="password" id="mot_de_passe" name="mot_de_passe" required><br>

        <input type="submit" value="Se connecter">
    </form>
</body>
</html>
