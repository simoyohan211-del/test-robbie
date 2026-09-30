<?php

session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../config.php';


/* =========================
   RÉCUPÉRER LE TARIF
========================= */

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: tarifs.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT *
    FROM tarifs
    WHERE id = :id
");

$stmt->execute([
    ':id' => $id
]);

$tarif = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$tarif) {
    header('Location: tarifs.php');
    exit;
}


$erreur = '';
$succes = '';


/* =========================
   MODIFIER LE TARIF
========================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $designation = trim($_POST['designation'] ?? '');
    $quantite = trim($_POST['quantite'] ?? '');
    $prix = trim($_POST['prix'] ?? '');

    if ($designation === '') {

        $erreur = 'Veuillez saisir la désignation.';

    } elseif ($quantite === '') {

        $erreur = 'Veuillez saisir la quantité.';

    } elseif ($prix === '') {

        $erreur = 'Veuillez saisir le prix.';

    } else {

        $stmtUpdate = $pdo->prepare("
            UPDATE tarifs
            SET
                designation = :designation,
                quantite = :quantite,
                prix = :prix
            WHERE id = :id
        ");

        $stmtUpdate->execute([
            ':designation' => $designation,
            ':quantite' => $quantite,
            ':prix' => $prix,
            ':id' => $id
        ]);

        $tarif['designation'] = $designation;
        $tarif['quantite'] = $quantite;
        $tarif['prix'] = $prix;

        $succes = 'Tarif modifié avec succès.';
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
        Modifier un tarif - Robbie Lens
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

            gap: 20px;
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

        header a:hover {
            background-color: #696484;
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

        input {
            width: 100%;

            padding: 12px;

            border: 1px solid #ccc;

            border-radius: 5px;

            font-family: inherit;

            font-size: 1em;
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

        @media screen and (max-width: 600px) {

            header {
                flex-direction: column;

                padding: 15px 20px;

                text-align: center;
            }

            .bloc {
                padding: 20px;
            }

        }

    </style>

</head>

<body>

<header>

    <h1>
        Modifier un tarif
    </h1>

    <a href="tarifs.php">
        Retour aux tarifs
    </a>

</header>


<main>

    <div class="bloc">

        <h2>
            Modifier le tarif
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
            action="modifier-tarif.php?id=<?= (int) $tarif['id'] ?>"
        >

            <div class="champ">

                <label for="designation">
                    Désignation
                </label>

                <input
                    type="text"
                    name="designation"
                    id="designation"
                    value="<?= htmlspecialchars($tarif['designation']) ?>"
                    required
                >

            </div>


            <div class="champ">

                <label for="quantite">
                    Quantité
                </label>

                <input
                    type="text"
                    name="quantite"
                    id="quantite"
                    value="<?= htmlspecialchars($tarif['quantite']) ?>"
                    required
                >

            </div>


            <div class="champ">

                <label for="prix">
                    Prix
                </label>

                <input
                    type="text"
                    name="prix"
                    id="prix"
                    value="<?= htmlspecialchars($tarif['prix']) ?>"
                    required
                >

            </div>


            <button type="submit">
                Enregistrer les modifications
            </button>

        </form>

    </div>

</main>

</body>

</html>