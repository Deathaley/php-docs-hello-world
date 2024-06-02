<?php
session_start();
$successLog = 'logs/success.log';
$failureLog = 'logs/failure.log';

// Fonction pour vérifier le mot de passe
function verifierMotDePasse($pass) {
    $majuscule = preg_match('@[A-Z]@', $pass);
    $special = preg_match('@[^\w]@', $pass);
    return $majuscule && $special;
}

if (isset($_POST['mail']) && isset($_POST['pass'])) {
    $retour = false;
    $mail = $_POST['mail'];
    $pass = $_POST['pass'];

    try {
        // Connexion à la base de données
        $madb = new PDO('sqlite:bdd/comptes.sqlite');
        $madb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if ($mail !== "" && $pass !== "") {
            // Vérification du format du mot de passe
            if (!verifierMotDePasse($pass)) {
                header('Location: connexion.php?erreur=3'); // mot de passe ne respecte pas les critères
                exit();
            }

            // Utilisation de requêtes préparées pour éviter les injections SQL
            $requete = $madb->prepare("SELECT EMAIL, PASS FROM utilisateurs WHERE EMAIL = :mail AND PASS = :pass");
            $requete->execute([':mail' => $mail, ':pass' => $pass]);

            $tableau_assoc = $requete->fetchAll(PDO::FETCH_ASSOC);

            if (sizeof($tableau_assoc) != 0) {
                $retour = true;
                $_SESSION['mail'] = $mail;
                $logMessage = '[' . date('Y-m-d H:i:s') . '] Connexion réussie pour l\'utilisateur ' . $mail . "\n";
                error_log($logMessage, 3, $successLog);
                header('Location: index.php');
                exit();
            } else {
                $logMessage = '[' . date('Y-m-d H:i:s') . '] Tentative de connexion échouée pour l\'utilisateur ' . $mail . "\n";
                error_log($logMessage, 3, $failureLog);
                header('Location: connexion.php?erreur=1'); // utilisateur ou mot de passe incorrect
                exit();
            }
        } else {
            header('Location: connexion.php?erreur=2'); // utilisateur ou mot de passe vide
            exit();
        }
    } catch (PDOException $e) {
        // Gestion des erreurs de connexion
        echo 'Erreur : ' . $e->getMessage();
    }
} else {
    header('Location: connexion.php');
    exit();
}
?>