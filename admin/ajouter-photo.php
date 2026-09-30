<?php

session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../config.php';

$erreur = '';
$succes = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $titre = trim($_POST['titre'] ?? '');
    $categorie = $_POST['categorie'] ?? '';
    $description = trim($_POST['description'] ?? '');

    $categoriesAutorisees = [
        'accueil',
        'paysage',
        'portrait'
    ];

    /* Vérifier les champs */

    if ($titre === '') {

        $erreur = 'Veuillez saisir un titre.';

    } elseif (!in_array($categorie, $categoriesAutorisees, true)) {

        $erreur = 'Catégorie incorrecte.';

    } elseif (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {

        $erreur = 'Veuillez sélectionner une image.';

    } else {

        $fichier = $_FILES['image'];

        /* Vérifier la taille */

        $tailleMax = 5 * 1024 * 1024;

        if ($fichier['size'] > $tailleMax) {

            $erreur = 'L\'image est trop grande. Maximum : 5 Mo.';

        } else {

            /* Vérifier le type réel de l'image */

            $typesAutorises = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/webp' => 'webp'
            ];

            $finfo = new finfo(FILEINFO_MIME_TYPE);

            $typeReel = $finfo->file($fichier['tmp_name']);

            if (!isset($typesAutorises[$typeReel])) {

                $erreur = 'Format non autorisé. Utilisez JPG, PNG ou WEBP.';

            } else {

                $extension = $typesAutorises[$typeReel];

                /* Créer un nom de fichier unique */

                $nomFichier = uniqid('photo_', true) . '.' . $extension;

                /* Dossier où enregistrer l'image */

                $dossier = __DIR__ . '/../IMG ROBBIE LENS/';

                if (!is_dir($dossier)) {

                    mkdir($dossier, 0755, true);
                }

                $cheminPhysique = $dossier . $nomFichier;

                /* Déplacer l'image */

                if (move_uploaded_file(
                    $fichier['tmp_name'],
                    $cheminPhysique
                )) {

                    /* Chemin enregistré dans MySQL */

                    $cheminBDD = 'IMG ROBBIE LENS/' . $nomFichier;

                    $stmt = $pdo->prepare("
                        INSERT INTO photos
                        (titre, categorie, image, description)
                        VALUES
                        (:titre, :categorie, :image, :description)
                    ");

                    $stmt->execute([
                        ':titre' => $titre,
                        ':categorie' => $categorie,
                        ':image' => $cheminBDD,
                        ':description' => $description
                    ]);

                    $succes = 'Photo ajoutée avec succès.';

                } else {

                    $erreur = 'Impossible d\'enregistrer l\'image.';
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

    <title>Ajouter une photo - Robbie Lens</title>

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
            text-align: center;

            margin-top: 0;

            margin-bottom: 30px;
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

        @media screen and (max-width: 600px) {

            header {
                padding: 15px 20px;

                flex-direction: column;

                gap: 15px;

                text-align: center;
            }

            main {
                width: 90%;
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
        Ajouter une photo
    </h1>

    <a href="photos.php">
        Retour aux photos
    </a>

</header>

<main>

    <div class="form-container">

        <h2>
            Nouvelle photo
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


        <form
            method="post"
            action="ajouter-photo.php"
            enctype="multipart/form-data"
        >

            <div class="champ">

                <label for="titre">
                    Titre de la photo
                </label>

                <input
                    type="text"
                    name="titre"
                    id="titre"
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

                    <option value="">
                        -- Choisir une catégorie --
                    </option>

                    <option value="accueil">
                        Accueil
                    </option>

                    <option value="paysage">
                        Paysage
                    </option>

                    <option value="portrait">
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
                ></textarea>

            </div>


            <div class="champ">

                <label for="image">
                    Image
                </label>

                <input
                    type="file"
                    name="image"
                    id="image"
                    accept=".jpg,.jpeg,.png,.webp"
                    required
                >

            </div>


            <button type="submit">
                Ajouter la photo
            </button>

        </form>

    </div>

</main>

</body>

</html>