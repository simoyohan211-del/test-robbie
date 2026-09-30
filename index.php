<?php

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/config.php';


/* =========================
   PHOTOS DE L'ACCUEIL
========================= */

$stmtPhotos = $pdo->prepare("
    SELECT *
    FROM photos
    WHERE categorie = 'accueil'
    ORDER BY id ASC
    LIMIT 6
");

$stmtPhotos->execute();

$photosAccueil = $stmtPhotos->fetchAll(PDO::FETCH_ASSOC);


/* =========================
   FORMULAIRE DE CONTACT
========================= */

$messageEnvoye = false;
$erreurMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* Vérifier que le visiteur est connecté */

    if (!visiteurConnecte()) {

        header('Location: connexion.php');
        exit;
    }


    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');


    /* Vérification des champs */

    if ($nom === '' || $email === '' || $message === '') {

        $erreurMessage = "Tous les champs sont obligatoires.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $erreurMessage = "Veuillez entrer une adresse e-mail valide.";

    } else {

        $stmt = $pdo->prepare("
            INSERT INTO message
            (nom, email, message)
            VALUES
            (:nom, :email, :message)
        ");

        $stmt->execute([
            ':nom' => $nom,
            ':email' => $email,
            ':message' => $message
        ]);

        $messageEnvoye = true;
    }
}

?>


<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Accueil - Robbie Lens Photographie</title>

    <link href="style.css" rel="stylesheet">

    <link href="index.css" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Manrope&family=Montserrat&display=swap"
        rel="stylesheet"
    >

</head>


<body>


<header>

    <nav>

        <img
            src="IMG ROBBIE LENS/logo.png"
            alt="Logo Robbie Lens"
        >


        <div>

            <a href="index.php">
                Accueil
            </a>

            <a href="a-propos.php">
                À propos
            </a>


            <?php if (visiteurConnecte()): ?>

                <a href="portfolio.php">
                    Portfolio
                </a>

            <?php else: ?>

                <a href="connexion.php?redirect=portfolio.php">
                    Portfolio
                </a>

            <?php endif; ?>


            <?php if (visiteurConnecte()): ?>

                <a href="deconnexion.php">
                    Déconnexion
                </a>

            <?php else: ?>

                <a href="connexion.php">
                    Connexion
                </a>

                <a href="inscription.php">
                    Inscription
                </a>

            <?php endif; ?>

        </div>

    </nav>

</header>


<main>


    <!-- =========================
         INTRODUCTION
    ========================= -->

    <section class="accueil-introduction">

        <div>

            <h1>
                Robbie Lens Photographie
            </h1>


            <p>

                Où <em>professionalisme</em> s’allie avec
                <em>passion</em>.

                Depuis plus de 5 ans maintenant, j’exerce mon métier
                avec la passion qui m’anime :

                capturer l’essence de chaque instant.

            </p>


            <a
                href="#contact"
                class="cta"
            >
                UN PROJET ? ÉCRIVEZ-MOI
            </a>

        </div>


        <img
            src="IMG ROBBIE LENS/robbie-lens (1).png"
            alt="Portrait avec effet de la photographe Robbie Lens"
        >

    </section>



    <!-- =========================
         PHOTOS DE L'ACCUEIL
    ========================= -->

    <section class="accueil-photos">

        <h2>
            Mon dernier projet
        </h2>


        <div class="galerie-accueil">


            <?php foreach ($photosAccueil as $photo): ?>

                <img
                    src="<?= htmlspecialchars($photo['image']) ?>"
                    alt="<?= htmlspecialchars($photo['titre']) ?>"
                >

            <?php endforeach; ?>


        </div>

    </section>



    <!-- =========================
         CONTACT
    ========================= -->

    <section
        id="contact"
        class="section-contact"
    >

        <h2>
            Parlons de votre projet
        </h2>



        <?php if ($messageEnvoye): ?>

            <p id="message-succes">

                Votre message a bien été envoyé.

            </p>

        <?php endif; ?>



        <?php if ($erreurMessage !== ''): ?>

            <p id="message-erreur">

                <?= htmlspecialchars($erreurMessage) ?>

            </p>

        <?php endif; ?>



        <?php if (visiteurConnecte()): ?>


            <!-- VISITEUR CONNECTÉ -->

            <form
                method="post"
                action="index.php#contact"
            >


                <div class="form-nom-email">


                    <div class="form-column">

                        <label for="nom">
                            Nom
                        </label>


                        <input
                            type="text"
                            name="nom"
                            id="nom"
                            value="<?= htmlspecialchars($_SESSION['user_nom'] ?? '') ?>"
                            readonly
                        >

                    </div>



                    <div class="form-column">

                        <label for="email">
                            Email
                        </label>


                        <input
                            type="email"
                            name="email"
                            id="email"
                            value="<?= htmlspecialchars($_SESSION['user_email'] ?? '') ?>"
                            readonly
                        >

                    </div>


                </div>


                <label for="message">
                    Message
                </label>


                <textarea
                    name="message"
                    id="message"
                    rows="10"
                    required
                ></textarea>


                <input
                    type="submit"
                    value="ENVOYER"
                    class="cta"
                >


            </form>



        <?php else: ?>


            <!-- VISITEUR NON CONNECTÉ -->


            <div class="contact-connexion">

                <p>
                    Vous devez créer un compte ou vous connecter
                    avant de pouvoir envoyer un message.
                </p>


                <div>

                    <a
                        href="connexion.php?redirect=index.php"
                        class="cta"
                    >
                        SE CONNECTER
                    </a>


                    <a
                        href="inscription.php"
                        class="cta"
                    >
                        CRÉER UN COMPTE
                    </a>

                </div>

            </div>


        <?php endif; ?>


    </section>


</main>



<!-- =========================
     FOOTER
========================= -->

<footer>


    <img
        src="IMG ROBBIE LENS/logo.png"
        alt="Logo Robbie Lens"
    >


    <div>


        <a
            target="_blank"
            href="https://twitter.com/"
            class="lien-icone"
        >

            <img
                src="IMG ROBBIE LENS/twitter.png"
                alt="Logo Twitter"
            >

        </a>



        <a
            target="_blank"
            href="https://www.instagram.com/"
            class="lien-icone"
        >

            <img
                src="IMG ROBBIE LENS/instagram.png"
                alt="Logo Instagram"
            >

        </a>


    </div>


</footer>



<!-- =========================
     JAVASCRIPT
========================= -->

<script>

document.addEventListener("DOMContentLoaded", function () {

    const messageSucces =
        document.getElementById("message-succes");


    if (messageSucces) {

        setTimeout(function () {

            messageSucces.style.opacity = "0";


            setTimeout(function () {

                messageSucces.remove();

            }, 500);


        }, 10000);

    }

});

</script>


</body>

</html>