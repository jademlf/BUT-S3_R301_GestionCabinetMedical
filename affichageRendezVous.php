<?php include 'verificationUtilisateur.php'; ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des rendez-vous</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link rel="stylesheet" type="text/css" href="menu.css">
    <script>
        // Fonction pour mettre à jour la page lorsque la liste déroulante change
        function filtrerParMedecin() {
            // Récupérer l'élément de liste déroulante des médecins
            var medecinSelect = document.getElementById("medecin");
            // Récupérer la valeur sélectionnée dans la liste déroulante (ID du médecin)
            var selectedMedecin = medecinSelect.value;
            // Rediriger la page avec le paramètre de filtre pour le médecin sélectionné
            window.location.href = '?medecin=' + selectedMedecin;
        }
    </script>
</head>
<body>
    <?php 
        include 'menu.php'; // Menu de navigation
        include 'connexion_bd.php'; // Connexion à la BD
        
        // Sélectionner la liste des médecins pour le menu déroulant
        $queryMedecins = $linkpdo->query("SELECT ID_Medecin, Nom, Prénom FROM medecins ORDER BY Nom");
        $medecins = $queryMedecins->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <h1>Liste des rendez-vous</h1>
    <section>
        <!-- Formulaire de filtrage par médecin -->
        <form action="" method="get">
            <label for="medecin">Filtrer par médecin :</label>
            <select name="medecin" id="medecin" onchange="filtrerParMedecin()">
                <option value="">Tous les médecins</option>
                <?php
                    // Afficher la liste des médecins dans le menu déroulant
                    foreach ($medecins as $medecin) {
                        $selected = (isset($_GET['medecin']) && $_GET['medecin'] == $medecin['ID_Medecin']) ? 'selected' : '';
                        echo '<option value="' . $medecin['ID_Medecin'] . '" ' . $selected . '>' . $medecin['Nom'] . ' ' . $medecin['Prénom'] . '</option>';
                    }
                ?>
            </select><br>
        </form>

        <?php
            // Filtrer les rendez-vous par médecin si un médecin est sélectionné
            $medecinFilter = isset($_GET['medecin']) ? intval($_GET['medecin']) : null;
            $conditionMedecin = $medecinFilter ? "AND rendezVous.ID_Medecin = $medecinFilter" : "";

            // Sélectionner le nombre total de rendez-vous
            $queryTotal = $linkpdo->query("SELECT COUNT(*) AS total FROM rendezVous WHERE 1 $conditionMedecin");
            $resultTotal = $queryTotal->fetch();
            $totalPages = ceil($resultTotal['total'] / 10); // 10 rendez-vous par page

            // Récupérer le numéro de page à partir de l'URL, par défaut 1
            $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

            // Calculer l'offset
            $offset = ($page - 1) * 10;

            // Sélectionner les rendez-vous pour la page actuelle, triés par ordre chronologique descendant
            $query = $linkpdo->query("SELECT ID_RendezVous, 
                                    DATE_FORMAT(DateConsultation, '%d/%m/%Y') AS DateConsultation,
                                    HeureConsultation, DuréeConsultation, 
                                    usagers.Nom AS NomUsager, usagers.Prénom AS PrenomUsager, 
                                    medecins.Nom AS NomMedecin, medecins.Prénom AS PrenomMedecin
                                    FROM rendezVous
                                    JOIN usagers ON rendezVous.ID_Usager = usagers.ID_Usager
                                    JOIN medecins ON rendezVous.ID_Medecin = medecins.ID_Medecin
                                    WHERE 1 $conditionMedecin
                                    ORDER BY DateConsultation DESC, HeureConsultation DESC
                                    LIMIT $offset, 10"
                                );

            // Afficher le tableau des rendez-vous ou un message si aucun rendez-vous n'est trouvé
            if ($query->rowCount() > 0) {
                echo '<form id="formulaireActions" method="post">';
                echo '<input type="hidden" name="idRendezVous" value="">'; // Champ caché pour stocker l'ID de la ligne sélectionnée
                echo '<table>
                        <tr>
                            <th>Date de consultation</th>
                            <th>Heure de consultation</th>
                            <th>Durée (minutes)</th>
                            <th>Usager</th>
                            <th>Médecin</th>
                            <th></th>
                        </tr>';
                // Affichage des rendez-vous
                while ($row = $query->fetch()) {
                    echo '<tr>
                            <td>' . $row['DateConsultation'] . '</td>
                            <td>' . $row['HeureConsultation'] . '</td>
                            <td>' . $row['DuréeConsultation'] . '</td>
                            <td>' . $row['NomUsager'] . ' ' . $row['PrenomUsager'] . '</td>
                            <td>' . $row['NomMedecin'] . ' ' . $row['PrenomMedecin'] . '</td>
                            <td>
                                <a href="modificationRDV.php?id=' . $row['ID_RendezVous'] . '">🖊</a>
                                <span> ou </span>
                                <a href="suppressionRDV.php?id=' . $row['ID_RendezVous'] . '">🗑</a>
                            </td>
                        </tr>';
                }
                echo '</table>';
            } else {
                // Aucun rendez-vous trouvé
                echo '<br>Aucun rendez-vous trouvé.';
            }

            // Afficher les liens de pagination
            echo '<div class="pagination">';
            for ($i = 1; $i <= $totalPages; $i++) {
                echo '<a href="?page=' . $i . '&medecin=' . $medecinFilter . '">' . $i . '</a>';
            }
            echo '</div>';
        ?>
    </section>
</body>
</html>
