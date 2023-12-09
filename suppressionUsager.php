<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Suppression d'Usager</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
<?php include 'menu.php'; 
    
    include 'connexion_bd.php';

    if (isset($_GET['id'])) {
        // Récupérer l'identifiant de l'usager depuis l'URL
        $id = $_GET['id'];

        // Sélectionner les informations de l'usager correspondant dans la base de données
        $query = $linkpdo->prepare('SELECT * FROM Usagers WHERE ID_Usager = :id');
        $query->execute(array('id' => $id));
        $usager = $query->fetch();

        if ($usager) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Si le formulaire a été soumis
                if (isset($_POST['confirmation']) && ($_POST['confirmation'] === 'oui' || $_POST['confirmation'] === 'non')) {
                    // Si la confirmation est "oui", supprimer l'usager
                    if ($_POST['confirmation'] === 'oui') {
                        try {
                            // Supprimer les rendez-vous associés à l'usager
                            $deleteRendezVousQuery = $linkpdo->prepare('DELETE FROM RendezVous WHERE ID_Usager = :id');
                            $deleteRendezVousQuery->execute(array('id' => $id));

                            // Supprimer l'usager
                            $deleteQuery = $linkpdo->prepare('DELETE FROM Usagers WHERE ID_Usager = :id');
                            $deleteQuery->execute(array('id' => $id));

                            // Rediriger vers la page rechercheUsager.php (ou la page appropriée)
                            header('Location: rechercheUsager.php');
                            exit();
                        } catch (PDOException $e) {
                            echo 'Erreur lors de la suppression : ' . $e->getMessage();
                        }
                    } else {
                        // Si la confirmation est "non", rediriger vers la page rechercheUsager.php sans supprimer l'usager
                        header('Location: rechercheUsager.php');
                        exit();
                    }
                }
            }

            // Afficher un message de confirmation avec des boutons radio stylisés
            echo '<h1>Confirmation de suppression</h1>';
            echo '<p>Êtes-vous sûr de vouloir supprimer l\'usager suivant ?</p>';
            echo '<p>Nom : ' . $usager['Nom'] . '</p>';
            echo '<p>Prénom : ' . $usager['Prénom'] . '</p>';
            echo '<form method="POST" action="suppressionUsager.php?id=' . $id . '">';
            echo '<div>';
            echo '<label for="oui">Oui</label>';
            echo '<input type="radio" name="confirmation" id="oui" value="oui" required>';
            echo '</div>';
            echo '<div>';
            echo '<label for="non">Non</label>';
            echo '<input type="radio" name="confirmation" id="non" value="non" required>';
            echo '</div>';
            echo '<input type="submit" value="Valider">';
            echo '</form>';
        } else {
            echo '<h1>Usager introuvable</h1>';
            echo '<p>L\'usager spécifié n\'existe pas.</p>';
        }
    } else {
        echo '<h1>Identifiant non spécifié</h1>';
        echo '<p>Aucun identifiant d\'usager spécifié.</p>';
    }
    ?>
</body>
</html>
