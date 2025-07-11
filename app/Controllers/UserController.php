<?php
// app/Controllers/UserController.php

require_once __DIR__ . '/../Models/User.php';

class UserController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            $user = $this->userModel->findByUsername($username);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                header('Location: ' . BASE_URL . '/');
                exit();
            } else {
                $error = "Nom d'utilisateur ou mot de passe incorrect.";
                require_once __DIR__ . '/../Views/auth/login.php';
            }
        } else {
            require_once __DIR__ . '/../Views/auth/login.php';
        }
    }

    public function logout() {
        session_unset();
        session_destroy();
        header('Location: ' . BASE_URL . '/login');
        exit();
    }

    // --- Fonctions de gestion des utilisateurs (ADMIN SEULEMENT) ---

    private function requireAdmin() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: ' . BASE_URL . '/');
            exit();
        }
    }

    public function index() {
        $this->requireAdmin();
        $users = $this->userModel->getAllUsers();
        require_once __DIR__ . '/../Views/users/index.php';
    }

    public function create() {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? 'secretaire';
            $email = $_POST['email'] ?? '';

            if (empty($username) || empty($password) || empty($email)) {
                $error = "Tous les champs sont obligatoires.";
            } elseif ($this->userModel->findByUsername($username)) {
                $error = "Ce nom d'utilisateur existe déjà.";
            } elseif ($this->userModel->create($username, $password, $role, $email)) {
                $_SESSION['success_message'] = "Utilisateur créé avec succès !";
                header('Location: ' . BASE_URL . '/users');
                exit();
            } else {
                $error = "Erreur lors de la création de l'utilisateur.";
            }
        }
        require_once __DIR__ . '/../Views/users/create.php';
    }

    public function edit($id) {
        $this->requireAdmin();
        $user = $this->userModel->getUserById($id);

        if (!$user) {
            header('Location: ' . BASE_URL . '/users');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? $user['username'];
            $role = $_POST['role'] ?? $user['role'];
            $email = $_POST['email'] ?? $user['email'];
            $password = $_POST['password'] ?? null;

            if (empty($username) || empty($email)) {
                $error = "Nom d'utilisateur et email sont obligatoires.";
            } elseif ($this->userModel->update($id, $username, $role, $email, $password)) {
                $_SESSION['success_message'] = "Utilisateur mis à jour avec succès !";
                header('Location: ' . BASE_URL . '/users');
                exit();
            } else {
                $error = "Erreur lors de la mise à jour de l'utilisateur.";
            }
        }
        require_once __DIR__ . '/../Views/users/edit.php';
    }

    public function delete($id) {
        $this->requireAdmin();
        if ($this->userModel->delete($id)) {
            $_SESSION['success_message'] = "Utilisateur supprimé avec succès !";
        } else {
            $_SESSION['error_message'] = "Erreur lors de la suppression de l'utilisateur.";
        }
        header('Location: ' . BASE_URL . '/users');
        exit();
    }
}