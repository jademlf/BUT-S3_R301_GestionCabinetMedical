<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modification Usager</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <?php include 'menu.php'; ?>    

    <h1>Modification Usager</h1>

    <?php
     include 'connexion_bd.php';

    $message = ''; // Initialiser le message

    if (isset($_GET['id'])) {
        // Récupérer l'identifiant de l'usager depuis l'URL
        $id = $_GET['id'];

        // Sélectionner les informations de l'usager correspondant dans la base de données
        $query = $linkpdo->prepare('SELECT * FROM Usagers WHERE ID_Usager = :id');
        $query->execute(array('id' => $id));
        $usager = $query->fetch();

        if ($usager) {
            // Traitement du formulaire de modification lorsqu'il est soumis
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $newNom = $_POST['nom'];
                $newPrenom = $_POST['prenom'];
                $newAdresse = $_POST['adresse'];
                $newCP = $_POST['cp'];
                $newVille = $_POST['ville'];

                // Utilisation de l'instruction SQL UPDATE pour mettre à jour l'usager
                $updateQuery = $linkpdo->prepare('UPDATE Usagers SET Nom = ?, Prénom = ?, Adresse = ?, Cp = ?, Ville = ? WHERE ID_Usager = ?');
                $updateQuery->execute([$newNom, $newPrenom, $newAdresse, $newCP, $newVille, $id]);

                // Réexécutez la requête pour obtenir les informations mises à jour
                $query->execute(array('id' => $id));
                $usager = $query->fetch();

                $message = 'L\'usager a été mis à jour avec succès.';
            }

            // Afficher le formulaire de modification avec les données mises à jour
            echo '<form method="POST" action="modificationUsager.php?id=' . $id . '">
                    <input type="hidden" name="id" value="' . $id . '">
                    <label for="nom">Nom :</label>
                    <input type="text" name="nom" id="nom" value="' . $usager['Nom'] . '">
                    <label for="prenom">Prénom :</label>
                    <input type="text" name="prenom" id="prenom" value="' . $usager['Prénom'] . '">
                    <label for="adresse">Adresse :</label>
                    <input type="text" name="adresse" id="adresse" value="' . $usager['Adresse'] . '">
                    <label for="cp">Code Postal :</label>
                    <input type="text" name="cp" id="cp" value="' . $usager['Cp'] . '">
                    <label for="ville">Ville :</label>
                    <input type="text" name="ville" id="ville" value="' . $usager['Ville'] . '">
                    <input type="submit" value="Modifier">
                </form>';
        } else {
            echo '<p>L\'usager spécifié n\'existe pas.</p>';
        }
    } else {
        echo '<p>Aucun identifiant d\'usager spécifié.</p>';
    }

    // Afficher le message de succès ici
    echo '<p>' . $message . '</p>';
    ?>
</body>
</html>
