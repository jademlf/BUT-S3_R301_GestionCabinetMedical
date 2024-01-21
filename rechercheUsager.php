<?php include 'verificationUtilisateur.php'; ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Recherche usager</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link rel="stylesheet" type="text/css" href="menu.css">
</head>
<body>
    <?php
        include 'menu.php'; // Menu de navigation
        include 'connexion_bd.php'; // Connexion à la BD
    ?>
           
    <h1>Recherche usager</h1>
    <section>
        <form method="POST" action="rechercheUsager.php">
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

            // Préparer la requête SQL pour la recherche des usagers et leur médecin référent
            $query = $linkpdo->prepare('SELECT u.ID_Usager, u.Civilité, u.Nom, u.Prénom, 
                                        u.Adresse, u.Cp, u.Ville, DATE_FORMAT(u.DateNaissance, "%d/%m/%Y") AS DateNaissance, 
                                        u.LieuNaissance, m.Nom as NomMedecin, m.Prénom as PrenomMedecin

                                        FROM usagers u
                                        LEFT JOIN medecins m ON u.MedecinReferent = m.ID_Medecin
                                        WHERE u.Nom LIKE :mots_cles OR u.Prénom LIKE :mots_cles');
            $query->execute(array('mots_cles' => "%$mots_cles%"));

            // Vérifier si des résultats ont été trouvés
            if ($query->rowCount() > 0) {
                echo '<h3>Résultats de la recherche </h3>';
                echo '<section><table>
                        <tr>
                            <th>Civilité</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Adresse</th>
                            <th>Code Postal</th>
                            <th>Ville</th>
                            <th>Date de naissance</th>
                            <th>Lieu de naissance</th>
                            <th>Médecin référent</th>
                            <th></th>
                        </tr>';

                // Afficher les résultats dans un tableau
                while ($row = $query->fetch()) {
                    echo '<tr>
                            <td>' . $row['Civilité'] . '</td>
                            <td>' . $row['Nom'] . '</td>
                            <td>' . $row['Prénom'] . '</td>
                            <td>' . $row['Adresse'] . '</td>
                            <td>' . $row['Cp'] . '</td>
                            <td>' . $row['Ville'] . '</td>
                            <td>' . $row['DateNaissance'] . '</td>
                            <td>' . $row['LieuNaissance'] . '</td>
                            <td>' . $row['NomMedecin'] . ' ' . $row['PrenomMedecin'] . '</td>
                            <td>
                                <a href="modificationUsager.php?id=' . $row['ID_Usager'] . '">🖊</a>
                                <span> ou </span>
                                <a href="suppressionUsager.php?id=' . $row['ID_Usager'] . '">🗑</a>
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