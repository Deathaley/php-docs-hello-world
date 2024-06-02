<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="style.css" media="screen" type="text/css" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js" integrity="sha384-eMNCOe7tC1doHpGoJtKh7z7lGz7fuP4F8nfdFvAOA6Gg/z6Y5J6XqqyGXYM2ntX5" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <title>La Cave Trégoroise</title>
</head>
<body>
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
?>
    </div>
    <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
    <ol class="carousel-indicators">
        <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
        <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
        <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
    </ol>
    <div class="carousel-inner">
        <div class="carousel-item active">
        <img src="./image/vinblanc.jpg" class="d-block w-100" alt="Wine 1">
        </div>
        <div class="carousel-item">
        <img src="./image/vinrouge.webp" class="d-block w-100" alt="Wine 2">
        </div>
        <div class="carousel-item small-item">
        <img src="./image/vinrose.webp" class="d-block w-100" alt="Wine 3">
        </div>
    </div>
    <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
    </a>
    <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
    </a>
    </div>
    <div class="container mt-5">
    <div class="row">
        <div class="col-md-6">
        <h1>Bienvenue sur La Cave Trégoroise</h1>
        <p>Vente de vin : rouge,rosé et blanc</p>
        <a href="#" class="btn btn-primary">Découvrez nos sélections de vins</a>
        </div>
    </div>
    </div>
    <div class="container mt-5">
    <h2>afficher les vins</h2>
    <form id="filterForm" method="POST">
        <!-- Ajoutez ici les champs de filtrage, par exemple: -->
        <div class="form-group">
            <label for="origine">Catégorie :</label>
            <select class="form-control" id="origine" name="origine">
                <option value="Écosse">Ecosse</option>
                <option value="France">France</option>
                <option value="Japon">Japon</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Filtrer</button>
    </form>
</div>

<!-- Tableau HTML pour afficher les données filtrées -->
<div class="container mt-5" id="filteredData">
    <!-- Les données filtrées seront affichées ici -->
</div>

<script>
    $(document).ready(function(){
        $('#filterForm').submit(function(event){
            event.preventDefault(); // Empêche le formulaire de se soumettre normalement
            var formData = $(this).serialize();
            // Envoie de la requête AJAX pour récupérer les données filtrées
            $.ajax({
                type: 'POST',
                url: './asset/Ajax/filtre.php', // Chemin vers le fichier PHP de traitement des données filtrées
                data: formData,
                success: function(response){
                    $('#filteredData').html(response); // Affiche les données filtrées dans le div
                }
            });
        });
    });
</script>