<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des usagers</title>
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

    <h1>Liste des usagers</h1>

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

    // Sélectionnez le nombre total d'usagers
    $queryTotal = $linkpdo->query('SELECT COUNT(*) AS total FROM Usagers');
    $resultTotal = $queryTotal->fetch();
    $totalPages = ceil($resultTotal['total'] / 5); // 5 usagers par page

    // Récupérer le numéro de page à partir de l'URL, par défaut 1
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

    // Calculer l'offset
    $offset = ($page - 1) * 5;

    // Sélectionnez les usagers pour la page actuelle
    $query = $linkpdo->query("SELECT ID_Usager, civilité, nom, prénom, DateNaissance, MédecinRéférent  FROM Usagers LIMIT $offset, 5");

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
                
                    echo '<tr>
                            <td>' . $row['civilité'] . '</td>
                            <td>' . $row['nom'] . '</td>
                            <td>' . $row['prénom'] . '</td>
                            <td>' . $row['DateNaissance'] . '</td>
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
