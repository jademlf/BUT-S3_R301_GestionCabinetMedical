<?php include 'verificationUtilisateur.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajout d'un Usager</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link rel="stylesheet" type="text/css" href="menu.css">

    <script>
        function validateNumSecuSociale() {
            // Fonction de validation du numéro de sécurité sociale
            var numSecuSociale = document.getElementById('numSecuSociale').value;
            var regex = /^[1-37-8][0-9]{12}$/;

            if (!regex.test(numSecuSociale)) {
                // Affiche une alerte si le format est invalide
                alert("Format invalide pour le numéro de sécurité sociale.");
                return false; // Empêche la soumission du formulaire
            }
            return true; // Autorise la soumission du formulaire
        }
    </script>
</head>
<body>
    <!-- Navigation -->
    <?php include 'menu.php'; ?>

    <div class="container">
        <h1>Ajout d'un Usager</h1>

        <?php
        // Inclusion du fichier de connexion à la base de données
        include 'connexion_bd.php';

        // Vérification si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupération des données du formulaire
            $civilite = $_POST['civilite'];
            $nom = $_POST['nom'];
            $prenom = $_POST['prenom'];
            $adresse = $_POST['adresse'];
            $cp = $_POST['cp'];
            $ville = $_POST['ville'];
            $dateNaissance = $_POST['dateNaissance'];
            $lieuNaissance = $_POST['lieuNaissance'];
            $numSecuSociale = $_POST['numSecuSociale'];
            $medecinId = $_POST['medecin'];

            // Ajout de l'usager dans la base de données
            $insertQuery = $linkpdo->prepare('INSERT INTO Usagers(Civilité, Nom, Prénom, Adresse, Cp, Ville, DateNaissance, LieuNaissance, NumSecuSociale, MédecinRéférent) 
                                                VALUES(:civilite, :nom, :prenom, :adresse, :cp, :ville, :dateNaissance, :lieuNaissance, :numSecuSociale, :medecinId)');
            $insertResult = $insertQuery->execute(array(
                'civilite' => $civilite,
                'nom' => $nom,
                'prenom' => $prenom,
                'adresse' => $adresse,
                'cp' => $cp,
                'ville' => $ville,
                'dateNaissance' => $dateNaissance,
                'lieuNaissance' => $lieuNaissance,
                'numSecuSociale' => $numSecuSociale,
                'medecinId' => $medecinId
            ));

            if ($insertResult) {
                // Affiche un message de succès si l'insertion est réussie
                echo "<p>L'usager a été ajouté avec succès.</p>";
            } else {
                // Affiche un message d'erreur en cas d'échec de l'insertion
                echo "<p>Erreur lors de l'ajout de l'usager.</p>";
            }
        }
        ?>

        <!-- Formulaire d'ajout d'un usager -->
        <form action="ajoutUsager.php" method="post" onsubmit="return validateNumSecuSociale()">
            <label>Civilité :</label>
            <input type="radio" name="civilite" id="civiliteM" value="M" required>
            <label for="civiliteM">M</label>

            <input type="radio" name="civilite" id="civiliteF" value="F" required>
            <label for="civiliteF">F</label><br />

            <label for="nom">Nom :</label>
            <input type="text" name="nom" id="nom" required><br />

            <label for="prenom">Prénom :</label>
            <input type="text" name="prenom" id="prenom" required><br />

            <label for="adresse">Adresse :</label>
            <input type="text" name="adresse" id="adresse" required><br />

            <label for="cp">Code postal :</label>
            <input type="text" name="cp" id="cp" required pattern="[0-9]{5}" title="Entrez exactement 5 chiffres pour le code postal"><br />

            <label for="ville">Ville :</label>
            <input type="text" name="ville" id="ville" required><br />

            <label for="dateNaissance">Date de naissance :</label>
            <input type="date" name="dateNaissance" id="dateNaissance" max="<?php echo date('Y-m-d'); ?>" required><br />

            <label for="lieuNaissance">Lieu de naissance :</label>
            <input type="text" name="lieuNaissance" id="lieuNaissance" required><br />

            <label for="numSecuSociale">Numéro de sécurité sociale :</label>
            <input type="text" name="numSecuSociale" id="numSecuSociale" required><br />

            <label for="medecin">Médecin Référent :</label>
            <!-- Sélection du médecin référent depuis la base de données -->
            <select name="medecin" id="medecin" required>
                <?php
                // Récupérer la liste des médecins depuis la base de données
                $medecinQuery = $linkpdo->query('SELECT ID_Medecin, Nom, Prénom FROM Medecins');
                while ($medecin = $medecinQuery->fetch()) {
                    echo '<option value="' . $medecin['ID_Medecin'] . '">' . $medecin['Nom'] . ' ' . $medecin['Prénom'] . '</option>';
                }
                ?>
            </select><br />

            <!-- Boutons de soumission et de réinitialisation du formulaire -->
            <input type="submit" value="Ajouter l'usager">
            <input type="reset" value="Effacer"> 
        </form>
    </div>
</body>
</html>
