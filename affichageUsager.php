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
    <?php 
        include 'menu.php'; // Menu de navigation
        include 'connexion_bd.php'; // Connexion à la BD

        // Sélectionner le nombre total d'usagers
        $queryTotal = $linkpdo->query('SELECT COUNT(*) AS total FROM usagers');
        $resultTotal = $queryTotal->fetch();
        $totalPages = ceil($resultTotal['total'] / 10); // 10 usagers par page

        // Récupérer le numéro de page à partir de l'URL, par défaut 1
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

        // Calculer l'offset
        $offset = ($page - 1) * 10;

        // Sélectionner les usagers pour la page actuelle
        $query = $linkpdo->query("SELECT ID_Usager, civilité, nom, prénom, DateNaissance, MédecinRéférent FROM usagers ORDER BY usagers.nom LIMIT $offset, 10");

        if ($query->rowCount() > 0) {
            echo '<h1>Liste des usagers</h1><section>';
            echo '<table>
                    <tr>
                        <th>Civilité</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Date de naissance</th>
                        <th>Médecin Référent</th>
                    </tr>';

            // Parcourir les résultats de la requête des usagers
            while ($row = $query->fetch()) {
                // Récupérer l'identifiant du médecin référent à partir des résultats de la requête des usagers
                $medecinReferentId = $row['MédecinRéférent'];

                // Vérifier si un médecin référent est associé à cet usager
                if ($medecinReferentId !== null) {
                    // Requête pour récupérer le nom et prénom du médecin référent à partir de la table des médecins
                    $queryMedecin = $linkpdo->query("SELECT nom, prénom FROM medecins WHERE ID_Medecin = $medecinReferentId");
                    $medecinRow = $queryMedecin->fetch();

                    // Construire l'information du médecin référent
                    $medecinReferentInfo = $medecinRow['nom'] . ' ' . $medecinRow['prénom'];
                } else {
                    // Aucun médecin référent associé à cet usager
                    $medecinReferentInfo = 'Non renseigné';
                }

                // Formater la date de naissance au format français
                $dateNaissance = strftime('%d/%m/%Y', strtotime($row['DateNaissance']));

                // Afficher les informations de l'usager dans une ligne du tableau HTML
                echo '<tr>
                        <td>' . $row['civilité'] . '</td>
                        <td>' . $row['nom'] . '</td>
                        <td>' . $row['prénom'] . '</td>
                        <td>' . $dateNaissance . '</td>
                        <td>' . $medecinReferentInfo . '</td>
                    </tr>';
            }

            echo '</table>';
        } else {
            // Aucun usager trouvé
            echo 'Aucun usager trouvé.';
        }

        // Afficher les liens de pagination
        echo '<div class="pagination">';
        for ($i = 1; $i <= $totalPages; $i++) {
            echo '<a href="?page=' . $i . '">' . $i . '</a>';
        }
        echo '</div> </section>';
    ?>
</body>
</html>
