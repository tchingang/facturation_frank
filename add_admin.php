<?php
require_once __DIR__ . '/app/Config/config.php';
require_once __DIR__ . '/app/Models/Database.php';
require_once __DIR__ . '/app/Models/User.php';

$userModel = new User();

$username = 'admin';
$password = 'admin123';
$role = 'admin';
$email = 'admin@example.com';

if ($userModel->create($username, $password, $role, $email)) {
    echo "Utilisateur administrateur '$username' créé avec succès !";
} else {
    echo "Erreur lors de la création de l'utilisateur.";
}
?>