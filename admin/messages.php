<?php

session_start();

/* Vérifier que l'administrateur est connecté */
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

/* Connexion à la base de données */
require_once __DIR__ . '/../config.php';


/* =========================
   SUPPRESSION D'UN MESSAGE
   ========================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';

    if ($action === 'supprimer') {

        $id = (int) ($_POST['id'] ?? 0);

        if ($id > 0) {

            $stmt = $pdo->prepare("
                DELETE FROM message
                WHERE id = :id
            ");

            $stmt->execute([
                ':id' => $id
            ]);
        }

        header('Location: messages.php');
        exit;
    }
}


/* =========================
   RECUPERATION DES MESSAGES
   ========================= */

$stmt = $pdo->prepare("
    SELECT *
    FROM message
    ORDER BY date_creation DESC
");

$stmt->execute();

$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Messages - Administration Robbie Lens</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #242424;
        }

        header {
            background-color: #1f2039;
            color: white;
            padding: 20px 40px;

            display: flex;
            justify-content: space-between;
            align-items: center;

            gap: 20px;
        }

        header h1 {
            margin: 0;
            font-size: 24px;
        }

        .header-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-block;
            padding: 10px 16px;
            border-radius: 6px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-dashboard {
            background-color: white;
            color: #1f2039;
        }

        .btn-delete {
            background-color: #dc3545;
            color: white;
        }

        main {
            width: 90%;
            max-width: 1200px;
            margin: 40px auto;
        }

        .messages-container {
            background-color: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        h2 {
            margin-top: 0;
            margin-bottom: 25px;
        }

        .message-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            background-color: #fafafa;
        }

        .message-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
            margin-bottom: 15px;
        }

        .message-info strong {
            display: block;
            font-size: 18px;
            margin-bottom: 5px;
        }

        .message-info span {
            display: block;
            color: #666;
            margin-bottom: 4px;
        }

        .message-date {
            color: #777;
            font-size: 14px;
            white-space: nowrap;
        }

        .message-content {
            background-color: white;
            padding: 15px;
            border-radius: 6px;
            line-height: 1.6;
            white-space: pre-wrap;
            margin-top: 15px;
        }

        .message-actions {
            margin-top: 15px;
        }

        .empty {
            text-align: center;
            padding: 40px 20px;
            color: #777;
        }

        @media screen and (max-width: 700px) {

            header {
                padding: 20px;
                flex-direction: column;
                align-items: flex-start;
            }

            main {
                width: 95%;
                margin: 20px auto;
            }

            .messages-container {
                padding: 15px;
            }

            .message-header {
                flex-direction: column;
                gap: 10px;
            }

            .message-date {
                white-space: normal;
            }

        }

    </style>

</head>


<body>


<header>

    <h1>Messages reçus</h1>

    <div class="header-actions">

        <a
            href="dashboard.php"
            class="btn btn-dashboard"
        >
            ← Dashboard
        </a>

    </div>

</header>


<main>

    <div class="messages-container">

        <h2>Liste des messages</h2>


        <?php if (empty($messages)): ?>

            <div class="empty">

                <p>Aucun message reçu pour le moment.</p>

            </div>

        <?php else: ?>


            <?php foreach ($messages as $message): ?>

                <div class="message-card">


                    <div class="message-header">


                        <div class="message-info">

                            <strong>
                                <?= htmlspecialchars($message['nom']) ?>
                            </strong>

                            <span>
                                Email :
                                <?= htmlspecialchars($message['email']) ?>
                            </span>

                        </div>


                        <div class="message-date">

                            <?= htmlspecialchars($message['date_creation']) ?>

                        </div>


                    </div>


                    <div class="message-content">

                        <?= htmlspecialchars($message['message']) ?>

                    </div>


                    <div class="message-actions">

                        <form
                            method="post"
                            action="messages.php"
                            onsubmit="return confirm('Voulez-vous vraiment supprimer ce message ?');"
                        >

                            <input
                                type="hidden"
                                name="action"
                                value="supprimer"
                            >

                            <input
                                type="hidden"
                                name="id"
                                value="<?= (int) $message['id'] ?>"
                            >

                            <button
                                type="submit"
                                class="btn btn-delete"
                            >
                                Supprimer
                            </button>

                        </form>

                    </div>


                </div>

            <?php endforeach; ?>


        <?php endif; ?>


    </div>

</main>


</body>

</html>