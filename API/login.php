<?php
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Méthode non autorisée
    echo json_encode(["success" => false, "message" => "Méthode non autorisée"]);
    exit;
}

// Inclure la connexion PDO
require_once '../config/database.php';

// Récupérer les données JSON
$data = json_decode(file_get_contents("php://input"), true);

// Extraire les champs
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

// Vérifier les champs requis
if (!$email || !$password) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Email et mot de passe requis"]);
    exit;
}

// Validation de l'email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Email invalide"]);
    exit;
}

try {
    // Connexion à la base
    $pdo = new PDO("mysql:host=localhost;dbname=myChat", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer l'utilisateur correspondant à l'email
    $stmt = $pdo->prepare("SELECT id, firstname, lastname, email, password, role FROM users WHERE LOWER(email) = LOWER(?)");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérifier si l'utilisateur existe et le mot de passe est correct
    if ($user && password_verify($password, $user['password'])) {
        // Connexion réussie → On peut générer une session ou un token ici si besoin

        // Tu peux aussi démarrer une session si tu travailles avec des sessions
        // session_start();
        // $_SESSION['user_id'] = $user['id'];

        http_response_code(200);
        echo json_encode([
            "success" => true,
            "message" => "Connexion réussie",
            "user" => [
                "id" => $user['id'],
                "firstname" => $user['firstname'],
                "lastname" => $user['lastname'],
                "email" => $user['email'],
                "role" => $user['role']
            ]
        ]);
    } else {
        http_response_code(401); // Non autorisé
        echo json_encode(["success" => false, "message" => "Email ou mot de passe incorrect"]);
    }

} catch (Exception $e) {
    http_response_code(500); // Erreur serveur
    echo json_encode(["success" => false, "message" => "Erreur serveur : " . $e->getMessage()]);
}