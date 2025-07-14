<?php
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Méthode non autorisée
    echo json_encode(["success" => false, "message" => "Méthode non autorisée"]);
    exit;
}

// Inclure le fichier de configuration PDO
require_once '../config/db.php';

// Lire les données JSON reçues
$data = $_POST;

// Extraire et nettoyer les champs
$firstname = trim($data['firstname'] ?? '');
$lastname  = trim($data['lastname'] ?? '');
$email     = trim($data['email'] ?? '');
$password  = $data['password'] ?? '';

// Vérification des champs requis
if (!$firstname || !$lastname || !$email || !$password) {
    http_response_code(400); // Requête invalide
    echo json_encode(["success" => false, "message" => "Tous les champs sont requis"]);
    exit;
}

// Validation de l'email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Email invalide"]);
    exit;
}

// Validation de la longueur du mot de passe
if (strlen($password) < 6) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Le mot de passe doit contenir au moins 6 caractères"]);
    exit;
}



try {
    // Connexion à la base de données (via fichier database.php)
    // $pdo = new PDO("mysql:host=localhost;dbname=myChat", "root", "");
    // $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Vérifier si l'utilisateur existe déjà (email insensible à la casse)
    $stmt = $pdo->prepare("SELECT id FROM users WHERE LOWER(email) = LOWER(?)");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        http_response_code(409); // Conflit (doublon)
        echo json_encode(["success" => false, "message" => "Utilisateur déjà inscrit"]);
        exit;
    }

    // Hacher le mot de passe
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    

    //get the role id
    $roleId = 1; // Par défaut, rôle 'client'

    // Insertion dans la base de données avec rôle par défaut 'client'
    $stmt = $pdo->prepare("INSERT INTO users (firstname, lastname, email, password, role_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$firstname, $lastname, $email, $hashedPassword, $roleId]);

    http_response_code(201); // Créé
    echo json_encode(["success" => true, "message" => "Inscription réussie"]);
} catch (Exception $e) {
    http_response_code(500); // Erreur serveur
    echo json_encode(["success" => false, "message" => "Erreur serveur : " . $e->getMessage()]);
}
