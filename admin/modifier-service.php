<?php

session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../config.php';


/* =========================
   RÉCUPÉRER LE SERVICE
========================= */

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: services.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT *
    FROM services
    WHERE id = :id
");

$stmt->execute([
    ':id' => $id
]);

$service = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$service) {
    header('Location: services.php');
    exit;
}


$erreur = '';
$succes = '';


/* =========================
   MODIFIER LE SERVICE
========================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nom = trim($_POST['nom'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($nom === '') {

        $erreur = 'Veuillez saisir le nom du service.';

    } else {

        $stmtUpdate = $pdo->prepare("
            UPDATE services
            SET
                nom = :nom,
                description = :description
            WHERE id = :id
        ");

        $stmtUpdate->execute([
            ':nom' => $nom,
            ':description' => $description,
            ':id' => $id
        ]);

        $service['nom'] = $nom;
        $service['description'] = $description;

        $succes = 'Service modifié avec succès.';
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

    <title>Modifier un service - Robbie Lens</title>

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

        .bloc {
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
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-family: inherit;
            font-size: 1em;
        }

        textarea {
            min-height: 150px;
            resize: vertical;
        }

        button {
            width: 100%;
            padding: 13px;
            border: none;
            border-radius: 5px;
            background-color: #8e86b5;
            color: white;
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

    </style>

</head>

<body>

<header>

    <h1>
        Modifier un service
    </h1>

    <a href="services.php">
        Retour aux services
    </a>

</header>

<main>

    <div class="bloc">

        <h2>
            Modifier le service
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
            action="modifier-service.php?id=<?= (int) $service['id'] ?>"
        >

            <div class="champ">

                <label for="nom">
                    Nom du service
                </label>

                <input
                    type="text"
                    name="nom"
                    id="nom"
                    value="<?= htmlspecialchars($service['nom']) ?>"
                    required
                >

            </div>

            <div class="champ">

                <label for="description">
                    Description
                </label>

                <textarea
                    name="description"
                    id="description"
                ><?= htmlspecialchars($service['description'] ?? '') ?></textarea>

            </div>

            <button type="submit">
                Enregistrer les modifications
            </button>

        </form>

    </div>

</main>

</body>

</html>