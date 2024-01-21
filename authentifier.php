<?php
// Inclure la connexion à la base de données
include 'connexion_bd.php';

// Vérifier si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $nom_utilisateur = $_POST['nom_utilisateur'];
    $mot_de_passe = $_POST['mot_de_passe'];

    // Vérifier les informations d'authentification
    $verifierUtilisateur = $linkpdo->prepare('SELECT * FROM Utilisateurs WHERE NomUtilisateur = :nom_utilisateur AND MotDePasse = :mot_de_passe');
    $verifierUtilisateur->execute(['nom_utilisateur' => $nom_utilisateur, 'mot_de_passe' => $mot_de_passe]);

    // Si l'utilisateur est authentifié
    if ($verifierUtilisateur->rowCount() > 0) {
        // Démarrer la session
        session_start();

        // Enregistrer l'ID de l'utilisateur dans la session
        $_SESSION['id_utilisateur'] = $verifierUtilisateur->fetch()['ID_Utilisateur'];

        // Rediriger vers la page protégée
        header('Location: index.php');
        exit();
    } else {
        // Afficher un message d'erreur si l'authentification échoue
        echo 'Nom d\'utilisateur ou mot de passe incorrect.';
    }
}
?>