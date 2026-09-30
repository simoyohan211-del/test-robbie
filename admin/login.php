<?php

session_start();

require_once __DIR__ . '/../config.php';

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $motDePasse = $_POST['mot_de_passe'] ?? '';

    if ($email === '' || $motDePasse === '') {

        $erreur = 'Veuillez remplir tous les champs.';

    } else {

        $stmt = $pdo->prepare("
            SELECT *
            FROM admins
            WHERE email = :email
            LIMIT 1
        ");

        $stmt->execute([
            ':email' => $email
        ]);

        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($motDePasse, $admin['mot_de_passe'])) {

            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_nom'] = $admin['nom'];
            $_SESSION['admin_email'] = $admin['email'];

            header('Location: dashboard.php');
            exit;

        } else {

            $erreur = 'Email ou mot de passe incorrect.';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Connexion - Administration Robbie Lens</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;

            display: flex;
            justify-content: center;
            align-items: center;

            font-family: Arial, sans-serif;

            background-color: #1f2039;
        }

        .login-container {
            width: 90%;
            max-width: 420px;

            padding: 40px;

            background-color: white;

            border-radius: 10px;
        }

        h1 {
            margin-top: 0;
            margin-bottom: 30px;

            text-align: center;

            color: #242424;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;

            margin-bottom: 8px;

            color: #242424;
        }

        input {
            width: 100%;

            padding: 12px;

            border: 1px solid #ccc;

            border-radius: 5px;

            font-size: 1em;
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

        .erreur {
            margin-bottom: 20px;

            padding: 10px;

            border-radius: 5px;

            background-color: #f8d7da;

            color: #842029;

            text-align: center;
        }

    </style>

</head>

<body>

    <div class="login-container">

        <h1>Administration</h1>

        <?php if ($erreur !== ''): ?>

            <div class="erreur">
                <?= htmlspecialchars($erreur) ?>
            </div>

        <?php endif; ?>

        <form method="post" action="login.php">

            <div class="form-group">

                <label for="email">
                    Email
                </label>

                <input
                    type="email"
                    name="email"
                    id="email"
                    required
                >

            </div>

            <div class="form-group">

                <label for="mot_de_passe">
                    Mot de passe
                </label>

                <input
                    type="password"
                    name="mot_de_passe"
                    id="mot_de_passe"
                    required
                >

            </div>

            <button type="submit">
                Se connecter
            </button>

        </form>

    </div>

</body>

</html>