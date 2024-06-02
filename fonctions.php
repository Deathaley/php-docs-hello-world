<?php
function afficherNavbar($statut) {
    // Définition de la variable $user
    $user = null;

    // Vérification si l'utilisateur est connecté
    if (isset($_SESSION['mail']) && $_SESSION['mail'] !== "") {
        // Requête pour récupérer le statut de l'utilisateur depuis la base de données
        // Connexion à la base de données des whisky
        $madb = new PDO('sqlite:bdd\comptes.sqlite');
        $madb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $madb->prepare("SELECT STATUT FROM utilisateurs WHERE email = :mail");
        $stmt->bindParam(':mail', $_SESSION['mail']);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Vous pouvez également récupérer d'autres informations sur l'utilisateur à partir de la session si nécessaire
        $user = array(
            'mail' => $_SESSION['mail'],
            'STATUT' => $user['STATUT'] ?? null
            // Autres informations utilisateur
        );
    }

    // Si l'utilisateur est connecté
    if (isset($_SESSION['mail']) && $_SESSION['mail'] !== "" && $user['STATUT'] !== null) {
        $mail = htmlspecialchars($_SESSION['mail']); // Évitez les failles XSS
        
        // Navbar pour les utilisateurs connectés
        $navbar = '
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="index.php">La Cave Trégoroise</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img src="./image/user.jpg" alt="User Logo" width="30" height="30">
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="#">Profil</a>
                            <a class="dropdown-item" href="connexion.php">Déconnexion</a>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>';
    }

    // Si l'utilisateur est admin
    if ($user !== null && $user['STATUT'] === 'admin') {
        $navbar = '
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="index.php">La Cave Trégoroise</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="./insertion.php">Insertion</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./suppression.php">Suppression</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Modification</a>
                    </li>
                </ul>
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            '. ($user !== null ? $user['STATUT'] . ': ' . $mail : '') .'
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="deconnexion.php">Déconnexion</a>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>';
        // Ajouter des liens ou des boutons supplémentaires pour l'administration
    }

    echo $navbar;
}

function afficheTableau() {

    try {
        $madb = new PDO('sqlite:bdd/whisky.db');
        $madb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $query = "SELECT * FROM whisky";
        $stmt = $madb->query($query);
        $whiskies = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo '<table class="table table-striped">';
        echo '<thead><tr><th>ID</th><th>Nom</th><th>Distillerie</th><th>Origine</th><th>Annee</th><th>Prix</th><th>Image</th></tr></thead>';
        echo '<tbody>';
        foreach ($whiskies as $whisky) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($whisky['id']) . '</td>';
            echo '<td>' . htmlspecialchars($whisky['nom']) . '</td>';
            echo '<td>' . htmlspecialchars($whisky['distillerie']) . '</td>';
            echo '<td>' . htmlspecialchars($whisky['origine']) . '</td>';
            echo '<td>' . htmlspecialchars($whisky['annee']) . '</td>';
            echo '<td>' . htmlspecialchars($whisky['Prix']) . '</td>';
            echo '<td><img src="' . htmlspecialchars($whisky['image']) . '" alt="' . htmlspecialchars($whisky['nom']) . '" width="100"></td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
    } catch (PDOException $e) {
        echo "Erreur lors de la récupération des données : " . $e->getMessage();
    }
}
?>