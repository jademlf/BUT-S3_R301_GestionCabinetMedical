<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajout d'un Usager</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <!-- Navigation -->
    <?php include 'menu.php'; ?>

    <div class="container">
        <h1>Ajout d'un Usager</h1>

        <?php
         include 'connexion_bd.php';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $civilite = $_POST['civilite'];
            $nom = $_POST['nom'];
            $prenom = $_POST['prenom'];
            $adresse = $_POST['adresse'];
            $cp = $_POST['cp'];
            $ville = $_POST['ville'];
            $dateNaissance = $_POST['dateNaissance'];
            $lieuNaissance = $_POST['lieuNaissance'];
            $medecinId = $_POST['medecin'];

            // Ajout de l'usager
            $insertQuery = $linkpdo->prepare('INSERT INTO Usagers(Civilité, Nom, Prénom, Adresse, Cp, Ville, DateNaissance, LieuNaissance, MédecinRéférent) 
                                                VALUES(:civilite, :nom, :prenom, :adresse, :cp, :ville, :dateNaissance, :lieuNaissance, :medecinId)');
            $insertResult = $insertQuery->execute(array(
                'civilite' => $civilite,
                'nom' => $nom,
                'prenom' => $prenom,
                'adresse' => $adresse,
                'cp' => $cp,
                'ville' => $ville,
                'dateNaissance' => $dateNaissance,
                'lieuNaissance' => $lieuNaissance,
                'medecinId' => $medecinId
            ));

            if ($insertResult) {
                echo "<p>L'usager a été ajouté avec succès.</p>";
            } else {
                echo "<p>Erreur lors de l'ajout de l'usager.</p>";
            }
        }
        ?>

        <!-- Formulaire d'ajout d'un usager -->
        <form action="ajoutUsager.php" method="post">
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
            <input type="text" name="cp" id="cp" required><br />

            <label for="ville">Ville :</label>
            <input type="text" name="ville" id="ville" required><br />

            <label for="dateNaissance">Date de naissance :</label>
            <input type="date" name="dateNaissance" id="dateNaissance" required><br />

            <label for="lieuNaissance">Lieu de naissance :</label>
            <input type="text" name="lieuNaissance" id="lieuNaissance" required><br />

            <label for="medecin">Médecin Référent :</label>
            <select name="medecin" id="medecin" required>
                <?php
                // Récupérer la liste des médecins depuis la base de données
                $medecinQuery = $linkpdo->query('SELECT ID_Medecin, Nom, Prénom FROM Medecins');
                while ($medecin = $medecinQuery->fetch()) {
                    echo '<option value="' . $medecin['ID_Medecin'] . '">' . $medecin['Nom'] . ' ' . $medecin['Prénom'] . '</option>';
                }
                ?>
            </select><br />

            <input type="submit" value="Ajouter l'usager">
            <input type="reset" value="Effacer"> 
        </form>
    </div>
</body>
</html>
