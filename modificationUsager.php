<?php include 'verificationUtilisateur.php'; ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modification Usager</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link rel="stylesheet" type="text/css" href="menu.css">
</head>
<body>
    <?php 
        include 'menu.php'; // Menu de navigation
        include 'connexion_bd.php'; // Connexion à la BD
    ?>  

    <h1>Modification Usager</h1>
    <section>
        <?php
            $message = ''; // Initialiser le message

            if (isset($_GET['id'])) {
                // Récupérer l'identifiant de l'usager depuis l'URL
                $id = $_GET['id'];

                // Sélectionner les informations de l'usager correspondant dans la base de données
                $query = $linkpdo->prepare('SELECT * FROM usagers WHERE ID_Usager = :id');
                $query->execute(array('id' => $id));
                $usager = $query->fetch();

                if ($usager) {
                    // Traitement du formulaire de modification lorsqu'il est soumis
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        $nouvelleCivilite = $_POST['civilite'];
                        $nouveauNom = $_POST['nom'];
                        $nouveauPrenom = $_POST['prenom'];
                        $nouvelleAdresse = $_POST['adresse'];
                        $nouveauCP = $_POST['cp'];
                        $nouvelleVille = $_POST['ville'];
                        $nouveauNumSecuSociale = $_POST['numSecuSociale'];
                        $nouvelleDateNaissance = $_POST['dateNaissance'];
                        $nouveauLieuNaissance = $_POST['lieuNaissance'];
                        $nouveauMedecinReferent = $_POST['medecin']; // Ajout du champ médecin référent

                        // Utilisation de l'instruction SQL UPDATE pour mettre à jour l'usager
                        $updateQuery = $linkpdo->prepare('UPDATE usagers SET Civilité = ?, Nom = ?, Prénom = ?, Adresse = ?, Cp = ?, Ville = ?, NumSecuSociale = ?, DateNaissance = ?, LieuNaissance = ?, MédecinRéférent = ? WHERE ID_Usager = ?');
                        $updateQuery->execute([$nouvelleCivilite, $nouveauNom, $nouveauPrenom, $nouvelleAdresse, $nouveauCP, $nouvelleVille, $nouveauNumSecuSociale, $nouvelleDateNaissance, $nouveauLieuNaissance, $nouveauMedecinReferent, $id]);

                        // Réexécutez la requête pour obtenir les informations mises à jour
                        $query->execute(array('id' => $id));
                        $usager = $query->fetch();

                        $message = 'L\'usager a été mis à jour avec succès.';
                    }

                    // Afficher le formulaire de modification avec les données mises à jour
                    echo '<form method="POST" action="modificationUsager.php?id=' . $id . '">
                            <input type="hidden" name="id" value="' . $id . '">
                            <label>Civilité :</label>
                            <input type="radio" name="civilite" value="M" ' . ($usager['Civilité'] == 'M' ? 'checked' : '') . '> M
                            <input type="radio" name="civilite" value="F" ' . ($usager['Civilité'] == 'F' ? 'checked' : '') . '> F<br />

                            <label for="nom">Nom :</label>
                            <input type="text" name="nom" id="nom" value="' . $usager['Nom'] . '">
                            
                            <label for="prenom">Prénom :</label>
                            <input type="text" name="prenom" id="prenom" value="' . $usager['Prénom'] . '">
                            
                            <label for="adresse">Adresse :</label>
                            <input type="text" name="adresse" id="adresse" value="' . $usager['Adresse'] . '">
                            
                            <label for="cp">Code Postal :</label>
                            <input type="text" name="cp" id="cp" value="' . $usager['Cp'] . '">
                            
                            <label for="ville">Ville :</label>
                            <input type="text" name="ville" id="ville" value="' . $usager['Ville'] . '">
                            
                            <label for="numSecuSociale">Numéro de sécurité sociale :</label>
                            <input type="text" name="numSecuSociale" id="numSecuSociale" value="' . $usager['NumSecuSociale'] . '">
                            
                            <label for="dateNaissance">Date de naissance :</label>
                            <input type="date" name="dateNaissance" id="dateNaissance" value="' . $usager['DateNaissance'] . '"><br />
                            
                            <label for="lieuNaissance">Lieu de naissance :</label>
                            <input type="text" name="lieuNaissance" id="lieuNaissance" value="' . $usager['LieuNaissance'] . '">
                            
                            <label for="medecin">Médecin Référent :</label>
                            <select name="medecin" id="medecin" required>';
                            
                                // Récupérer la liste des médecins depuis la base de données
                                $medecinQuery = $linkpdo->query('SELECT ID_Medecin, Nom, Prénom FROM medecins');
                                while ($medecin = $medecinQuery->fetch()) {
                                    echo '<option value="' . $medecin['ID_Medecin'] . '" ' . ($usager['MédecinRéférent'] == $medecin['ID_Medecin'] ? 'selected' : '') . '>' . $medecin['Nom'] . ' ' . $medecin['Prénom'] . '</option>';
                                }
                                
                            echo '</select>
                            
                            <br /><input type="submit" value="Modifier">
                        </form>';
                } else {
                    echo '<p>L\'usager spécifié n\'existe pas.</p>';
                }
            } else {
                echo '<p>Aucun identifiant d\'usager spécifié.</p>';
            }

            // Afficher le message de succès ici
            echo '<p style="color: #0097b2;">' . $message . '</p>';
        ?>
    </section>
</body>
</html>
