<?php include 'verificationUtilisateur.php'; ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des usagers</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link rel="stylesheet" type="text/css" href="menu.css">
</head>
<body>
    <?php include 'menu.php'; ?>

    <h1>Liste des usagers</h1>

    <?php
    include 'connexion_bd.php';

    // Sélectionnez le nombre total d'usagers
    $queryTotal = $linkpdo->query('SELECT COUNT(*) AS total FROM Usagers');
    $resultTotal = $queryTotal->fetch();
    $totalPages = ceil($resultTotal['total'] / 10); // 10 usagers par page

    // Récupérer le numéro de page à partir de l'URL, par défaut 1
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

    // Calculer l'offset
    $offset = ($page - 1) * 10;

    // Sélectionnez les usagers pour la page actuelle
    $query = $linkpdo->query("SELECT ID_Usager, civilité, nom, prénom, DateNaissance, MédecinRéférent  FROM Usagers ORDER BY Usagers.nom LIMIT $offset, 10");

    if ($query->rowCount() > 0) {
        echo '<table>
                <tr>
                    <th>Civilité</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Date de naissance</th>
                    <th>Médecin Référent</th>
                </tr>';

        while ($row = $query->fetch()) {
            // Récupérer le nom et prénom du médecin référent à partir de la table des médecins
            $medecinReferentId = $row['MédecinRéférent'];
            $queryMedecin = $linkpdo->query("SELECT nom, prénom FROM Medecins WHERE ID_Medecin = $medecinReferentId");
            $medecinRow = $queryMedecin->fetch();

            // Formater la date au format français
            $dateNaissance = strftime('%d/%m/%Y', strtotime($row['DateNaissance']));

            echo '<tr>
                    <td>' . $row['civilité'] . '</td>
                    <td>' . $row['nom'] . '</td>
                    <td>' . $row['prénom'] . '</td>
                    <td>' . $dateNaissance . '</td>
                    <td>' . $medecinRow['nom'] . ' ' . $medecinRow['prénom'] . '</td>
                  </tr>';
        }

        echo '</table>';
    } else {
        echo 'Aucun usager trouvé.';
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