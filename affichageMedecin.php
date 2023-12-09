<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des médecins</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <?php include 'menu.php'; ?>

    <h1>Liste des médecins</h1>

    <?php
    include 'connexion_bd.php';

    // Sélectionner le nombre total de médecins
    $queryTotal = $linkpdo->query('SELECT COUNT(*) AS total FROM Medecins');
    $resultTotal = $queryTotal->fetch();
    $totalPages = ceil($resultTotal['total'] / 10); // 5 médecins par page

    // Récupérer le numéro de page à partir de l'URL, par défaut 1
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

    // Calculer l'offset
    $offset = ($page - 1) * 10;

    // Sélectionnez les médecins pour la page actuelle
    $query = $linkpdo->query("SELECT civilité, nom, prénom FROM Medecins LIMIT $offset, 10");

    if ($query->rowCount() > 0) {
        echo '<table>
                <tr>
                    <th>Civilité</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                </tr>';

        while ($row = $query->fetch()) {
            echo '<tr>
                    <td>' . $row['civilité'] . '</td>
                    <td>' . $row['nom'] . '</td>
                    <td>' . $row['prénom'] . '</td>
                  </tr>';
        }

        echo '</table>';
    } else {
        echo 'Aucun médecin trouvé.';
    }

    // Afficher les liens de pagination
    echo '<div class="pagination">';
    for ($i = 1; $i <= $totalPages; $i++) {
        echo '<a href="?page=' . $i . '">' . $i . '</a>';
    }
    echo '</div>';
    ?>

</body>
</html>
