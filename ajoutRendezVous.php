<?php include 'verificationUtilisateur.php'; ?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion Cabinet Médical</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link rel="stylesheet" type="text/css" href="menu.css">
    <script>
        // Fonction pour désactiver les week-ends dans l'entrée de date
        function desactiverWeekends() {
            var dateInput = document.getElementById("date");
            var dateSelectionnee = new Date(dateInput.value);
            var jourDeSemaine = dateSelectionnee.getDay();

            // Désactiver les week-ends (Samedi = 6, Dimanche = 0)
            if (jourDeSemaine === 6 || jourDeSemaine === 0) {
                alert("Les rendez-vous ne sont pas disponibles le samedi et le dimanche.");
                dateInput.value = ""; // Effacer l'entrée si le week-end est sélectionné
            }
        }

        // Fonction pour préremplir le médecin référent dans la liste déroulante des médecins
        function remplirMedecinReferent() {
            var usagerSelect = document.getElementById("usager");
            var medecinSelect = document.getElementById("medecin");
            var medecinReferentId = usagerSelect.options[usagerSelect.selectedIndex].getAttribute("data-medecin-referent");

            // Trouver l'option correspondante dans la liste des médecins
            var medecinOption = document.querySelector('#medecin option[value="' + medecinReferentId + '"]');

            // Sélectionner le médecin référent par défaut
            if (medecinOption) {
                medecinOption.selected = true;
            }
        }
    </script>
</head>
<body onload="remplirMedecinReferent()">
    <?php include 'menu.php'; ?>

    <h1> Ajouter un rendez-vous </h1>

    <?php
    include 'connexion_bd.php';
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    $message_erreur = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validation côté serveur
        $champs_obligatoires = ['usager', 'medecin', 'date', 'heure', 'duree'];

        foreach ($champs_obligatoires as $champ) {
            if (empty($_POST[$champ])) {
                $message_erreur = "Veuillez remplir tous les champs obligatoires.";
                break;
            }
        }

        if (empty($message_erreur)) {
            $id_usager = $_POST['usager'];
            $id_medecin = $_POST['medecin'];
            $date_consultation = $_POST['date'];
            $heure_consultation = $_POST['heure'];
            $duree_consultation = $_POST['duree'];

            // Calculer la date et l'heure de fin du rendez-vous en tenant compte de la durée
            $heure_fin_consultation = date('H:i', strtotime("$heure_consultation + $duree_consultation minutes"));
// Vérifier si le rendez-vous est disponible
$verifierDisponibiliteQuery = $linkpdo->prepare('SELECT COUNT(*) AS count FROM RendezVous 
    WHERE ID_Medecin = :id_medecin 
    AND DateConsultation = :date_consultation 
    AND (
        (HeureConsultation >= :heure_consultation AND HeureConsultation < :heure_fin_consultation) OR
        (HeureConsultation < :heure_consultation AND ADDTIME(HeureConsultation, SEC_TO_TIME(DuréeConsultation * 60)) > :heure_consultation)
    )');

$verifierDisponibiliteQuery->execute(array(
    'id_medecin' => $id_medecin,
    'date_consultation' => $date_consultation,
    'heure_consultation' => $heure_consultation,
    'heure_fin_consultation' => $heure_fin_consultation
));
$resultat = $verifierDisponibiliteQuery->fetchColumn();

if ($resultat == 0) {
    // Le rendez-vous est disponible, insérer dans la base de données
    $insererQuery = $linkpdo->prepare('INSERT INTO RendezVous (ID_Usager, ID_Medecin, DateConsultation, HeureConsultation, DuréeConsultation) 
                                        VALUES (:id_usager, :id_medecin, :date_consultation, :heure_consultation, :duree_consultation)');
    $resultatInsertion = $insererQuery->execute(array(
        'id_usager' => $id_usager,
        'id_medecin' => $id_medecin,
        'date_consultation' => $date_consultation,
        'heure_consultation' => $heure_consultation,
        'duree_consultation' => $duree_consultation
    ));

    if ($resultatInsertion) {
        echo "<p>Le rendez-vous a été enregistré avec succès.</p>";
    } else {
        echo "<p>Erreur lors de l'enregistrement du rendez-vous.</p>";
        echo "Erreur SQL : " . $insererQuery->errorInfo()[2];
    }
    
} else {
    // Le rendez-vous n'est pas disponible
    echo "<p>Le rendez-vous n'est pas disponible à cette date et heure.</p>";
}

        }
    }
    ?>

<div class="container">
    <?php
    // Afficher le message d'erreur s'il y a lieu
    if (!empty($message_erreur)) {
        echo '<p style="color: red;">' . $message_erreur . '</p>';
    }
    ?>
    
        <form action="#" method="post">
            <label for="usager">Usager :</label>
            <select name="usager" id="usager" required onchange="remplirMedecinReferent()">
                <?php
                // Récupérer la liste des usagers depuis la base de données et les trier par ordre alphabétique
                $usagerQuery = $linkpdo->query('SELECT ID_Usager, nom, prénom, MédecinRéférent FROM Usagers ORDER BY nom, prénom');
                while ($usager = $usagerQuery->fetch()) {
                    echo '<option value="' . $usager['ID_Usager'] . '" data-medecin-referent="' . $usager['MédecinRéférent'] . '">' . $usager['nom'] . ' ' . $usager['prénom'] . '</option>';
                }
                ?>
            </select><br />

            <label for="medecin">Médecin :</label>
            <select name="medecin" id="medecin" required>
                <?php
                // Récupérer la liste des médecins depuis la base de données et les trier par ordre alphabétique
                $medecinQuery = $linkpdo->query('SELECT ID_Medecin, nom, prénom FROM Medecins ORDER BY nom, prénom');
                while ($medecin = $medecinQuery->fetch()) {
                    echo '<option value="' . $medecin['ID_Medecin'] . '">' . $medecin['nom'] . ' ' . $medecin['prénom'] . '</option>';
                }
                ?>
            </select><br />

            <?php
            // Calculer la date limite (aujourd'hui + 2 ans)
            $dateLimite = date('Y-m-d', strtotime('+2 years'));
            // Date actuelle
            $dateActuelle = date('Y-m-d');  
            ?>
            <label for="date">Date :</label>
            <input class="custom-input" type="date" id="date" name="date" min="<?= $dateActuelle ?>" max="<?= $dateLimite ?>" required onchange="desactiverWeekends()">

            <label for="heure">Heure :</label>
            <input class="custom-input" type="time" id="heure" name="heure" required min="08:00" max="18:00">

            <label for="duree">Durée (en minutes) :</label>
            <input class="custom-input" type="number" id="duree" name="duree" value="30" required min="15">

            <input type="submit" value="Enregistrer Rendez-vous">
        </form>
        
        <?php
        // Afficher le message d'erreur s'il y a lieu
        if (!empty($message_erreur)) {
            echo '<p style="color: red;">' . $message_erreur . '</p>';
        }
        ?>
    </div>
</body>
</html>
