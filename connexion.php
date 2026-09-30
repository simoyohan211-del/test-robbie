<?php

require_once __DIR__ . '/config.php';
session_start();

$erreur = '';

$redirect = $_GET['redirect'] ?? $_POST['redirect'] ?? 'index.php';

if (!in_array($redirect, ['index.php', 'portfolio.php'], true)) {
    $redirect = 'index.php';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $motDePasse = $_POST['mot_de_passe'] ?? '';

    $stmt = $pdo->prepare("
        SELECT *
        FROM users
        WHERE email = :email
        LIMIT 1
    ");

    $stmt->execute([
        ':email' => $email
    ]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (
        $user &&
        password_verify($motDePasse, $user['mot_de_passe'])
    ) {

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_nom'] = $user['nom'];
        $_SESSION['user_email'] = $user['email'];

        header('Location: ' . $redirect);
        exit;

    } else {

        $erreur = "Email ou mot de passe incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Connexion - Robbie Lens</title>

    <style>

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #1f2039;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .form-container {
            width: 90%;
            max-width: 450px;
            background: white;
            padding: 35px;
            border-radius: 12px;
        }

        h1 {
            text-align: center;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-top: 15px;
            margin-bottom: 5px;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            margin-top: 25px;
            padding: 12px;
            border: none;
            border-radius: 6px;
            background: #8e86b5;
            color: white;
            cursor: pointer;
        }

        .erreur {
            background: #f8d7da;
            color: #842029;
            padding: 10px;
            border-radius: 6px;
        }

        p {
            text-align: center;
            margin-top: 20px;
        }

        a {
            color: #5752a2;
        }

    </style>

</head>

<body>

<div class="form-container">

    <h1>Connexion</h1>

    <?php if ($erreur !== ''): ?>

        <div class="erreur">
            <?= htmlspecialchars($erreur) ?>
        </div>

    <?php endif; ?>

    <form method="post">

        <input
            type="hidden"
            name="redirect"
            value="<?= htmlspecialchars($redirect) ?>"
        >

        <label for="email">Email</label>

        <input
            type="email"
            id="email"
            name="email"
            required
        >

        <label for="mot_de_passe">Mot de passe</label>

        <input
            type="password"
            id="mot_de_passe"
            name="mot_de_passe"
            required
        >

        <button type="submit">
            Se connecter
        </button>

    </form>

    <p>
        Pas encore de compte ?
        <a href="inscription.php">Créer un compte</a>
    </p>

</div>

</body>

</html>