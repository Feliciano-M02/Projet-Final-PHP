if ($_GET['action'] === 'login') {
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // renvoyer au frontend les infos nécessaires
        echo json_encode([
            "success" => true,
            "user" => [
                "id" => $user['id'],
                "firstname" => $user['firstname'],
                "lastname" => $user['lastname'],
                "email" => $user['email'],
                "avatar" => $user['avatar'],
                "role_id" => $user['role_id']
            ]
        ]);
        exit;
    } else {
        echo json_encode(["success" => false, "message" => "Identifiants invalides."]);
        exit;
    }
}