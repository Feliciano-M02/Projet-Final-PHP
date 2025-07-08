<?php
header('Content-Type: application/json');

$host = 'localhost';
$dbname = 'reseau_social';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur DB']);
    exit;
}

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

$stmt = $pdo->prepare("SELECT id, password FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['password'])) {
    echo json_encode(['success' => true, 'message' => 'Connexion réussie', 'userId' => $user['id']]);
} else {
    echo json_encode(['success' => false, 'message' => 'Email ou mot de passe incorrect']);
}
