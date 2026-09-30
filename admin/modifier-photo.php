<?php

session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../config.php';


/* =========================
   RÉCUPÉRER LA PHOTO
========================= */

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: photos.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT *
    FROM photos
    WHERE id = :id
");

$stmt->execute([
    ':id' => $id
]);

$photo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$photo) {
    header('Location: photos.php');
    exit;
}


$erreur = '';
$succes = '';


/* =========================
   MODIFICATION
========================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $titre = trim($_POST['titre'] ?? '');
    $categorie = $_POST['categorie'] ?? '';
    $description = trim($_POST['description'] ?? '');

    $categoriesAutorisees = [
        'accueil',
        'paysage',
        'portrait'
    ];

    if ($titre === '') {

        $erreur = 'Veuillez saisir un titre.';

    } elseif (!in_array($categorie, $categoriesAutorisees, true)) {

        $erreur = 'Catégorie incorrecte.';

    } else {

        /* =========================
           AUCUNE NOUVELLE IMAGE
        ========================= */

        if (
            !isset($_FILES['image']) ||
            $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE
        ) {

            $stmtUpdate = $pdo->prepare("
                UPDATE photos
                SET
                    titre = :titre,
                    categorie = :categorie,
                    description = :description
                WHERE id = :id
            ");

            $stmtUpdate->execute([
                ':titre' => $titre,
                ':categorie' => $categorie,
                ':description' => $description,
                ':id' => $id
            ]);

            $succes = 'Photo modifiée avec succès.';

        } else {

            /* =========================
               NOUVELLE IMAGE
            ========================= */

            if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {

                $erreur = 'Erreur lors de l\'envoi de l\'image.';

            } else {

                $fichier = $_FILES['image'];

                $tailleMax = 5 * 1024 * 1024;

                if ($fichier['size'] > $tailleMax) {

                    $erreur = 'L\'image est trop grande. Maximum : 5 Mo.';

                } else {

                    $typesAutorises = [
                        'image/jpeg' => 'jpg',
                        'image/png' => 'png',
                        'image/webp' => 'webp'
                    ];

                    $finfo = new finfo(FILEINFO_MIME_TYPE);

                    $typeReel = $finfo->file(
                        $fichier['tmp_name']
                    );

                    if (!isset($typesAutorises[$typeReel])) {

                        $erreur = 'Format non autorisé. Utilisez JPG, PNG ou WEBP.';

                    } else {

                        $extension = $typesAutorises[$typeReel];

                        $nomFichier = uniqid(
                            'photo_',
                            true
                        ) . '.' . $extension;

                        $dossier = __DIR__ . '/../IMG ROBBIE LENS/';

                        if (!is_dir($dossier)) {
                            mkdir($dossier, 0755, true);
                        }

                        $cheminPhysique = $dossier . $nomFichier;

                        if (
                            move_uploaded_file(
                                $fichier['tmp_name'],
                                $cheminPhysique
                            )
                        ) {

                            /* Supprimer ancienne image */

                            $ancienneImage = str_replace(
                                '\\',
                                '/',
                                $photo['image']
                            );

                            $ancienneImage = ltrim(
                                $ancienneImage,
                                '/'
                            );

                            $ancienFichier = __DIR__
                                . '/../'
                                . $ancienneImage;

                            if (
                                file_exists($ancienFichier)
                                && is_file($ancienFichier)
                            ) {
                                unlink($ancienFichier);
                            }


                            /* Nouveau chemin */

                            $nouvelleImage =
                                'IMG ROBBIE LENS/'
                                . $nomFichier;


                            /* Mise à jour MySQL */

                            $stmtUpdate = $pdo->prepare("
                                UPDATE photos
                                SET
                                    titre = :titre,
                                    categorie = :categorie,
                                    image = :image,
                                    description = :description
                                WHERE id = :id
                            ");

                            $stmtUpdate->execute([
                                ':titre' => $titre,
                                ':categorie' => $categorie,
                                ':image' => $nouvelleImage,
                                ':description' => $description,
                                ':id' => $id
                            ]);

                            $succes =
                                'Photo et informations modifiées avec succès.';

                            /* Actualiser les données affichées */

                            $photo['titre'] = $titre;
                            $photo['categorie'] = $categorie;
                            $photo['image'] = $nouvelleImage;
                            $photo['description'] = $description;

                        } else {

                            $erreur =
                                'Impossible d\'enregistrer la nouvelle image.';
                        }
                    }
                }
            }
        }
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

    <title>
        Modifier une photo - Robbie Lens
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

        header {
            background-color: white;

            padding: 20px 40px;

            display: flex;

            justify-content: space-between;

            align-items: center;
        }

        header h1 {
            margin: 0;

            color: #242424;

            font-size: 1.5em;
        }

        header a {
            padding: 10px 15px;

            background-color: #8e86b5;

            color: white;

            text-decoration: none;

            border-radius: 5px;
        }

        main {
            width: 90%;

            max-width: 700px;

            margin: 40px auto;
        }

        .form-container {
            background-color: white;

            color: #242424;

            padding: 30px;

            border-radius: 10px;
        }

        h2 {
            margin-top: 0;

            margin-bottom: 30px;

            text-align: center;
        }

        .champ {
            margin-bottom: 20px;
        }

        label {
            display: block;

            margin-bottom: 8px;

            font-weight: bold;
        }

        input,
        select,
        textarea {
            width: 100%;

            padding: 12px;

            border: 1px solid #ccc;

            border-radius: 5px;

            font-size: 1em;

            font-family: inherit;
        }

        textarea {
            min-height: 120px;

            resize: vertical;
        }

        .image-actuelle {
            margin-bottom: 20px;

            text-align: center;
        }

        .image-actuelle img {
            width: 100%;

            max-width: 400px;

            height: 250px;

            object-fit: cover;

            border-radius: 5px;
        }

        button {
            width: 100%;

            padding: 13px;

            border: none;

            border-radius: 5px;

            background-color: #8e86b5;

            color: white;

            font-size: 1em;

            cursor: pointer;
        }

        button:hover {
            background-color: #696484;
        }

        .erreur,
        .succes {
            margin-bottom: 20px;

            padding: 12px;

            border-radius: 5px;

            text-align: center;
        }

        .erreur {
            background-color: #f8d7da;

            color: #842029;
        }

        .succes {
            background-color: #d1e7dd;

            color: #0f5132;
        }

        .info {
            color: #666;

            font-size: 0.9em;
        }

        @media screen and (max-width: 600px) {

            header {
                padding: 15px 20px;

                flex-direction: column;

                gap: 15px;

                text-align: center;
            }

            .form-container {
                padding: 20px;
            }

        }

    </style>

</head>


<body>


<header>

    <h1>
        Modifier une photo
    </h1>

    <a href="photos.php">
        Retour aux photos
    </a>

</header>


<main>

    <div class="form-container">

        <h2>
            Modifier la photo
        </h2>


        <?php if ($erreur !== ''): ?>

            <div class="erreur">
                <?= htmlspecialchars($erreur) ?>
            </div>

        <?php endif; ?>


        <?php if ($succes !== ''): ?>

            <div class="succes">
                <?= htmlspecialchars($succes) ?>
            </div>

        <?php endif; ?>


        <div class="image-actuelle">

            <?php

            $cheminImage = str_replace(
                '\\',
                '/',
                $photo['image']
            );

            $cheminImage = ltrim(
                $cheminImage,
                '/'
            );

            $parts = explode(
                '/',
                $cheminImage
            );

            $parts = array_map(
                'rawurlencode',
                $parts
            );

            $srcImage =
                '/lens/'
                . implode('/', $parts);

            ?>

            <img
                src="<?= htmlspecialchars($srcImage) ?>"
                alt="<?= htmlspecialchars($photo['titre']) ?>"
            >

        </div>


        <form
            method="post"
            action="modifier-photo.php?id=<?= (int) $photo['id'] ?>"
            enctype="multipart/form-data"
        >


            <div class="champ">

                <label for="titre">
                    Titre
                </label>

                <input
                    type="text"
                    name="titre"
                    id="titre"
                    value="<?= htmlspecialchars($photo['titre']) ?>"
                    required
                >

            </div>


            <div class="champ">

                <label for="categorie">
                    Catégorie
                </label>

                <select
                    name="categorie"
                    id="categorie"
                    required
                >

                    <option
                        value="accueil"
                        <?= $photo['categorie'] === 'accueil' ? 'selected' : '' ?>
                    >
                        Accueil
                    </option>

                    <option
                        value="paysage"
                        <?= $photo['categorie'] === 'paysage' ? 'selected' : '' ?>
                    >
                        Paysage
                    </option>

                    <option
                        value="portrait"
                        <?= $photo['categorie'] === 'portrait' ? 'selected' : '' ?>
                    >
                        Portrait
                    </option>

                </select>

            </div>


            <div class="champ">

                <label for="description">
                    Description
                </label>

                <textarea
                    name="description"
                    id="description"
                ><?= htmlspecialchars($photo['description'] ?? '') ?></textarea>

            </div>


            <div class="champ">

                <label for="image">
                    Nouvelle image
                </label>

                <input
                    type="file"
                    name="image"
                    id="image"
                    accept=".jpg,.jpeg,.png,.webp"
                >

                <p class="info">
                    Laissez vide si vous souhaitez conserver
                    l'image actuelle.
                </p>

            </div>


            <button type="submit">
                Enregistrer les modifications
            </button>


        </form>

    </div>

</main>


</body>

</html>