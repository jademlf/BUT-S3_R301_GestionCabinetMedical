<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion Cabinet Médical</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <?php include 'menu.php'; ?>
    <h1>Statistiques</h1>

    <?php
     include 'connexion_bd.php';
    
    // Calcul des statistiques sur la répartition des usagers par sexe et âge
    // (vous devrez remplacer les valeurs fictives par les données réelles de votre base de données)
    $statistiquesSexeAge = [
        ['Moins de 25 ans', 10, 15],    // Exemple : 10 hommes, 15 femmes
        ['Entre 25 et 50 ans', 20, 25],  // Exemple : 20 hommes, 25 femmes
        ['Plus de 50 ans', 5, 10]        // Exemple : 5 hommes, 10 femmes
    ];

    // Calcul de la durée totale des consultations effectuées par chaque médecin
    // (vous devrez remplacer les valeurs fictives par les données réelles de votre base de données)
    $statistiquesDureeConsultation = [
        ['Dr. Dupont', 30],    // Exemple : 30 heures
        ['Dr. Martin', 20],    // Exemple : 20 heures
        // ... (ajoutez d'autres médecins et leurs heures de consultation)
    ];
    ?>

    <!-- Affichage du tableau de répartition par sexe et âge -->
    <h3>Répartition des usagers par sexe et âge</h3>
    <table>
        <tr>
            <th>Tranche d'âge</th>
            <th>Nb Hommes</th>
            <th>Nb Femmes</th>
        </tr>
        <?php foreach ($statistiquesSexeAge as $stat) : ?>
            <tr>
                <td><?= $stat[0] ?></td>
                <td><?= $stat[1] ?></td>
                <td><?= $stat[2] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <br/><br/>
    <!-- Affichage du tableau de la durée totale des consultations par médecin -->
    <h3>Durée totale des consultations par médecin</h3>
    <table>
        <tr>
            <th>Médecin</th>
            <th>Durée totale (heures)</th>
        </tr>
        <?php foreach ($statistiquesDureeConsultation as $stat) : ?>
            <tr>
                <td><?= $stat[0] ?></td>
                <td><?= $stat[1] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

</body>
</html>


</body>
</html>
