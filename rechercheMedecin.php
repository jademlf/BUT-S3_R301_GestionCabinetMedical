<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Recherche Medecin</title>
    <link rel="stylesheet" type="text/css" href="styles.css">

    </head>
<body>
    <?php include 'menu.php'; ?>
           
<h1>Recherche Medecin</h1>

<form method="POST" action="rechercheMedecin.php">
    <label for="mots_cles">Mots-clés :</label>
    <input type="text" name="mots_cles" id="mots_cles">
    <input type="submit" value="Rechercher">
</form>

<?php
 include 'connexion_bd.php';

// Traitement du formulaire
if (isset($_POST['mots_cles'])) {
    $mots_cles = $_POST['mots_cles'];
    $query = $linkpdo->prepare('SELECT ID_Medecin, civilité,nom,prénom FROM Medecins WHERE nom LIKE :mots_cles OR prénom LIKE :mots_cles');
    $query->execute(array('mots_cles' => "%$mots_cles%"));

    if ($query->rowCount() > 0) {
        echo '<h2>Résultats de la recherche :</h2>';
        echo '<table>
                <tr>
                    <th>Civilité</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Actions</th>
                </tr>';

        while ($row = $query->fetch()) {
            echo '<tr>
                    <td>' . $row['civilité'] . '</td>
                    <td>' . $row['nom'] . '</td>
                    <td>' . $row['prénom'] . '</td>
                    <td>
                        <a href="modificationMedecin.php?id='.$row['ID_Medecin'].'">Modifier</a>
                        <p>ou</p>
                        <a href="suppressionMedecin.php?id='.$row['ID_Medecin'].'">Supprimer</a>
                    </td>
                  </tr>';
        }

        echo '</table>';
    } else {
        echo 'Aucun résultat trouvé.';
    }
}
?>
</body>
</html>
