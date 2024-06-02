function validateForm_insertion() {
    var nom = document.getElementById('nom').value;
    var origine = document.getElementById('origine').value;
    var annee = document.getElementById('annee').value;
    var distillerie = document.getElementById('distillerie').value;
    var Prix = document.getElementById('Prix').value;
    var negociant = document.getElementById('negociant').value;
    var nb_bouteilles = document.getElementById('nb_bouteilles').value;
    var image = document.getElementById('image').value;
    var captchaInput = document.getElementById('captcha').value;

    // Récupérer l'année actuelle
    var currentYear = new Date().getFullYear();
    
    // Vérifier si l'année est valide
    if (isNaN(annee) || parseInt(annee) > currentYear) {
        document.getElementById('error-message').innerText = "Veuillez entrer une année valide.";
        document.getElementById('error-message').style.display = 'block'; // Afficher le message d'erreur
        return false;
    }

    // Vérifier si le prix est positif
    if (isNaN(Prix) || parseInt(Prix) <= 20) {
        document.getElementById('error-message').innerText = "Le Prix doit être supérieur à 20 Euros.";
        document.getElementById('error-message').style.display = 'block'; // Afficher le message d'erreur
        return false;
    }

    // Vérifier si le nombre de bouteilles est positif
    if (isNaN(nb_bouteilles) || parseInt(nb_bouteilles) <= 0) {
        document.getElementById('error-message').innerText = "Le nombre de bouteilles doit être supérieur à zéro.";
        document.getElementById('error-message').style.display = 'block'; // Afficher le message d'erreur
        return false;
    }

    // Vérifier le format de l'image
    var allowedExtensions = /(\.jpg|\.jpeg|\.png|\.gif|\.webp)$/i;
    if (!allowedExtensions.exec(image)) {
        document.getElementById('error-message').innerText = "Veuillez télécharger une image au format JPG, JPEG, Webp, PNG ou GIF.";
        document.getElementById('error-message').style.display = 'block'; // Afficher le message d'erreur
        return false;
    }

    // Vérifier le captcha
    if (captchaInput !== "<?php echo $_SESSION['code']; ?>" || nom === '' || origine === '' || annee === '' || distillerie === '' || Prix === '' || negociant === '' || nb_bouteilles === '' || image === '') {
        document.getElementById('captcha-error').style.display = 'block';
        document.getElementById('error-message').innerText = 'Veuillez remplir tous les champs.';
        return false;
    } else {
        document.getElementById('captcha-error').style.display = 'none';
        return true;
    }
}
