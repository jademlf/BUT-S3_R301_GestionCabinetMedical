<?php include 'verificationUtilisateur.php'; ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Recherche Medecin</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link rel="stylesheet" type="text/css" href="menu.css">

    </head>
<body>
    <?php 
        include 'menu.php'; // Menu de navigation
        include 'connexion_bd.php'; // Connexion à la BD
    ?>
           
    <h1>Recherche Medecin</h1>

    <section>
    <form method="POST" action="rechercheMedecin.php">
        <label for="mots_cles">Mots-clés :</label>
        <input type="text" name="mots_cles" id="mots_cles">
        <input type="submit" value="Rechercher">
    </form>
    </section>

    <?php
        // Traitement du formulaire
        if (isset($_POST['mots_cles'])) {
            // Récupérer les mots-clés depuis le formulaire
            $mots_cles = $_POST['mots_cles'];

            // Préparer la requête SQL pour la recherche des médecins
            $query = $linkpdo->prepare('SELECT ID_Medecin, Civilité, Nom, Prénom FROM medecins WHERE Nom LIKE :mots_cles OR Prénom LIKE :mots_cles');
            $query->execute(array('mots_cles' => "%$mots_cles%"));

            // Vérifier si des résultats ont été trouvés
            if ($query->rowCount() > 0) {
                echo '<h3>Résultats de la recherche </h3>';
                echo '<section><table>
                        <tr>
                            <th>Civilité</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th></th>
                        </tr>';

                // Afficher les résultats dans un tableau
                while ($row = $query->fetch()) {
                    echo '<tr>
                            <td>' . $row['Civilité'] . '</td>
                            <td>' . $row['Nom'] . '</td>
                            <td>' . $row['Prénom'] . '</td>
                            <td>
                                <a href="modificationMedecin.php?id=' . $row['ID_Medecin'] . '">🖊</a>
                                <span> ou </span>
                                <a href="suppressionMedecin.php?id=' . $row['ID_Medecin'] . '">🗑</a>
                            </td>
                        </tr>';
                }

                echo '</table></section>';
            } else {
                // Aucun résultat trouvé
                echo 'Aucun résultat trouvé.';
            }
        }
    ?>
</body>
</html>
