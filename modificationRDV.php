<?php include 'verificationUtilisateur.php'; ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion Cabinet Médical - Modifier Rendez-vous</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link rel="stylesheet" type="text/css" href="menu.css">
    <script>
        // Fonction pour désactiver les week-ends dans l'entrée de date
        function desactiverWeekends() {
            // Récupérer l'élément de champ de date
            var dateInput = document.getElementById("date");

            // Convertir la valeur de la date en objet Date
            var dateSelectionnee = new Date(dateInput.value);

            // Obtenir le jour de la semaine (Dimanche = 0, Lundi = 1, ..., Samedi = 6)
            var jourDeSemaine = dateSelectionnee.getDay();

            // Désactiver les week-ends (Samedi = 6, Dimanche = 0)
            if (jourDeSemaine === 6 || jourDeSemaine === 0) {
                // Afficher une alerte et effacer l'entrée si le week-end est sélectionné
                alert("Les rendez-vous ne sont pas disponibles le samedi et le dimanche.");
                dateInput.value = "";
            }
        }

        // Fonction pour préremplir le médecin référent dans la liste déroulante des médecins
        function remplirMedecinReferent() {
            // Récupérer les éléments de sélection des usagers et des médecins
            var usagerSelect = document.getElementById("usager");
            var medecinSelect = document.getElementById("medecin");

            // Obtenir l'ID du médecin référent à partir des attributs de données de l'option usager sélectionnée
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
    <?php 
        include 'menu.php'; // Menu de navigation
        include 'connexion_bd.php'; // Connexion à la BD
    ?>

    <h1> Modifier un rendez-vous </h1>
    <section>
        <?php 
            error_reporting(E_ALL); // Activer le rapport d'erreurs
            ini_set('display_errors', 1); // Afficher les erreurs à l'écran
            $message_erreur = ''; // Initialiser le message d'erreur
            $message_succes = ''; // Initialiser le message de succès

            // Récupérer l'identifiant du rendez-vous depuis l'URL
            $idRendezVous = isset($_GET['id']) ? $_GET['id'] : null;

            // Vérifier si le formulaire a été soumis
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Validation des champs obligatoires
                $champs_obligatoires = ['usager', 'medecin', 'date', 'heure', 'duree'];

                foreach ($champs_obligatoires as $champ) {
                    // Vérifier si chaque champ obligatoire est rempli
                    if (empty($_POST[$champ])) {
                        $message_erreur = "Veuillez remplir tous les champs obligatoires.";
                        break;
                    }
                }

                // Si aucune erreur de validation n'a été trouvée
                if (empty($message_erreur)) {
                    // Récupérer les valeurs du formulaire
                    $usagerId = $_POST['usager'];
                    $medecinId = $_POST['medecin'];
                    $date = $_POST['date'];
                    $heure = $_POST['heure'];
                    $duree = $_POST['duree'];

                    // Vérification du chevauchement
                    $chevauchementQuery = $linkpdo->prepare('SELECT COUNT(*) AS count FROM rendezvous 
                        WHERE ID_RendezVous != :idRendezVous 
                        AND ID_Medecin = :medecinId 
                        AND DateConsultation = :date 
                        AND (
                            (HeureConsultation >= :heure AND HeureConsultation < ADDTIME(:heure, SEC_TO_TIME(:duree * 60))) OR
                            (ADDTIME(HeureConsultation, SEC_TO_TIME(DuréeConsultation * 60)) > :heure AND ADDTIME(HeureConsultation, SEC_TO_TIME(DuréeConsultation * 60)) <= ADDTIME(:heure, SEC_TO_TIME(:duree * 60))) OR
                            (HeureConsultation <= :heure AND ADDTIME(HeureConsultation, SEC_TO_TIME(DuréeConsultation * 60)) > ADDTIME(:heure, SEC_TO_TIME(:duree * 60)))
                        )');

                    $chevauchementQuery->execute([
                        'idRendezVous' => $idRendezVous,
                        'medecinId' => $medecinId,
                        'date' => $date,
                        'heure' => $heure,
                        'duree' => $duree
                    ]);

                    $chevauchementResult = $chevauchementQuery->fetch();

                    if ($chevauchementResult['count'] == 0) {
                        // Aucun chevauchement, procéder à la mise à jour du rendez-vous
                        $updateQuery = $linkpdo->prepare('UPDATE rendezvous SET ID_Usager = ?, ID_Medecin = ?, 
                                        DateConsultation = ?, HeureConsultation = ?, DuréeConsultation = ? 
                                        WHERE ID_RendezVous = ?');
                        $updateQuery->execute([$usagerId, $medecinId, $date, $heure, $duree, $idRendezVous]);

                        $message_succes = 'Le rendez-vous a été mis à jour avec succès.';
                    } else {
                        $message_erreur = 'Le rendez-vous chevauche un autre rendez-vous existant. Veuillez choisir une autre date ou heure.';
                    }
                }
            }

            // Récupérer les détails du rendez-vous pour affichage
            $queryRendezVous = $linkpdo->prepare('SELECT * FROM rendezvous WHERE ID_RendezVous = :idRendezVous');
            $queryRendezVous->execute(['idRendezVous' => $idRendezVous]);
            $rendezVous = $queryRendezVous->fetch();

            // Récupérer la liste des usagers
            $queryUsagers = $linkpdo->query('SELECT * FROM usagers');
            $usagers = $queryUsagers->fetchAll();

            // Récupérer la liste des médecins
            $queryMedecins = $linkpdo->query('SELECT * FROM medecins');
            $medecins = $queryMedecins->fetchAll();
        ?>

        <div class="container">
            <?php
                // Afficher le message d'erreur s'il y en a un
                if (!empty($message_erreur)) {
                    echo '<p style="color: red;">' . $message_erreur . '</p>';
                }
                // Afficher le message de succès s'il y en a un
                if (!empty($message_succes)) {
                    echo '<p style="color: #0097b2;">' . $message_succes . '</p>';
                }
            ?>

            <!-- Formulaire de modification d'un rendez-vous -->
            <form action="#" method="post">
                <label for="usager">Usager :</label>
                <select id="usager" name="usager" required>
                    <?php foreach ($usagers as $usager) : ?>
                        <option value="<?= $usager['ID_Usager'] ?>" <?= ($usager['ID_Usager'] == $rendezVous['ID_Usager']) ? 'selected' : '' ?>>
                            <?= $usager['Nom'] . ' ' . $usager['Prénom'] ?>
                        </option>
                    <?php endforeach; ?>
                </select><br>

                <label for="medecin">Médecin :</label>
                <select id="medecin" name="medecin" required>
                    <?php foreach ($medecins as $medecin) : ?>
                        <option value="<?= $medecin['ID_Medecin'] ?>" <?= ($medecin['ID_Medecin'] == $rendezVous['ID_Medecin']) ? 'selected' : '' ?>>
                            <?= $medecin['Nom'] . ' ' . $medecin['Prénom'] ?>
                        </option>
                    <?php endforeach; ?>
                </select><br>

                <label for="date">Date :</label>
                <input class="custom-input" type="date" id="date" name="date" value="<?= $rendezVous['DateConsultation'] ?>" min="<?= $dateActuelle ?>" max="<?= $dateLimite ?>" required onchange="desactiverWeekends()">

                <label for="heure">Heure :</label>
                <input class="custom-input" type="time" id="heure" name="heure" value="<?= $rendezVous['HeureConsultation'] ?>" required min="08:00" max="18:00">

                <label for="duree">Durée (en minutes) :</label>
                <input class="custom-input" type="number" id="duree" name="duree" value="<?= $rendezVous['DuréeConsultation'] ?>" required min="15">

                <input type="submit" value="Enregistrer Rendez-vous">
            </form>
        </div>
    </section>
</body>
</html>