<?php

session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../config.php';


/* =========================
   SUPPRESSION D'UNE PHOTO
========================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = (int) ($_POST['id'] ?? 0);

    if ($id > 0) {

        $stmt = $pdo->prepare("
            SELECT image
            FROM photos
            WHERE id = :id
        ");

        $stmt->execute([
            ':id' => $id
        ]);

        $photo = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($photo) {

            /* Nettoyer le chemin */
            $imageRelative = str_replace(
                '\\',
                '/',
                $photo['image']
            );

            $imageRelative = ltrim(
                $imageRelative,
                '/'
            );

            while (str_starts_with($imageRelative, '../')) {
                $imageRelative = substr(
                    $imageRelative,
                    3
                );
            }

            /* Chemin physique de l'image */
            $imagePath = __DIR__
                . '/../'
                . $imageRelative;

            /* Supprimer le fichier image */
            if (
                file_exists($imagePath)
                && is_file($imagePath)
            ) {
                unlink($imagePath);
            }

            /* Supprimer la ligne dans MySQL */
            $stmtDelete = $pdo->prepare("
                DELETE FROM photos
                WHERE id = :id
            ");

            $stmtDelete->execute([
                ':id' => $id
            ]);
        }
    }

    header('Location: photos.php');
    exit;
}


/* =========================
   RÉCUPÉRER LES PHOTOS
========================= */

$stmt = $pdo->prepare("
    SELECT *
    FROM photos
    ORDER BY categorie ASC, id ASC
");

$stmt->execute();

$photos = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Gestion des photos - Robbie Lens
    </title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #1f2039;
            color: white;
        }


        /* =========================
           HEADER
        ========================= */

        header {
            background-color: white;
            padding: 20px 40px;

            display: flex;
            justify-content: space-between;
            align-items: center;

            gap: 20px;
        }

        header h1 {
            margin: 0;
            color: #242424;
            font-size: 1.5em;
        }

        .header-actions {
            display: flex;
            gap: 10px;
        }

        .header-actions a {
            padding: 10px 15px;

            background-color: #8e86b5;
            color: white;

            text-decoration: none;
            border-radius: 5px;
        }

        .header-actions a:hover {
            background-color: #696484;
        }


        /* =========================
           CONTENU
        ========================= */

        main {
            width: 90%;
            max-width: 1200px;
            margin: 40px auto;
        }

        h2 {
            margin-bottom: 30px;
            text-align: center;
        }


        /* =========================
           GRILLE DES PHOTOS
        ========================= */

        .photos {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }


        /* =========================
           CARTE PHOTO
        ========================= */

        .photo-card {
            background-color: white;
            color: #242424;

            padding: 15px;

            border-radius: 8px;
        }

        .photo-card img {
            width: 100%;
            height: 220px;

            object-fit: cover;
            display: block;

            margin-bottom: 15px;

            border-radius: 5px;
        }

        .photo-card p {
            margin: 8px 0;
        }

        .categorie {
            font-weight: bold;
            color: #8e86b5;
        }


        /* =========================
           ACTIONS
        ========================= */

        .actions {
            display: flex;
            flex-direction: column;
            gap: 10px;

            margin-top: 15px;
        }

        .modifier {
            display: block;

            width: 100%;

            padding: 10px;

            background-color: #8e86b5;
            color: white;

            text-align: center;
            text-decoration: none;

            border-radius: 5px;
        }

        .modifier:hover {
            background-color: #696484;
        }

        .supprimer {
            width: 100%;

            padding: 10px;

            border: none;

            border-radius: 5px;

            background-color: #c0392b;

            color: white;

            cursor: pointer;
        }

        .supprimer:hover {
            background-color: #992d22;
        }


        /* =========================
           TABLETTE
        ========================= */

        @media screen and (max-width: 800px) {

            .photos {
                grid-template-columns: repeat(2, 1fr);
            }

            header {
                padding: 20px;
            }

            .header-actions {
                flex-wrap: wrap;
                justify-content: flex-end;
            }

        }


        /* =========================
           MOBILE
        ========================= */

        @media screen and (max-width: 500px) {

            header {
                flex-direction: column;

                padding: 15px 20px;

                text-align: center;
            }

            .header-actions {
                width: 100%;

                flex-direction: column;
            }

            .header-actions a {
                width: 100%;
                text-align: center;
            }

            main {
                width: 90%;
                margin: 30px auto;
            }

            .photos {
                grid-template-columns: 1fr;
            }

        }

    </style>

</head>


<body>


<header>

    <h1>
        Gestion des photos
    </h1>


    <div class="header-actions">

        <a href="dashboard.php">
            Tableau de bord
        </a>

        <a href="ajouter-photo.php">
            Ajouter une photo
        </a>

    </div>

</header>


<main>

    <h2>
        Photos enregistrées
    </h2>


    <div class="photos">

        <?php foreach ($photos as $photo): ?>

            <?php

            /* =========================
               CONSTRUIRE L'URL DE L'IMAGE
            ========================= */

            $cheminImage = str_replace(
                '\\',
                '/',
                $photo['image']
            );

            $cheminImage = ltrim(
                $cheminImage,
                '/'
            );

            while (str_starts_with($cheminImage, '../')) {
                $cheminImage = substr(
                    $cheminImage,
                    3
                );
            }


            /*
             * Encoder chaque partie du chemin
             * pour gérer les espaces.
             */

            $partiesChemin = explode(
                '/',
                $cheminImage
            );

            $partiesEncodees = array_map(
                'rawurlencode',
                $partiesChemin
            );


            $srcImage =
                '/lens/'
                . implode('/', $partiesEncodees);

            ?>


            <div class="photo-card">

                <img
                    src="<?= htmlspecialchars($srcImage) ?>"
                    alt="<?= htmlspecialchars($photo['titre']) ?>"
                >


                <p>

                    <strong>
                        <?= htmlspecialchars($photo['titre']) ?>
                    </strong>

                </p>


                <p class="categorie">

                    Catégorie :
                    <?= htmlspecialchars($photo['categorie']) ?>

                </p>


                <div class="actions">

                    <!-- MODIFIER -->

                    <a
                        href="modifier-photo.php?id=<?= (int) $photo['id'] ?>"
                        class="modifier"
                    >
                        Modifier
                    </a>


                    <!-- SUPPRIMER -->

                    <form
                        method="post"
                        action="photos.php"
                    >

                        <input
                            type="hidden"
                            name="id"
                            value="<?= (int) $photo['id'] ?>"
                        >


                        <button
                            type="submit"
                            class="supprimer"
                            onclick="return confirm('Voulez-vous vraiment supprimer cette photo ?');"
                        >
                            Supprimer
                        </button>

                    </form>

                </div>

            </div>

        <?php endforeach; ?>

    </div>

</main>


</body>

</html>