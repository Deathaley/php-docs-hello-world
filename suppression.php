<?php
session_start();
include "fonctions.php";

// Vérifier le statut de l'utilisateur
if (isset($_SESSION['mail']) && $_SESSION['mail'] !== "") {
    $mail = htmlspecialchars($_SESSION['mail']); // Évitez les failles XSS

    // Connexion à la base de données SQLite
    try {
        $madb = new PDO('sqlite:bdd/comptes.sqlite');
        $madb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Récupération du statut de l'utilisateur
        $stmt = $madb->prepare("SELECT STATUT FROM utilisateurs WHERE email = :mail");
        $stmt->bindParam(':mail', $mail);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Afficher la navbar en fonction du statut de l'utilisateur
        afficherNavbar($user['STATUT']);

    } catch (PDOException $e) {
        // Gestion des erreurs PDO
        echo "Erreur de connexion à la base de données : " . $e->getMessage();
    }
} else {
    header('Location: connexion.php');
    exit();
}

// Récupération de la liste des whiskies depuis la base de données
try {
    // Connexion à la base de données des whisky
    $madb = new PDO('sqlite:bdd/whisky.db');
    $madb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupération de la liste des éléments à afficher
    $query = "SELECT id, nom, distillerie, annee, origine, Prix FROM whisky";
    $stmt = $madb->query($query);
    $whiskies = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Gestion des erreurs PDO
    echo "Erreur de connexion à la base de données : " . $e->getMessage();
}

// Vérification du captcha et suppression des whiskies sélectionnés si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['captcha']) && isset($_SESSION['code']) && $_POST['captcha'] === $_SESSION['code']) {
        // Le captcha est valide

        // Vérification si des IDs de whisky ont été sélectionnés pour suppression
        if (isset($_POST['delete_ids'])) {
            try {
                // Connexion à la base de données des whisky
                $madb = new PDO('sqlite:bdd/whisky.db');
                $madb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Récupération des IDs des éléments à supprimer depuis le formulaire
                $ids_to_delete = $_POST['delete_ids'];

                // Suppression de chaque élément sélectionné dans la table whisky
                foreach ($ids_to_delete as $id) {
                    $query = "DELETE FROM whisky WHERE id = :id";
                    $stmt = $madb->prepare($query);
                    $stmt->bindParam(':id', $id);
                    $stmt->execute();
                }

                // Rafraîchir la page après la suppression
                header('Location: suppression.php');
                exit();
            } catch (PDOException $e) {
                // Gestion des erreurs PDO
                echo "Erreur de connexion à la base de données : " . $e->getMessage();
            }
        }
    } else {
        // Le captcha est invalide
        echo "Code captcha incorrect.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Suppression d'un whisky</title>
    <link rel="stylesheet" href="style.css" media="screen" type="text/css" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
</head>
<body>
<div class="container mt-5">
    <h2>Suppression d'un Whisky</h2>
    <form method="post">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nom</th>
                    <th>Distillerie</th>
                    <th>Année</th>
                    <th>Origine</th>
                    <th>Prix</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($whiskies)): ?>
                    <?php foreach ($whiskies as $whisky): ?>
                        <tr>
                            <td><input type="checkbox" name="delete_ids[]" value="<?php echo $whisky['id']; ?>"></td>
                            <td><?php echo $whisky['nom']; ?></td>
                            <td><?php echo $whisky['distillerie']; ?></td>
                            <td><?php echo $whisky['annee']; ?></td>
                            <td><?php echo $whisky['origine']; ?></td>
                            <td><?php echo $whisky['Prix']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <!-- Champ Captcha -->
        <div class="form-group">
            <label for="captcha">Captcha</label>
            <input type="text" class="form-control" id="captcha" name="captcha" required>
            <img src="image.php" alt="captcha">
        </div>
        <button type="submit" class="btn btn-danger">Valider la suppression</button>
    </form>
</div>
<div class="container mt-5">
    <h2>Liste des Whiskies</h2>
    <?php afficheTableau(); ?>
</div>

</body>
</html>