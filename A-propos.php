<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8" >
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>À propos - Robbie Lens Photographie</title>
    <link href="style.css" rel="stylesheet" >
    <link href="A-propos.css" rel="stylesheet" >
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope&family=Montserrat&display=swap" rel="stylesheet">
</head>

<body>
    <?php

require_once __DIR__ . '/config.php';

$stmtServices = $pdo->prepare("
    SELECT *
    FROM services
    ORDER BY id ASC
");

$stmtServices->execute();

$services = $stmtServices->fetchAll(PDO::FETCH_ASSOC);

$stmtTarifs = $pdo->prepare("
    SELECT *
    FROM tarifs
    ORDER BY id ASC
");

$stmtTarifs->execute();

$tarifs = $stmtTarifs->fetchAll(PDO::FETCH_ASSOC);

?>

    <header>
        <nav>
            <a href="index.html" class="lien-icone">
                <img src="IMG ROBBIE LENS/logo.png" alt="Logo Robbie Lens" >
            </a>
            
            <div>
                <a href="index.php">Accueil</a>
                <a href="a-propos.php">À propos</a>
                <a href="portfolio.php">Portfolio</a>
            </div>
        </nav>
    </header>
    <main class="a-propos-main">
        <section>
            <h1>À propos</h1>
            <div class="carre-contenu">
                <p>
                    Photographe depuis plus de 5 ans, je réalise des reportages aux photos dynamiques et pertinentes pour vos projets de communication. Créativité, qualité, et sérénité pour vous! Je gère tout, depuis la direction artistique, la réalisation du reportage jusqu’à la livraison de vos photos retouchées, prêtes à l’emploi.
                </p>
                <h2>Services</h2>
                <ul>

    <?php foreach ($services as $service): ?>

        <li>
            <?= htmlspecialchars($service['nom']) ?>
        </li>

    <?php endforeach; ?>

</ul>
                
            </div>
            <div>
                <a href="Portfolio.php" class="cta">VOIR MON PORTFOLIO</a>
            </div>
        </section>
        <section class="section-tarifs">
            <h2>Tarifs</h2>
            <table>
                <thead>
                    <tr>
                        <th>Désignation</th>
                        <th>Quantité</th>
                        <th>Prix</th>
                    </tr>
                </thead>
                <tbody>

    <?php foreach ($tarifs as $tarif): ?>

        <tr>

            <td>
                <?= htmlspecialchars($tarif['designation']) ?>
            </td>

            <td>
                <?= htmlspecialchars($tarif['quantite']) ?>
            </td>

            <td>
                <?= htmlspecialchars($tarif['prix']) ?>
            </td>

        </tr>

    <?php endforeach; ?>

</tbody>
                
            </table>

        </section>
    </main>
    <footer>
        <a href="index.php" class="lien-icone">
            <img src="IMG ROBBIE LENS/logo.png" alt="Logo Robbie Lens" >
        </a>
        <div>
            <a target="_blank" href="https://twitter.com/" class="lien-icone">
                <img src="IMG ROBBIE LENS/twitter.png" alt="Logo Twitter" >
            </a>
            <a target="_blank" href="https://www.instagram.com/" class="lien-icone">
                <img src="IMG ROBBIE LENS/instagram.png" alt="Logo Instagram" >
            </a>
        </div>
    </footer>
</body>

</html>