<?php include 'verificationUtilisateur.php'; ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des médecins</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link rel="stylesheet" type="text/css" href="menu.css">
</head>
<body>
    <?php 
        include 'menu.php'; // Menu de navigation
        include 'connexion_bd.php'; // Connexion à la BD

        // Sélectionner le nombre total de médecins
        $queryTotal = $linkpdo->query('SELECT COUNT(*) AS total FROM medecins');
        $resultTotal = $queryTotal->fetch();
        $totalPages = ceil($resultTotal['total'] / 10); // 10 médecins par page

        // Récupérer le numéro de page à partir de l'URL, par défaut 1
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

        // Calculer l'offset
        $offset = ($page - 1) * 10;

        // Sélectionnez les médecins pour la page actuelle
        $query = $linkpdo->query("SELECT civilité, nom, prénom FROM medecins ORDER BY nom LIMIT $offset, 10");

        if ($query->rowCount() > 0) {
            // Afficher le tableau des médecins
            echo '<h1>Liste des médecins</h1><section>';
            echo '<table>
                    <tr>
                        <th>Civilité</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                    </tr>';

            // Tant qu'il reste des informations à affciher
            while ($row = $query->fetch()) {
                // Affichage des informations du médecin dans une ligne du tableau
                echo '<tr>
                        <td>' . $row['civilité'] . '</td>
                        <td>' . $row['nom'] . '</td>
                        <td>' . $row['prénom'] . '</td>
                      </tr>';
            }
            echo '</table>';
        } else {
            // Aucun médecin trouvé
            echo 'Aucun médecin trouvé.';
        }
        
        // Afficher les liens de pagination
        echo '<div class="pagination">';
        for ($i = 1; $i <= $totalPages; $i++) {
            echo '<a href="?page=' . $i . '">' . $i . '</a>';
        }
        echo '</div></section>';
        ?>
    
</body>
</html>
