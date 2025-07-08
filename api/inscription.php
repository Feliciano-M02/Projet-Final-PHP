<?php
// Réponse JSON
header('Content-Type: application/json');

// Connexion à la base de données
$host = 'localhost';
$dbname = 'reseau_social';
$user = 'root';
$pass = ''; // Mets ton mot de passe si nécessaire

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur connexion DB']);
    exit;
}

// Récupérer les données POST
$firstname = $_POST['firstname'] ?? '';
$lastname  = $_POST['lastname'] ?? '';
$email     = $_POST['email'] ?? '';
$password  = $_POST['password'] ?? '';

// Vérification simple
if (!$firstname || !$lastname || !$email || !$password) {
    echo json_encode(['success' => false, 'message' => 'Champs requis manquants']);
    exit;
}

// Vérifie si l'email existe déjà
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Email déjà utilisé']);
    exit;
}

// Hachage du mot de passe
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Insertion utilisateur
$stmt = $pdo->prepare("INSERT INTO users (firstname, lastname, email, password) VALUES (?, ?, ?, ?)");
$ok = $stmt->execute([$firstname, $lastname, $email, $hashedPassword]);

if ($ok) {
    echo json_encode(['success' => true, 'message' => 'Inscription réussie !']);
} else {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'inscription']);
}
// Envoi d'email HTML
$to = $email;
$subject = "Bienvenue sur notre Réseau Social";
$message = "
<html>
<head><title>Bienvenue</title></head>
<body>
  <h2>Bonjour $firstname,</h2>
  <p>Merci de vous être inscrit sur notre réseau social !</p>
  <p><a href='http://localhost/vues/clients/connexion.html'>Cliquez ici pour vous connecter</a></p>
</body>
</html>
";
$headers  = "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/html; charset=UTF-8\r\n";
$headers .= "From: no-reply@reseau.local\r\n";

mail($to, $subject, $message, $headers);
