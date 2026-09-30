<?php

require_once __DIR__ . '/config.php';
session_start();

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $motDePasse = $_POST['mot_de_passe'] ?? '';

    if ($nom === '' || $email === '' || $motDePasse === '') {

        $erreur = "Tous les champs sont obligatoires.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $erreur = "Adresse e-mail invalide.";

    } elseif (strlen($motDePasse) < 6) {

        $erreur = "Le mot de passe doit contenir au moins 6 caractères.";

    } else {

        $stmt = $pdo->prepare("
            SELECT id
            FROM users
            WHERE email = :email
        ");

        $stmt->execute([
            ':email' => $email
        ]);

        if ($stmt->fetch()) {

            $erreur = "Cette adresse e-mail possède déjà un compte.";

        } else {

            $motDePasseHash = password_hash(
                $motDePasse,
                PASSWORD_DEFAULT
            );

            $stmt = $pdo->prepare("
                INSERT INTO users
                (nom, email, mot_de_passe)
                VALUES
                (:nom, :email, :mot_de_passe)
            ");

            $stmt->execute([
                ':nom' => $nom,
                ':email' => $email,
                ':mot_de_passe' => $motDePasseHash
            ]);

            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['user_nom'] = $nom;
            $_SESSION['user_email'] = $email;

            header('Location: index.php');
            exit;
        }
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

    <title>Inscription - Robbie Lens</title>

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

    <h1>Créer un compte</h1>

    <?php if ($erreur !== ''): ?>

        <div class="erreur">
            <?= htmlspecialchars($erreur) ?>
        </div>

    <?php endif; ?>

    <form method="post">

        <label for="nom">Nom</label>

        <input
            type="text"
            id="nom"
            name="nom"
            required
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
            S'inscrire
        </button>

    </form>

    <p>
        Déjà un compte ?
        <a href="connexion.php">Se connecter</a>
    </p>

</div>

</body>

</html>