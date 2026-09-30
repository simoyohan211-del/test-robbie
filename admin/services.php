<?php

session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../config.php';


/* =========================
   AJOUTER OU SUPPRIMER
========================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';


    /* =========================
       AJOUTER UN SERVICE
    ========================= */

    if ($action === 'ajouter') {

        $nom = trim($_POST['nom'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if ($nom !== '') {

            $stmt = $pdo->prepare("
                INSERT INTO services (nom, description)
                VALUES (:nom, :description)
            ");

            $stmt->execute([
                ':nom' => $nom,
                ':description' => $description
            ]);
        }

        header('Location: services.php');
        exit;
    }


    /* =========================
       SUPPRIMER UN SERVICE
    ========================= */

    if ($action === 'supprimer') {

        $id = (int) ($_POST['id'] ?? 0);

        if ($id > 0) {

            $stmt = $pdo->prepare("
                DELETE FROM services
                WHERE id = :id
            ");

            $stmt->execute([
                ':id' => $id
            ]);
        }

        header('Location: services.php');
        exit;
    }
}


/* =========================
   RÉCUPÉRER LES SERVICES
========================= */

$stmt = $pdo->prepare("
    SELECT *
    FROM services
    ORDER BY id ASC
");

$stmt->execute();

$services = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
        Gestion des services - Robbie Lens
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


        input,
        textarea {
            width: 100%;

            padding: 12px;

            border: 1px solid #ccc;

            border-radius: 5px;

            font-family: inherit;
        }


        textarea {
            min-height: 100px;

            resize: vertical;
        }


        button {
            padding: 10px 15px;

            border: none;

            border-radius: 5px;

            color: white;

            cursor: pointer;
        }


        .ajouter {
            background-color: #8e86b5;
        }


        .ajouter:hover {
            background-color: #696484;
        }


        /* =========================
           SERVICES
        ========================= */

        .service {
            padding: 20px 0;

            border-bottom: 1px solid #ddd;
        }


        .service:last-child {
            border-bottom: none;
        }


        .service h3 {
            margin-top: 0;

            margin-bottom: 10px;
        }


        .service p {
            color: #555;

            line-height: 1.5;
        }


        /* =========================
           ACTIONS
        ========================= */

        .actions {
            display: flex;

            gap: 10px;

            margin-top: 15px;
        }


        .modifier {
            display: inline-block;

            padding: 10px 15px;

            background-color: #8e86b5;

            color: white;

            text-decoration: none;

            border-radius: 5px;
        }


        .modifier:hover {
            background-color: #696484;
        }


        .supprimer {
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


            .bloc {
                padding: 20px;
            }


            .actions {
                flex-direction: column;
            }


            .modifier,
            .supprimer {
                width: 100%;

                text-align: center;
            }

        }

    </style>

</head>


<body>


<header>

    <h1>
        Gestion des services
    </h1>


    <div class="header-actions">

        <a href="dashboard.php">
            Tableau de bord
        </a>

    </div>

</header>


<main>


    <!-- =========================
         AJOUTER UN SERVICE
    ========================== -->

    <div class="bloc">

        <h2>
            Ajouter un service
        </h2>


        <form
            method="post"
            action="services.php"
        >

            <input
                type="hidden"
                name="action"
                value="ajouter"
            >


            <div class="champ">

                <label for="nom">
                    Nom du service
                </label>

                <input
                    type="text"
                    name="nom"
                    id="nom"
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
                ></textarea>

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
         SERVICES ENREGISTRÉS
    ========================== -->

    <div class="bloc">

        <h2>
            Services enregistrés
        </h2>


        <?php if (count($services) === 0): ?>

            <p>
                Aucun service enregistré.
            </p>

        <?php endif; ?>


        <?php foreach ($services as $service): ?>

            <div class="service">

                <h3>
                    <?= htmlspecialchars($service['nom']) ?>
                </h3>


                <p>
                    <?= htmlspecialchars($service['description']) ?>
                </p>


                <div class="actions">

                    <!-- MODIFIER -->

                    <a
                        href="modifier-service.php?id=<?= (int) $service['id'] ?>"
                        class="modifier"
                    >
                        Modifier
                    </a>


                    <!-- SUPPRIMER -->

                    <form
                        method="post"
                        action="services.php"
                    >

                        <input
                            type="hidden"
                            name="action"
                            value="supprimer"
                        >


                        <input
                            type="hidden"
                            name="id"
                            value="<?= (int) $service['id'] ?>"
                        >


                        <button
                            type="submit"
                            class="supprimer"
                            onclick="return confirm('Voulez-vous vraiment supprimer ce service ?');"
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