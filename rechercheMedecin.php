<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Recherche Medecin</title>
    <link rel="stylesheet" type="text/css" href="styles.css">

    </head>
<body>
    <nav>
        <ul id="menu">
            <li><a href="accueil.php">Accueil</a>
            <li><a href="rendezVous.php"> Rendez-vous </a></li>
            </li><li><a href="affichageMedecin.php">Médecins</a>
                <ul>
                    <li><a href="ajoutMedecin.php">Ajout</a></li>
                    <li><a href="rechercheMedecin.php">Recherche</a></li>
                </ul>
            </li><li><a href="affichageUsager.php">Usagers</a>
                <ul>
                    <li><a href="ajoutUsager.php">Ajout</a></li>
                    <li><a href="rechercheUsager.php">Recherche</a></li>
                </ul>
            </li>
        </ul>
    </nav>
           
<h1>Recherche Medecin</h1>

<form method="POST" action="rechercheMedecin.php">
    <label for="mots_cles">Mots-clés :</label>
    <input type="text" name="mots_cles" id="mots_cles">
    <input type="submit" value="Rechercher">
</form>

<?php
// Votre configuration de connexion à la base de données
$server = 'localhost';
$login = 'root';
$mdp = '';
$db = 'projet_php';

try {
    $linkpdo = new PDO("mysql:host=$server;dbname=$db;charset=utf8", $login, $mdp);
} catch (PDOException $e) {
    die('Erreur : ' . $e->getMessage());
}

// Traitement du formulaire
if (isset($_POST['mots_cles'])) {
    $mots_cles = $_POST['mots_cles'];
    $query = $linkpdo->prepare('SELECT civilité,nom,prénom FROM Medecins WHERE nom LIKE :mots_cles OR prénom LIKE :mots_cles');
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
                        <a href="modificationUsager.php?id=' . $row['id_usager'] . '">Modifier</a>
                        <p>ou</p>
                        <a href="suppressionUsager.php?id=' . $row['id_usager'] . '">Supprimer</a>
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
