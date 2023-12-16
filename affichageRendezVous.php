<?php include 'verificationUtilisateur.php'; ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des rendez-vous</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link rel="stylesheet" type="text/css" href="menu.css">
</head>
<body>
    <?php include 'menu.php'; ?>

    <h1>Liste des rendez-vous</h1>

    <?php
    // Inclure le fichier de connexion à la base de données
    include 'connexion_bd.php';

    // Sélectionner la liste des médecins pour le menu déroulant
    $queryMedecins = $linkpdo->query("SELECT ID_Medecin, Nom, Prénom FROM Medecins ORDER BY Nom");
    $medecins = $queryMedecins->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <!-- Formulaire de filtrage par médecin -->
    <form action="" method="get">
        <label for="medecin">Filtrer par médecin :</label>
        <select name="medecin" id="medecin">
            <option value="">Tous les médecins</option>
            <?php
            // Afficher la liste des médecins dans le menu déroulant
            foreach ($medecins as $medecin) {
                echo '<option value="' . $medecin['ID_Medecin'] . '">' . $medecin['Nom'] . ' ' . $medecin['Prénom'] . '</option>';
            }
            ?>
        </select><br>
        <input type="submit" value="Filtrer">
    </form>

    <?php
    // Filtrer les rendez-vous par médecin si un médecin est sélectionné
    $medecinFilter = isset($_GET['medecin']) ? intval($_GET['medecin']) : null;
    $conditionMedecin = $medecinFilter ? "AND RendezVous.ID_Medecin = $medecinFilter" : "";

    // Sélectionner le nombre total de rendez-vous
    $queryTotal = $linkpdo->query("SELECT COUNT(*) AS total FROM RendezVous WHERE 1 $conditionMedecin");
    $resultTotal = $queryTotal->fetch();
    $totalPages = ceil($resultTotal['total'] / 10); // 10 rendez-vous par page

    // Récupérer le numéro de page à partir de l'URL, par défaut 1
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

    // Calculer l'offset
    $offset = ($page - 1) * 10;

    // Sélectionner les rendez-vous pour la page actuelle, triés par ordre chronologique descendant
    $query = $linkpdo->query("SELECT RendezVous.ID_RendezVous, 
        DATE_FORMAT(RendezVous.DateConsultation, '%d/%m/%Y') AS DateConsultation,
        RendezVous.HeureConsultation, RendezVous.DuréeConsultation, 
        Usagers.Nom AS NomUsager, Usagers.Prénom AS PrenomUsager, 
        Medecins.Nom AS NomMedecin, Medecins.Prénom AS PrenomMedecin
        FROM RendezVous
        JOIN Usagers ON RendezVous.ID_Usager = Usagers.ID_Usager
        JOIN Medecins ON RendezVous.ID_Medecin = Medecins.ID_Medecin
        WHERE 1 $conditionMedecin
        ORDER BY RendezVous.DateConsultation DESC, RendezVous.HeureConsultation DESC
        LIMIT $offset, 10");

    // Afficher le tableau des rendez-vous ou un message si aucun rendez-vous n'est trouvé
    if ($query->rowCount() > 0) {
        echo '<table>
                <tr>
                    <th>Date de consultation</th>
                    <th>Heure de consultation</th>
                    <th>Durée (minutes)</th>
                    <th>Usager</th>
                    <th>Médecin</th>
                </tr>';

        while ($row = $query->fetch()) {
            echo '<tr>
                    <td>' . $row['DateConsultation'] . '</td>
                    <td>' . $row['HeureConsultation'] . '</td>
                    <td>' . $row['DuréeConsultation'] . '</td>
                    <td>' . $row['NomUsager'] . ' ' . $row['PrenomUsager'] . '</td>
                    <td>' . $row['NomMedecin'] . ' ' . $row['PrenomMedecin'] . '</td>
                  </tr>';
        }

        echo '</table>';
    } else {
        echo 'Aucun rendez-vous trouvé.';
    }

    // Afficher les liens de pagination
    echo '<div class="pagination">';
    for ($i = 1; $i <= $totalPages; $i++) {
        echo '<a href="?page=' . $i . '&medecin=' . $medecinFilter . '">' . $i . '</a>';
    }
    echo '</div>';
    ?>

</body>
</html>
