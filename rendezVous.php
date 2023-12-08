<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion Cabinet Médical</title>
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

    <h1> Ajouter un rendez-vous </h1>

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

    $error_message = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validation côté serveur
        $required_fields = ['usager', 'medecin', 'date', 'heure', 'duree'];

        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                $error_message = "Veuillez remplir tous les champs obligatoires.";
                break;
            }
        }

        if (empty($error_message)) {
            $usagerId = $_POST['usager'];
            $medecinId = $_POST['medecin'];
            $dateConsultation = $_POST['date'];
            $heureConsultation = $_POST['heure'];
            $dureeConsultation = $_POST['duree'];

            // Vérifier si le rendez-vous est disponible
            $checkAvailabilityQuery = $linkpdo->prepare('SELECT COUNT(*) AS count FROM RendezVous WHERE ID_Usager = :usagerId AND DateConsultation = :dateConsultation AND HeureConsultation = :heureConsultation');
            $checkAvailabilityQuery->execute(array('usagerId' => $usagerId, 'dateConsultation' => $dateConsultation, 'heureConsultation' => $heureConsultation));
            $result = $checkAvailabilityQuery->fetchColumn();

            if ($result == 0) {
                // Le rendez-vous est disponible, insérer dans la base de données
                $insertQuery = $linkpdo->prepare('INSERT INTO RendezVous (ID_Usager, ID_Medecin, DateConsultation, HeureConsultation, DuréeConsultation) 
                                                VALUES (:usagerId, :medecinId, :dateConsultation, :heureConsultation, :dureeConsultation)');
                $insertResult = $insertQuery->execute(array(
                    'usagerId' => $usagerId,
                    'medecinId' => $medecinId,
                    'dateConsultation' => $dateConsultation,
                    'heureConsultation' => $heureConsultation,
                    'dureeConsultation' => $dureeConsultation
                ));

                if ($insertResult) {
                    echo "<p>Le rendez-vous a été enregistré avec succès.</p>";
                } else {
                    echo "<p>Erreur lors de l'enregistrement du rendez-vous.</p>";
                    print_r($insertQuery->errorInfo());
                }
            } else {
                // Le rendez-vous n'est pas disponible
                echo "<p>Le rendez-vous n'est pas disponible à cette date et heure.</p>";
            }
        }
    }
    ?>

    <div class="container">
        <form action="#" method="post">
            <label for="usager">Usager :</label>
            <select name="usager" id="usager" required>
                <?php
                // Récupérer la liste des usagers depuis la base de données
                $usagerQuery = $linkpdo->query('SELECT ID_Usager, nom, prénom FROM Usagers');
                while ($usager = $usagerQuery->fetch()) {
                    echo '<option value="' . $usager['ID_Usager'] . '">' . $usager['nom'] . ' ' . $usager['prénom'] . '</option>';
                }
                ?>
            </select><br />

            <label for="medecin">Médecin :</label>
            <select name="medecin" id="medecin" required>
                <?php
                // Récupérer la liste des médecins depuis la base de données
                $medecinQuery = $linkpdo->query('SELECT ID_Medecin, nom, prénom FROM Medecins');
                while ($medecin = $medecinQuery->fetch()) {
                    echo '<option value="' . $medecin['ID_Medecin'] . '">' . $medecin['nom'] . ' ' . $medecin['prénom'] . '</option>';
                }
                ?>
            </select><br />

            <label for="date">Date :</label>
            <input class="custom-input" type="date" id="date" name="date" required>

            <label for="heure">Heure :</label>
            <input class="custom-input" type="time" id="heure" name="heure" required>

            <label for="duree">Durée (en minutes) :</label>
            <input class="custom-input" type="number" id="duree" name="duree" value="30" required>

            <input type="submit" value="Enregistrer Rendez-vous">
        </form>
        
        <?php
        // Afficher le message d'erreur s'il y a lieu
        if (!empty($error_message)) {
            echo '<p style="color: red;">' . $error_message . '</p>';
        }
        ?>
    </div>
</body>
</html>
