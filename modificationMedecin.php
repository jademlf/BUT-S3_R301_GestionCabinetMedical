<?php include 'verificationUtilisateur.php'; ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modification Médecin</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link rel="stylesheet" type="text/css" href="menu.css">
</head>
<body>
    <?php 
        include 'menu.php'; // Menu de navigation
        include 'connexion_bd.php'; // Connexion à la BD
    ?>

    <h1>Modification Médecin</h1>
    <section>
        <?php
            $message = ''; // Initialisation du message

            if (isset($_GET['id'])) {
                // Récupération de l'identifiant du médecin depuis l'URL
                $id = $_GET['id'];

                // Sélection des informations du médecin correspondant dans la base de données
                $query = $linkpdo->prepare('SELECT * FROM medecins WHERE ID_Medecin = :id');
                $query->execute(array('id' => $id));
                $medecin = $query->fetch();

                if ($medecin) {
                    // Traitement du formulaire de modification lorsqu'il est soumis
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        $nouvelleCivilite = $_POST['civilite'];
                        $nouveauNom = $_POST['nom'];
                        $nouveauPrenom = $_POST['prenom'];

                        // Utilisation de l'instruction SQL UPDATE pour mettre à jour le médecin
                        $miseAJourQuery = $linkpdo->prepare('UPDATE medecins SET Civilité = ?, Nom = ?, Prénom = ? WHERE ID_Medecin = ?');
                        $miseAJourQuery->execute([$nouvelleCivilite, $nouveauNom, $nouveauPrenom, $id]);

                        // Réexécution de la requête pour obtenir les informations mises à jour
                        $query->execute(array('id' => $id));
                        $medecin = $query->fetch();

                        $message = 'Le médecin a été mis à jour avec succès.';
                    }

                    // Affichage du formulaire de modification avec les données mises à jour
                    echo '<form method="POST" action="modificationMedecin.php?id=' . $id . '">
                            <input type="hidden" name="id" value="' . $id . '">
                            
                            <label for="civilite">Civilité :</label>
                            
                            <input type="radio" name="civilite" value="M" ' . ($medecin['Civilité'] == 'M' ? 'checked' : '') . '> M
                            <input type="radio" name="civilite" value="F" ' . ($medecin['Civilité'] == 'F' ? 'checked' : '') . '> F<br />

                            <label for="nom">Nom :</label>
                            <input type="text" name="nom" id="nom" value="' . $medecin['Nom'] . '">

                            <label for="prenom">Prénom :</label>
                            <input type="text" name="prenom" id="prenom" value="' . $medecin['Prénom'] . '">

                            <input type="submit" value="Modifier">
                        </form>';
                } else {
                    echo '<p>Le médecin spécifié n\'existe pas.</p>';
                }
            } else {
                echo '<p>Aucun identifiant de médecin spécifié.</p>';
            }

            // Affichage du message de succès ici
            echo '<p style="color: #0097b2;">' . $message . '</p>';
        ?>
    </section>
</body>
</html>
