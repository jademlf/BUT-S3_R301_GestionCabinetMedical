<?php include 'verificationUtilisateur.php'; ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Suppression d'Usager</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link rel="stylesheet" type="text/css" href="menu.css">
</head>
<body>
    <?php
        include 'menu.php'; // Menu de navigation
        include 'connexion_bd.php'; // Connexion à la BD

        if (isset($_GET['id'])) {
            $id = $_GET['id']; // Récupérer l'id usager depuis l'URL

            // Sélectionner les informations de l'usager correspondant dans la base de données
            $query = $linkpdo->prepare('SELECT * FROM usagers WHERE ID_Usager = :id');
            $query->execute(array('id' => $id));
            $usager = $query->fetch();

            if ($usager) {
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    // Si le formulaire a été soumis
                    if (isset($_POST['confirmation']) && ($_POST['confirmation'] === 'oui' || $_POST['confirmation'] === 'non')) {
                        // Si oui, supprimer l'usager
                        if ($_POST['confirmation'] === 'oui') {
                            try {
                                // Supprimer les rendez-vous associés à l'usager
                                $deleteRendezVousQuery = $linkpdo->prepare('DELETE FROM rendezVous WHERE ID_Usager = :id');
                                $deleteRendezVousQuery->execute(array('id' => $id));
                                // Supprimer l'usager
                                $deleteQuery = $linkpdo->prepare('DELETE FROM usagers WHERE ID_Usager = :id');
                                $deleteQuery->execute(array('id' => $id));
                                // Rediriger vers la page rechercheUsager.php
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

                // Récupérer les détails du médecin référent
                $medecinReferentQuery = $linkpdo->prepare('SELECT Nom, Prénom FROM medecins WHERE ID_Medecin = :medecinId');
                $medecinReferentQuery->execute(array('medecinId' => $usager['MédecinRéférent']));
                $medecinReferentDetails = $medecinReferentQuery->fetch();

                // Affichez les infos de l'usager
                echo '<h1>Confirmation de suppression</h1>';

                echo '<section><b><p>Êtes-vous sûr de vouloir supprimer l\'usager suivant ?</b></p>';
                echo '<p>Civilité : ' . $usager['Civilité'] . '</p>';
                echo '<p>Nom : ' . $usager['Nom'] . '</p>';
                echo '<p>Prénom : ' . $usager['Prénom'] . '</p>';
                echo '<p>Adresse : ' . $usager['Adresse'] . '</p>';
                echo '<p>Ville : ' . $usager['Ville'] . '</p>';
                echo '<p>Code postal : ' . $usager['Cp'] . '</p>';
                echo '<p>Date de naissance : ' . $usager['DateNaissance'] . '</p>';
                echo '<p>Lieu de naissance : ' . $usager['LieuNaissance'] . '</p>';
                echo '<p>Numero de Securite Sociale : ' . $usager['NumSecuSociale'] . '</p>';
                echo '<p>Médecin Référent : ' . $medecinReferentDetails['Nom'] . ' ' . $medecinReferentDetails['Prénom'] . '</p>';
                echo '<form method="POST" action="suppressionUsager.php?id=' . $id . '">'; 
                echo '<div>';
                    // Demande de confirmation   
                    echo '<label for="oui">Oui</label>';
                    echo '<input type="radio" name="confirmation" id="oui" value="oui" required>';
                echo '</div>';
                echo '<div>';
                    echo '<label for="non">Non</label>';
                    echo '<input type="radio" name="confirmation" id="non" value="non" required>';
                echo '</div>';
                    // Validation
                    echo '<input type="submit" value="Valider">';
                    echo '</form></section>';
            } else { //
                echo '<h1>Usager introuvable</h1>';
                echo '<section><p>L\'usager spécifié n\'existe pas.</p></section>';
            }
        } else { // Id non trouvé
            echo '<h1>Identifiant non spécifié</h1>';
            echo '<section><p>Aucun identifiant d\'usager spécifié.</p></section>';
            }
    ?>
</body>
</html>
