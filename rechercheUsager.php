<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Recherche usager</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <nav>
        <ul id="menu">
            <li><a href="accueil.php">Accueil</a></li>
            <li><a href="rendezVous.php"> Rendez-vous </a></li>
            <li><a href="affichageMedecin.php">Médecins</a>
                <ul>
                    <li><a href="ajoutMedecin.php">Ajout</a></li>
                    <li><a href="rechercheMedecin.php">Recherche</a></li>
                </ul>
            </li>
            <li><a href="affichageUsager.php">Usagers</a>
                <ul>
                    <li><a href="ajoutUsager.php">Ajout</a></li>
                    <li><a href="rechercheUsager.php">Recherche</a></li>
                </ul>
            </li>
        </ul>
    </nav>
           
    <h1>Recherche usager</h1>

<form method="POST" action="rechercheUsager.php">
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

    // Sélectionnez les informations nécessaires des usagers et leur médecin référent
    $query = $linkpdo->prepare('SELECT u.ID_Usager, u.civilité, u.nom, u.prénom, u.adresse, u.cp, u.ville, u.dateNaissance, u.lieuNaissance, m.nom as nomMedecin, m.prénom as prenomMedecin
                                FROM Usagers u
                                LEFT JOIN Medecins m ON u.médecinRéférent = m.ID_Medecin
                                WHERE u.nom LIKE :mots_cles OR u.prénom LIKE :mots_cles');
    $query->execute(array('mots_cles' => "%$mots_cles%"));

    if ($query->rowCount() > 0) {
        echo '<h2>Résultats de la recherche :</h2>';
        echo '<table>
                <tr>
                    <th>Civilité</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Adresse</th>
                    <th>Code Postal</th>
                    <th>Ville</th>
                    <th>Date de naissance</th>
                    <th>Lieu de naissance</th>
                    <th>Médecin référent</th>
                    <th>Actions</th>
                </tr>';

        while ($row = $query->fetch()) {
            echo '<tr>
                    <td>' . $row['civilité'] . '</td>
                    <td>' . $row['nom'] . '</td>
                    <td>' . $row['prénom'] . '</td>
                    <td>' . $row['adresse'] . '</td>
                    <td>' . $row['cp'] . '</td>
                    <td>' . $row['ville'] . '</td>
                    <td>' . $row['dateNaissance'] . '</td>
                    <td>' . $row['lieuNaissance'] . '</td>
                    <td>' . $row['nomMedecin'] . ' ' . $row['prenomMedecin'] . '</td>
                    <td>
                        <a href="modificationUsager.php?id='.$row['ID_Usager'].'">Modifier</a>
                        <p>ou</p>
                        <a href="suppressionUsager.php?id='.$row['ID_Usager'].'">Supprimer</a>
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