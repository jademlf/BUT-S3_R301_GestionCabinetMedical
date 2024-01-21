<?php include 'verificationUtilisateur.php'; ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Suppression du Rendez-vous</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link rel="stylesheet" type="text/css" href="menu.css">
</head>
<body>
    <?php 
        include 'menu.php'; // Menu de navigation
        include 'connexion_bd.php'; // Connexion à la BD

        if (isset($_GET['id'])) {
            // Récupérer l'identifiant du rendez-vous depuis l'URL
            $id = $_GET['id'];

            // Sélectionner les informations du rendez-vous correspondant dans la base de données
            $query = $linkpdo->prepare('SELECT * FROM rendezVous WHERE ID_RendezVous = :id');
            $query->execute(array('id' => $id));
            $rendezVous = $query->fetch();

            if ($rendezVous) {
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    // Si le formulaire a été soumis
                    if (isset($_POST['confirmation']) && ($_POST['confirmation'] === 'oui' || $_POST['confirmation'] === 'non')) {
                        // Si la confirmation est "oui", supprimer le rendez-vous
                        if ($_POST['confirmation'] === 'oui') {
                            try {
                                // Supprimer le rendez-vous
                                $deleteQuery = $linkpdo->prepare('DELETE FROM rendezVous WHERE ID_RendezVous = :id');
                                $deleteQuery->execute(array('id' => $id));

                                // Rediriger vers la page appropriée (par exemple, la liste des rendez-vous)
                                header('Location: affichageRendezVous.php');
                                exit();
                            } catch (PDOException $e) {
                                echo 'Erreur lors de la suppression : ' . $e->getMessage();
                            }
                        } else {
                            // Si la confirmation est "non", rediriger vers la page appropriée sans supprimer le rendez-vous
                            header('Location: affichageRendezVous.php');
                            exit();
                        }
                    }
                }

                // Afficher un message de confirmation avec des boutons radio stylisés
                echo '<h1>Confirmation de suppression</h1>';
                echo '<section><p><b>Êtes-vous sûr de vouloir supprimer le rendez-vous suivant ?</p></b>';
                echo '<p>Date de consultation : ' . date('d/m/Y', strtotime($rendezVous['DateConsultation'])) . '</p>';
                echo '<p>Heure de consultation : ' . $rendezVous['HeureConsultation'] . '</p>';
                echo '<p>Durée (minutes) : ' . $rendezVous['DuréeConsultation'] . '</p>';
                echo '<form method="POST" action="suppressionRDV.php?id=' . $id . '">';
                echo '<div>';

                echo '<label for="oui">Oui</label>';
                echo '<input type="radio" name="confirmation" id="oui" value="oui" required>';
                echo '</div>';
                echo '<div>';
                echo '<label for="non">Non</label>';
                echo '<input type="radio" name="confirmation" id="non" value="non" required>';
                echo '</div>';

                echo '<input type="submit" value="Valider">';
                echo '</form></section>';
            } else {
                echo '<h1>Rendez-vous introuvable</h1>';
                echo '<section><p>Le rendez-vous spécifié n\'existe pas.</p></section>';
            }
        } else {
            echo '<h1>Identifiant non spécifié</h1>';
            echo '<section><p>Aucun identifiant de rendez-vous spécifié.</p></section>';
        }
    ?>
</body>
</html>
