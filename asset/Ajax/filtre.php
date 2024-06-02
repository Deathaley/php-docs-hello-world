<?php
// Connexion à la base de données des whisky
$madbWhisky = new PDO('sqlite:C:\xampp\htdocs\saewebconti\saeweb\bdd\whisky.db');
$madbWhisky->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
// Récupérer les données filtrées depuis le formulaire
$origine = $_POST['origine']; // par exemple

// Effectuer une requête SQL pour récupérer les données filtrées
// Remplacez cette requête par la vôtre en fonction de votre base de données et de vos critères de filtrage
$stmt = $madbWhisky->prepare("SELECT * FROM whisky WHERE origine = :origine");
$stmt->bindParam(':origine', $origine);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Générer le tableau HTML à partir des données filtrées
$tableHTML = '<table class="table"><thead><tr><th>Colonne 1</th><th>Colonne 2</th><th>Colonne 3</th></tr></thead><tbody>';
foreach ($data as $row) {
    // Pour afficher le nom de la distillerie
    $distillerie = $row['distillerie'];
    
    // Pour afficher le nom du whisky
    $nomWhisky = $row['nom'];
    
    // Pour afficher l'image du whisky sans afficher le chemin
    // Vous pouvez utiliser la fonction basename() pour obtenir le nom de fichier de l'image
    $image = basename($row['image']);
    
    // Générer la ligne du tableau avec les données obtenues
    $tableHTML .= '<tr><td>' . $distillerie . '</td><td>' . $nomWhisky . '</td><td><img src="' . $image . '" alt="' . $nomWhisky . '"></td></tr>';
}
$tableHTML .= '</tbody></table>';

// Renvoyer le tableau HTML
echo $tableHTML;
?>