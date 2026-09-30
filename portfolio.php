<?php

require_once __DIR__ . '/auth.php';

protegerPage();

require_once __DIR__ . '/config.php';
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio - Robbie Lens Photographie</title>
    <link href="style.css" rel="stylesheet" />
    <link href="portfolio.css" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope&family=Montserrat&display=swap" rel="stylesheet">
</head>

<body>
    <?php
require_once 'config.php';

$stmtPaysages = $pdo->prepare("
    SELECT *
    FROM photos
    WHERE categorie = 'paysage'
    ORDER BY id ASC
");

$stmtPaysages->execute();

$paysages = $stmtPaysages->fetchAll(PDO::FETCH_ASSOC);


$stmtPortraits = $pdo->prepare("
    SELECT *
    FROM photos
    WHERE categorie = 'portrait'
    ORDER BY id ASC
");

$stmtPortraits->execute();

$portraits = $stmtPortraits->fetchAll(PDO::FETCH_ASSOC);
?>

    <header>
        <nav>
            <img src="IMG ROBBIE LENS/logo.png" alt="Logo Robbie Lens" />
            <div>
                <a href="index.php">Accueil</a>
                <a href="a-propos.php">À propos</a>
                <a href="Portfolio.php">Portfolio</a>
            </div>
        </nav>
    </header>
    <main>
        <section>
            <h1>Portfolio</h1>
        </section>
        <section class="portfolio-section-photos">
            <h2>Paysages</h2>
             <!-- Créez ici votre grid Paysages -->
                <div class="grid-paysages">
                       <?php foreach ($paysages as $photo): ?>

                    <a href="<?= htmlspecialchars($photo['image']) ?>"class="lien-conteneur-photo">

                        <img
                        src="<?= htmlspecialchars($photo['image']) ?>"
                         alt="<?= htmlspecialchars($photo['titre']) ?>"
                        >

                        <div class="photo-hover">
                            Voir la photo
                        </div>

                    </a>
                        <?php endforeach; ?>
                    
                </div>
                
            <h2>Portraits</h2>
             <!-- Créez ici votre grid Portraits -->
                <div class="grid-portraits">
                      <?php foreach ($portraits as $photo): ?>

                    <a
                       href="<?= htmlspecialchars($photo['image']) ?>"
                       class="lien-conteneur-photo"
                    >

                    <img
                      src="<?= htmlspecialchars($photo['image']) ?>"
                      alt="<?= htmlspecialchars($photo['titre']) ?>"
                    >

               <div class="photo-hover">
                Voir la photo
            </div>

        </a>

    <?php endforeach; ?>
                </div>
        </section>
    </main>
    <footer>
        <a href="index.php" class="lien-icone">
            <img src="IMG ROBBIE LENS/logo.png" alt="Logo Robbie Lens" />
        </a>
        <div>
            <a target="_blank" href="https://twitter.com/" class="lien-icone">
                <img src="IMG ROBBIE LENS/twitter.png" alt="Logo Twitter" />
            </a>
            <a target="_blank" href="https://www.instagram.com/" class="lien-icone">
                <img src="IMG ROBBIE LENS/instagram.png" alt="Logo Instagram" />
            </a>
        </div>
    </footer>
</body>

</html>