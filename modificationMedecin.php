<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modification Médecin</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <?php include 'menu.php'; ?>

    <h1>Modification Médecin</h1>

    <?php
    // Votre configuration de connexion à la base de données
    include 'connexion_bd.php';

    $message = ''; // Initialiser le message

    if (isset($_GET['id'])) {
        // Récupérer l'identifiant du médecin depuis l'URL
        $id = $_GET['id'];

        // Sélectionner les informations du médecin correspondant dans la base de données
        $query = $linkpdo->prepare('SELECT * FROM Medecins WHERE ID_Medecin = :id');
        $query->execute(array('id' => $id));
        $medecin = $query->fetch();

        if ($medecin) {
            // Traitement du formulaire de modification lorsqu'il est soumis
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $newCivilite = $_POST['civilite'];
                $newNom = $_POST['nom'];
                $newPrenom = $_POST['prenom'];

                // Utilisation de l'instruction SQL UPDATE pour mettre à jour le médecin
                $updateQuery = $linkpdo->prepare('UPDATE Medecins SET Civilité = ?, Nom = ?, Prénom = ? WHERE ID_Medecin = ?');
                $updateQuery->execute([$newCivilite, $newNom, $newPrenom, $id]);

                // Réexécutez la requête pour obtenir les informations mises à jour
                $query->execute(array('id' => $id));
                $medecin = $query->fetch();

                $message = 'Le médecin a été mis à jour avec succès.';
            }

            // Afficher le formulaire de modification avec les données mises à jour
            echo '<form method="POST" action="modificationMedecin.php?id=' . $id . '">
                    <input type="hidden" name="id" value="' . $id . '">
                    <label for="civilite">Civilité :</label>
                    <input type="text" name="civilite" id="civilite" value="' . $medecin['Civilité'] . '">
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

    // Afficher le message de succès ici
    echo '<p>' . $message . '</p>';
    ?>
</body>
</html>
