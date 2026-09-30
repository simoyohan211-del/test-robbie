<?php

session_start();

function visiteurConnecte(): bool
{
    return isset($_SESSION['user_id']);
}

function protegerPage(): void
{
    if (!visiteurConnecte()) {
        header('Location: connexion.php');
        exit;
    }
}