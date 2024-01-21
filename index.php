<?php include 'verificationUtilisateur.php'; ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accueil</title>
    <link rel="stylesheet" type="text/css" href="menu.css">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link rel="stylesheet" type="text/css" href="index.css">
</head>
<body>
    <?php 
        include 'menu.php'; // Menu de navigation
    ?> 

    <div class="index-container">
        <h1>Gestion Cabinet Médical</h1>

        <div class="image-container">
            <img src="medecin_index.png" alt="Image du médecin">
        </div>

        <p>Bienvenue sur la plateforme de gestion de notre cabinet médical !</p>
        <p>Notre mission est de simplifier et d'optimiser la gestion quotidienne de votre cabinet médical,
            afin que vous puissiez vous concentrer pleinement sur ce qui compte le plus : la santé de vos patients.</p>

        <p>Notre système de gestion vous offre une solution complète, intuitive et efficace pour organiser vos rendez-vous,
            gérer les dossiers médicaux, et optimiser la coordination entre les membres de votre équipe médicale.</p>

        <p><strong>Ce que nous offrons :</strong></p>
        <ul>
            <li>Gestion des Rendez-vous Simplifiée</li>
            <li>Dossiers Médicaux Sécurisés</li>
            <li>Sécurité des Données</li>
        </ul>

        <p>Notre équipe est dédiée à vous fournir un support exceptionnel,
            et nous nous efforçons constamment d'améliorer nos services pour répondre aux besoins
            en constante évolution des professionnels de la santé.</p>
        <p>Découvrez dès maintenant comment notre plateforme peut simplifier la gestion de votre cabinet médical.</p>

        <a href="deconnexion.php"><input type="submit" value="Se déconnecter"></a>
    </div>

    <div class="index-container">
        <h2>Horaires d'ouverture</h2>
        <ul>
            <p><strong>Lundi :</strong> 8h00 - 19h00</p>
            <p><strong>Mardi :</strong> 8h00 - 19h00</p>
            <p><strong>Mercredi :</strong> 8h00 - 19h00</p>
            <p><strong>Jeudi :</strong> 8h00 - 19h00</p>
            <p><strong>Vendredi :</strong> 8h00 - 19h00</p>
            <p><strong>Samedi :</strong> Fermé</p>
            <p><strong>Dimanche :</strong> Fermé</p>
        </ul>
    </div>

    <div class="index-container">
        <h2>Contact d'urgence</h2>
        <p>En cas d'urgence, contactez 06 14 15 62 25 !</p>
    </div>

    <div class="index-container">
        <h2>Coordonnées</h2>
        <p>Numéro de téléphone : 06 01 02 03 04</p>
    </div>

</body>
</html>