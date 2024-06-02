<?php
session_start();
include "fonctions.php";  

// Fonction pour nettoyer et valider les entrées utilisateur
function test_input($data) {
    $data = trim($data); // Supprime les espaces en début et fin de chaîne
    $data = stripslashes($data); // Supprime les antislashs
    $data = htmlspecialchars($data); // Convertit les caractères spéciaux en entités HTML
    return $data;
}

try {
    // Vérifier si l'utilisateur est connecté
    if (isset($_SESSION['mail']) && $_SESSION['mail'] !== "") {
        $mail = htmlspecialchars($_SESSION['mail']); // Évitez les failles XSS

        // Connexion à la base de données SQLite pour les utilisateurs
        $madb = new PDO('sqlite:bdd/comptes.sqlite');
        $madb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Récupération du statut de l'utilisateur
        $stmt = $madb->prepare("SELECT STATUT FROM utilisateurs WHERE email = :mail");
        $stmt->bindParam(':mail', $mail);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Afficher la navbar en fonction du statut de l'utilisateur
        afficherNavbar($user['STATUT'], $madb);
    } else {
        // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
        header('Location: connexion.php');
        exit();
    }

    // Initialiser la variable de l'erreur captcha
    $captcha_err = "";

    // Connexion à la base de données des whisky pour récupérer les négociants
    $madbWhisky = new PDO('sqlite:bdd/whisky.db');
    $madbWhisky->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupération des négociants
    $negociantsQuery = "SELECT id, nom, region FROM negociants";
    $stmt = $madbWhisky->query($negociantsQuery);
    $negociants = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Vérifier le champ "captcha"
        if (empty($_POST["captcha"])) {
            $captcha_err = "Le code captcha est requis";
        } else {
            $captcha = test_input($_POST["captcha"]);
            // Vérifier si le captcha est correct
            if ($captcha != $_SESSION['code']) {
                $captcha_err = "Code captcha incorrect";
            }
        }

        // Si pas d'erreur de captcha, traiter le formulaire
        if (empty($captcha_err)) {
            // Sanitize and validate form inputs
            $nom = htmlspecialchars($_POST['nom']);
            $distillerie = htmlspecialchars($_POST['distillerie']);
            $annee = htmlspecialchars($_POST['annee']);
            $prix = htmlspecialchars($_POST['Prix']);
            $origine = htmlspecialchars($_POST['origine']);
            $negociant_id = htmlspecialchars($_POST['negociant']);
            $nb_bouteilles = (int)$_POST['nb_bouteilles'];

            // Handle image upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
                $img_dir = 'uploads/';
                $img_path = $img_dir . basename($_FILES['image']['name']);
                if (!is_dir($img_dir)) {
                    mkdir($img_dir, 0777, true);
                }
                if (move_uploaded_file($_FILES['image']['tmp_name'], $img_path)) {
                    $image = $img_path;
                } else {
                    throw new Exception("Erreur lors du téléchargement de l'image.");
                }
            } else {
                throw new Exception("Erreur lors de la réception de l'image.");
            }

            // Begin transaction
            $madbWhisky->beginTransaction();

            // Insert whisky into whisky table
            $query = "INSERT INTO whisky (distillerie, origine, annee, nom, Prix, image) VALUES (:distillerie, :origine, :annee, :nom, :Prix, :image)";
            $stmt = $madbWhisky->prepare($query);
            $stmt->bindParam(':distillerie', $distillerie);
            $stmt->bindParam(':annee', $annee);
            $stmt->bindParam(':origine', $origine);
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':Prix', $prix);
            $stmt->bindParam(':image', $image);
            $stmt->execute();

            // Retrieve last inserted whisky ID
            $last_id = $madbWhisky->lastInsertId();

            // Insert into Stock table
            $query = "INSERT INTO Stock (whisky_id, negociant_id, nombre_bouteilles) VALUES (:whisky_id, :negociant_id, :nombre_bouteilles)";
            $stmt = $madbWhisky->prepare($query);
            $stmt->bindParam(':whisky_id', $last_id);
            $stmt->bindParam(':negociant_id', $negociant_id);
            $stmt->bindParam(':nombre_bouteilles', $nb_bouteilles);
            $stmt->execute();

            // Commit transaction
            $madbWhisky->commit();

            echo "Le whisky a été ajouté avec succès!";
        } else {
            // Afficher un message d'erreur pour le captcha
            echo "<div class='alert alert-danger'>$captcha_err</div>";
        }
    }

} catch (PDOException $e) {
    // Gestion des erreurs PDO
    echo "Erreur de connexion à la base de données : " . $e->getMessage();
} catch (Exception $e) {
    // Gestion des autres erreurs
    echo $e->getMessage();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Insertion d'un whisky</title>
    <link rel="stylesheet" href="style.css" media="screen" type="text/css" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js" integrity="sha384-eMNCOe7tC1doHpGoJtKh7z7lGz7fuP4F8nfdFvAOA6Gg/z6Y5J6XqqyGXYM2ntX5" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
    <script src="./asset/js/validation.js"></script>
</head>
<body>

<div class="container mt-5">
    <h2>Ajouter un Whisky</h2>
    <div id="error-message" style="display: none;" class="alert alert-danger" role="alert"></div>
    <form id="whiskyForm" method="post" enctype="multipart/form-data" action="" onsubmit="return validateForm();">
        <div class="form-group">
            <label for="nom">Nom</label>
            <input type="text" class="form-control" id="nom" name="nom" required>
        </div>
        <div class="form-group">
            <label for="origine">Origine</label>
            <select class="form-control" id="origine" name="origine" required>
                <option value="Ecosse">Ecosse</option>
                <option value="France">France</option>
                <option value="Japon">Japon</option>
            </select>
        </div>
        <div class="form-group">
            <label for="annee">Annee</label>
            <input type="text" class="form-control" id="annee" name="annee" required>
        </div>
        <div class="form-group">
            <label for="distillerie">Distillerie</label>
            <input type="text" class="form-control" id="distillerie" name="distillerie" required>
        </div>
        <div class="form-group">
            <label for="Prix">Prix</label>
            <input type="text" class="form-control" id="Prix" name="Prix" required>
        </div>
        <div class="form-group">
            <label for="negociant">Nom du négociant</label>
            <select class="form-control" id="negociant" name="negociant" required>
                <?php foreach ($negociants as $negociant): ?>
                    <option value="<?php echo htmlspecialchars($negociant['id']); ?>"><?php echo htmlspecialchars($negociant['nom'] . " - " . $negociant['region']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="nb_bouteilles">Nombre de bouteilles</label>
            <input type="number" class="form-control" id="nb_bouteilles" name="nb_bouteilles" required>
        </div>
        <div class="form-group">
            <label for="image">Image</label>
            <input type="file" class="form-control" id="image" name="image" accept="image/webp" required>
        </div>
        <div class="form-group">
            <label for="captcha">Captcha</label>
            <input type="text" class="form-control" id="captcha" name="captcha" required>
            <span class="error">* <?php echo $captcha_err;?></span>
            <img src="image.php" onclick="this.src='image.php?' + Math.random();" alt="captcha" style="cursor:pointer;">
        </div>
        <input type="submit" name="submit" value="Submit" class="btn btn-primary">
    </form>
</div>
<div class="container mt-5">
    <h2>Liste des Whiskies</h2>
    <?php afficheTableau(); ?>
</div>

</body>
</html>