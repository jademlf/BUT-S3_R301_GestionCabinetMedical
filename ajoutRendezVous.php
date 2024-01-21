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
            // Récupérer l'élément d'entrée de date du formulaire
            var dateInput = document.getElementById("date");

            // Créer un objet Date à partir de la valeur de l'entrée de date
            var dateSelectionnee = new Date(dateInput.value);

            // Récupérer le jour de la semaine (0 pour Dimanche, 1 pour Lundi, ..., 6 pour Samedi)
            var jourDeSemaine = dateSelectionnee.getDay();

            // Vérifier si le jour de la semaine est un week-end (Samedi = 6, Dimanche = 0)
            if (jourDeSemaine === 6 || jourDeSemaine === 0) {
                // Afficher une alerte indiquant que les rendez-vous ne sont pas disponibles le week-end
                alert("Les rendez-vous ne sont pas disponibles le samedi et le dimanche.");

                // Effacer la valeur de l'entrée de date si le week-end est sélectionné
                dateInput.value = "";
            }
        }

        // Fonction pour préremplir le médecin référent dans la liste déroulante des médecins
        function remplirMedecinReferent() {
            // Récupérer l'élément de sélection des usagers
            var usagerSelect = document.getElementById("usager");

            // Récupérer l'élément de sélection des médecins
            var medecinSelect = document.getElementById("medecin");

            // Récupérer l'ID du médecin référent à partir de l'attribut "data-medecin-referent" de l'option sélectionnée dans la liste des usagers
            var medecinReferentId = usagerSelect.options[usagerSelect.selectedIndex].getAttribute("data-medecin-referent");

            // Trouver l'option correspondante dans la liste des médecins en utilisant l'ID du médecin référent
            var medecinOption = document.querySelector('#medecin option[value="' + medecinReferentId + '"]');

            // Sélectionner le médecin référent par défaut dans la liste déroulante des médecins
            if (medecinOption) {
                medecinOption.selected = true;
            }
        }

        // Fonction pour valider la durée du rendez-vous
        function validerDuree() {
            // Récupérer l'élément d'entrée pour la durée
            var dureeInput = document.getElementById("duree");
            // Récupérer la valeur de la durée et la convertir en nombre entier
            var duree = parseInt(dureeInput.value);
            // Vérifier si la durée n'est pas un nombre ou est en dehors de la plage autorisée (1 à 60 minutes)
            if (isNaN(duree) || duree <= 0 || duree > 60) {
                // Afficher une alerte indiquant la plage autorisée
                alert("La durée du rendez-vous doit être comprise entre 1 et 60 minutes.");
                // Effacer la valeur incorrecte de l'entrée
                dureeInput.value = ""; // Annuler la soumission du formulaire
                return false;
            }
            // Permettre la soumission du formulaire si la durée est valide
            return true;
        }
    </script>
</head>
<body onload="remplirMedecinReferent()">
    <?php 
        include 'menu.php'; // Menu de navigation
        include 'connexion_bd.php'; // Connexion à la BD
        error_reporting(E_ALL); // Activer le rapport d'erreurs
        ini_set('display_errors', 1); // Afficher les erreurs à l'écran
        $message_erreur = ''; // Initialiser le message d'erreur

        // Vérifier si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validation côté serveur
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
                $id_usager = $_POST['usager'];
                $id_medecin = $_POST['medecin'];
                $date_consultation = $_POST['date'];
                $heure_consultation = $_POST['heure'];
                $duree_consultation = $_POST['duree'];

                // Calculer la date et l'heure de fin du rendez-vous en tenant compte de la durée
                $heure_fin_consultation = date('H:i', strtotime("$heure_consultation + $duree_consultation minutes"));

                // Vérifier si le rendez-vous est disponible
                $verifierDisponibiliteQuery = $linkpdo->prepare(
                    'SELECT COUNT(*) AS count FROM rendezVous 
                    WHERE ID_Medecin = :id_medecin 
                    AND DateConsultation = :date_consultation 
                    AND (
                        -- Nouveau rendez-vous commence pendant un rendez-vous existant
                        (HeureConsultation >= :heure_consultation AND HeureConsultation < :heure_fin_consultation) OR
                        -- Nouveau rendez-vous se termine pendant un rendez-vous existant
                        (ADDTIME(HeureConsultation, SEC_TO_TIME(DuréeConsultation * 60)) > :heure_consultation AND ADDTIME(HeureConsultation, SEC_TO_TIME(DuréeConsultation * 60)) <= :heure_fin_consultation) OR
                        -- Nouveau rendez-vous englobe complètement un rendez-vous existant
                        (HeureConsultation <= :heure_consultation AND ADDTIME(HeureConsultation, SEC_TO_TIME(DuréeConsultation * 60)) > :heure_fin_consultation)
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
                    $insererQuery = $linkpdo->prepare('INSERT INTO rendezvous (ID_Usager, ID_Medecin, DateConsultation, HeureConsultation, DuréeConsultation) 
                                                        VALUES (:id_usager, :id_medecin, :date_consultation, :heure_consultation, :duree_consultation)');

                    $resultatInsertion = $insererQuery->execute(array(
                        'id_usager' => $id_usager,
                        'id_medecin' => $id_medecin,
                        'date_consultation' => $date_consultation,
                        'heure_consultation' => $heure_consultation,
                        'duree_consultation' => $duree_consultation
                    ));

                    if ($resultatInsertion) {
                        // Afficher un message de succès si l'insertion a réussi
                        echo '<p style="color: #0097b2;">Le rendez-vous a été enregistré avec succès.</p>';
                    } else {
                        // Afficher un message d'erreur et les détails SQL en cas d'échec de l'insertion
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
        <h1>Ajouter un rendez-vous</h1>
        <section>
            <form action="#" method="post" onsubmit="return validerDuree();">
                <label for="usager">Usager :</label>
                <select name="usager" id="usager" required onchange="remplirMedecinReferent()">
                    <?php
                    // Récupérer la liste des usagers depuis la base de données et les trier par ordre alphabétique
                    $usagerQuery = $linkpdo->query('SELECT ID_Usager, nom, prénom, MédecinRéférent FROM usagers ORDER BY nom, prénom');
                    while ($usager = $usagerQuery->fetch()) {
                        echo '<option value="' . $usager['ID_Usager'] . '" data-medecin-referent="' . $usager['MédecinRéférent'] . '">' . $usager['nom'] . ' ' . $usager['prénom'] . '</option>';
                    }
                    ?>
                </select><br />

                <label for="medecin">Médecin :</label>
                <select name="medecin" id="medecin" required>
                    <?php
                    // Récupérer la liste des médecins depuis la base de données et les trier par ordre alphabétique
                    $medecinQuery = $linkpdo->query('SELECT ID_Medecin, nom, prénom FROM medecins ORDER BY nom, prénom');
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
                <input class="custom-input" type="number" id="duree" name="duree" value="30" required min="15" max="60">

                <input type="submit" value="Enregistrer Rendez-vous">
            </form>
        </section>
    </div>
</body>
</html>
