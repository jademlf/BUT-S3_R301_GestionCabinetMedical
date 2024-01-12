<?php include 'verificationUtilisateur.php'; ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion Cabinet Médical</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link rel="stylesheet" type="text/css" href="menu.css">
</head>
<body>
    <?php include 'menu.php'; ?>

    <h1> Statistiques </h1>

    <?php
    include 'connexion_bd.php';

    // Répartition des usagers par sexe et âge
    $querySexeAge = $linkpdo->query('
        SELECT 
            CASE
                WHEN YEAR(CURDATE()) - YEAR(DateNaissance) < 25 THEN "Moins de 25 ans"
                WHEN YEAR(CURDATE()) - YEAR(DateNaissance) BETWEEN 25 AND 50 THEN "Entre 25 et 50 ans"
                ELSE "Plus de 50 ans"
            END AS TrancheAge,
            COUNT(*) AS NbUsagers,
            Civilité
        FROM Usagers
        GROUP BY TrancheAge, Civilité
    ');

    $statistiquesSexeAge = [];

    while ($row = $querySexeAge->fetch()) {
        $trancheAge = $row['TrancheAge'];
        $sexe = $row['Civilité'];
        $nbUsagers = $row['NbUsagers'];

        if (!isset($statistiquesSexeAge[$trancheAge])) {
            $statistiquesSexeAge[$trancheAge] = [0, 0];
        }
        $statistiquesSexeAge[$trancheAge][$sexe == 'F' ? 1 : 0] = $nbUsagers;
    }
    ?>
    <!-- Affichage du tableau de répartition par sexe et âge -->
    <h3>Répartition des usagers par sexe et âge</h3>
    <table>
        <tr>
            <th>Tranche d'âge</th>
            <th>Nombre d'hommes</th>
            <th>Nombre de femmes</th>
        </tr>
        <?php foreach ($statistiquesSexeAge as $trancheAge => $stat) : ?>
            <tr>
                <td><?= $trancheAge ?></td>
                <td><?= $stat[0] ?></td>
                <td><?= $stat[1] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <br/><br/>

<?php

// Durée totale des consultations par médecin
$queryDureeConsultation = $linkpdo->query('
    SELECT 
        Medecins.Nom AS Nom, Medecins.Prénom AS Prénom, 
        SUM(RendezVous.DuréeConsultation) AS DureeTotale
    FROM RendezVous
    JOIN Medecins ON RendezVous.ID_Medecin = Medecins.ID_Medecin
    GROUP BY Medecins.Nom, Medecins.Prénom 
');

$statistiquesDureeConsultation = [];

while ($row = $queryDureeConsultation->fetch()) {
    $medecinNom = $row['Nom'];
    $medecinPrenom = $row['Prénom'];
    $dureeTotale = $row['DureeTotale'] / 60;
    $statistiquesDureeConsultation[] = [$medecinNom, $medecinPrenom, $dureeTotale];
}
?>
<!-- Affichage du tableau de la durée totale des consultations par médecin -->
<h3>Durée totale des consultations par médecin</h3>
<table>
    <tr>
        <th>Nom du Médecin</th>
        <th>Prénom du Médecin</th>
        <th>Durée totale (heures)</th>
    </tr>
    <?php foreach ($statistiquesDureeConsultation as $stat) : ?>
        <tr>
            <td><?= $stat[0] ?></td>
            <td><?= $stat[1] ?></td>
            <td><?= $stat[2] ?></td>
        </tr>
    <?php endforeach; ?>
</table>
</body>
</html>
