<?php

session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../config.php';


/* =========================
   ACTIONS
========================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';


    /* =========================
       AJOUTER UN TARIF
    ========================= */

    if ($action === 'ajouter') {

        $designation = trim($_POST['designation'] ?? '');
        $quantite = trim($_POST['quantite'] ?? '');
        $prix = trim($_POST['prix'] ?? '');

        if (
            $designation !== '' &&
            $quantite !== '' &&
            $prix !== ''
        ) {

            $stmt = $pdo->prepare("
                INSERT INTO tarifs
                (designation, quantite, prix)
                VALUES
                (:designation, :quantite, :prix)
            ");

            $stmt->execute([
                ':designation' => $designation,
                ':quantite' => $quantite,
                ':prix' => $prix
            ]);
        }

        header('Location: tarifs.php');
        exit;
    }


    /* =========================
       SUPPRIMER UN TARIF
    ========================= */

    if ($action === 'supprimer') {

        $id = (int) ($_POST['id'] ?? 0);

        if ($id > 0) {

            $stmt = $pdo->prepare("
                DELETE FROM tarifs
                WHERE id = :id
            ");

            $stmt->execute([
                ':id' => $id
            ]);
        }

        header('Location: tarifs.php');
        exit;
    }
}


/* =========================
   RÉCUPÉRER LES TARIFS
========================= */

$stmt = $pdo->prepare("
    SELECT *
    FROM tarifs
    ORDER BY id ASC
");

$stmt->execute();

$tarifs = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
        Gestion des tarifs - Robbie Lens
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


        /* =========================
           CONTENU
        ========================= */

        main {
            width: 90%;

            max-width: 1000px;

            margin: 40px auto;
        }


        .bloc {
            background-color: white;

            color: #242424;

            padding: 25px;

            border-radius: 10px;

            margin-bottom: 30px;
        }


        h2 {
            margin-top: 0;

            margin-bottom: 25px;
        }


        /* =========================
           FORMULAIRE
        ========================= */

        .champ {
            margin-bottom: 15px;
        }

        label {
            display: block;

            margin-bottom: 8px;
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
            padding: 10px 15px;

            border: none;

            border-radius: 5px;

            color: white;

            cursor: pointer;

            font-family: inherit;
        }


        .ajouter {
            background-color: #8e86b5;
        }

        .ajouter:hover {
            background-color: #696484;
        }


        /* =========================
           TABLEAU
        ========================= */

        .table-container {
            width: 100%;

            overflow-x: auto;
        }

        table {
            width: 100%;

            border-collapse: collapse;

            min-width: 650px;
        }

        th,
        td {
            border: 1px solid #ddd;

            padding: 12px;

            text-align: left;

            vertical-align: middle;
        }

        th {
            background-color: #f2f2f2;

            color: #242424;
        }

        td {
            color: #242424;
        }


        /* =========================
           BOUTONS ACTION
        ========================= */

        .actions {
            display: flex;

            flex-direction: column;

            gap: 8px;

            min-width: 110px;
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

            background-color: #c0392b;
        }

        .supprimer:hover {
            background-color: #992d22;
        }


        /* =========================
           MOBILE
        ========================= */

        @media screen and (max-width: 600px) {

            header {
                flex-direction: column;

                padding: 15px 20px;

                text-align: center;
            }

            main {
                width: 90%;

                margin: 30px auto;
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
        Gestion des tarifs
    </h1>

    <a href="dashboard.php">
        Tableau de bord
    </a>

</header>


<main>


    <!-- =========================
         AJOUTER UN TARIF
    ========================== -->

    <div class="bloc">

        <h2>
            Ajouter un tarif
        </h2>


        <form
            method="post"
            action="tarifs.php"
        >

            <input
                type="hidden"
                name="action"
                value="ajouter"
            >


            <div class="champ">

                <label for="designation">
                    Désignation
                </label>

                <input
                    type="text"
                    name="designation"
                    id="designation"
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
                    required
                >

            </div>


            <button
                type="submit"
                class="ajouter"
            >
                Ajouter
            </button>

        </form>

    </div>


    <!-- =========================
         TARIFS ENREGISTRÉS
    ========================== -->

    <div class="bloc">

        <h2>
            Tarifs enregistrés
        </h2>


        <?php if (count($tarifs) === 0): ?>

            <p>
                Aucun tarif enregistré.
            </p>

        <?php else: ?>

            <div class="table-container">

                <table>

                    <thead>

                        <tr>

                            <th>
                                Désignation
                            </th>

                            <th>
                                Quantité
                            </th>

                            <th>
                                Prix
                            </th>

                            <th>
                                Action
                            </th>

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


                                <td>

                                    <div class="actions">

                                        <!-- MODIFIER -->

                                        <a
                                            href="modifier-tarif.php?id=<?= (int) $tarif['id'] ?>"
                                            class="modifier"
                                        >
                                            Modifier
                                        </a>


                                        <!-- SUPPRIMER -->

                                        <form
                                            method="post"
                                            action="tarifs.php"
                                        >

                                            <input
                                                type="hidden"
                                                name="action"
                                                value="supprimer"
                                            >


                                            <input
                                                type="hidden"
                                                name="id"
                                                value="<?= (int) $tarif['id'] ?>"
                                            >


                                            <button
                                                type="submit"
                                                class="supprimer"
                                                onclick="return confirm('Voulez-vous vraiment supprimer ce tarif ?');"
                                            >
                                                Supprimer
                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php endif; ?>

    </div>


</main>


</body>

</html>