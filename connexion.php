<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css" media="screen" type="text/css" />
    <title>La Cave Trégoroise</title>
</head>
<body class="connexion-page">
    <div id="container">
        <!-- zone de connexion -->
        <form action="verification.php" method="POST">
            <h1>Connexion</h1>

            <label for="mail"><b>Nom d'utilisateur</b></label>
            <input type="text" placeholder="Entrer le nom d'utilisateur" name="mail" id="mail" required>

            <label for="pass"><b>Mot de passe</b></label>
            <input type="password" placeholder="Entrer le mot de passe" name="pass" id="pass" required>

            <input type="submit" id="submit" value="LOGIN">
            <?php
            if (isset($_GET['erreur'])) {
                $err = $_GET['erreur'];
                if ($err == 1) {
                    echo "<p style='color:red'>Email ou mot de passe incorrect</p>";
                } elseif ($err == 2) {
                    echo "<p style='color:red'>Veuillez remplir tous les champs</p>";
                } elseif ($err == 3) {
                    echo "<p style='color:red'>Le mot de passe doit contenir au moins une majuscule et un caractère spécial</p>";
                }
            }
            ?>
        </form>
    </div>
</body>
</html>