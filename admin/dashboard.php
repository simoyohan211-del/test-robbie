<?php

session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tableau de bord - Robbie Lens</title>

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
            padding: 20px 40px;

            background-color: white;

            color: #242424;

            display: flex;

            justify-content: space-between;

            align-items: center;
        }

        header h1 {
            margin: 0;

            font-size: 1.5em;
        }

        .deconnexion {
            padding: 10px 15px;

            background-color: #8e86b5;

            color: white;

            text-decoration: none;

            border-radius: 5px;
        }

        .deconnexion:hover {
            background-color: #696484;
        }

        main {
            width: 90%;

            max-width: 1100px;

            margin: 50px auto;
        }

        h2 {
            text-align: center;

            margin-bottom: 40px;
        }

        .dashboard {
            display: grid;

            grid-template-columns: repeat(2, 1fr);

            gap: 25px;
        }

        .card {
            padding: 30px;

            background-color: white;

            color: #242424;

            border-radius: 10px;

            text-align: center;
        }

        .card h3 {
            margin-top: 0;

            font-size: 1.4em;
        }

        .card p {
            color: #555;

            line-height: 1.5;
        }

        .card a {
            display: inline-block;

            margin-top: 15px;

            padding: 12px 20px;

            background-color: #8e86b5;

            color: white;

            text-decoration: none;

            border-radius: 5px;
        }

        .card a:hover {
            background-color: #696484;
        }

        @media screen and (max-width: 700px) {

            header {
                padding: 15px 20px;

                flex-direction: column;

                gap: 15px;
            }

            main {
                width: 90%;

                margin: 30px auto;
            }

            .dashboard {
                grid-template-columns: 1fr;
            }

        }

    </style>

</head>

<body>

<header>

    <h1>
        Administration Robbie Lens
    </h1>

    <a
        href="logout.php"
        class="deconnexion"
    >
        Déconnexion
    </a>

</header>

<main>

    <h2>
        Bienvenue <?= htmlspecialchars($_SESSION['admin_nom']) ?>
    </h2>

    <div class="dashboard">

        <div class="card">

            <h3>
                📷 Photos
            </h3>

            <p>
                Ajouter, modifier et supprimer les photos
                du portfolio et de l'accueil.
            </p>

            <a href="photos.php">
                Gérer les photos
            </a>

        </div>


        <div class="card">

            <h3>
                🛠️ Services
            </h3>

            <p>
                Gérer les services proposés par
                Robbie Lens.
            </p>

            <a href="services.php">
                Gérer les services
            </a>

        </div>


        <div class="card">

            <h3>
                💰 Tarifs
            </h3>

            <p>
                Ajouter ou modifier les tarifs.
            </p>

            <a href="tarifs.php">
                Gérer les tarifs
            </a>

        </div>


        <div class="card">

            <h3>
                ✉️ Messages
            </h3>

            <p>
                Consulter les messages envoyés
                depuis le formulaire de contact.
            </p>

            <a href="messages.php">
                Voir les messages
            </a>

        </div>

    </div>

</main>

</body>

</html>