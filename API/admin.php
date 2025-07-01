<?php
header('Content-Type: application/json');
include '../config/db.php';

if ($_GET['action'] === 'login') {
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email=? AND role_id IN (2,3)");
    $stmt->execute([$email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin['password'])) {
        echo json_encode([
            "success" => true,
            "admin" => [
                "id" => $admin['id'],
                "firstname" => $admin['firstname'],
                "lastname" => $admin['lastname'],
                "role_id" => $admin['role_id']
            ]
        ]);
    } else {
        echo json_encode(["success"=>false, "message"=>"Accès refusé"]);
    }
    exit;
}


// statistiques
if ($_GET['action'] === 'stats') {
    $nbUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $nbPosts = $pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn();
    $nbComments = $pdo->query("SELECT COUNT(*) FROM comments")->fetchColumn();
    echo json_encode([
        "users"=>$nbUsers,
        "posts"=>$nbPosts,
        "comments"=>$nbComments
    ]);
    exit;
}



// lister les rôles
if ($_GET['action'] === 'list_roles') {
    $roles = $pdo->query("SELECT * FROM roles")->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($roles);
    exit;
}

// ajouter un rôle
if ($_GET['action'] === 'add_role') {
    $role_name = htmlspecialchars($_POST['role_name']);
    $stmt = $pdo->prepare("INSERT INTO roles (role_name) VALUES (?)");
    $stmt->execute([$role_name]);
    echo json_encode(["success"=>true, "message"=>"Rôle ajouté"]);
    exit;
}

// supprimer un rôle
if ($_GET['action'] === 'delete_role') {
    $data = json_decode(file_get_contents("php://input"), true);
    $roleId = $data['role_id'];

    if($roleId <= 3) {
        echo json_encode(["success"=>false,"message"=>"Rôle système non supprimable"]);
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM roles WHERE id=?");
    $stmt->execute([$roleId]);
    echo json_encode(["success"=>true, "message"=>"Rôle supprimé"]);
    exit;
}


// lister tous les utilisateurs + rôles
if ($_GET['action'] === 'list_users') {
    $users = $pdo->query("
        SELECT u.*, r.role_name
        FROM users u
        JOIN roles r ON u.role_id = r.id
    ")->fetchAll(PDO::FETCH_ASSOC);

    $roles = $pdo->query("SELECT * FROM roles")->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "users"=>$users,
        "roles"=>$roles
    ]);
    exit;
}

// supprimer un utilisateur
if ($_GET['action'] === 'delete_user') {
    $data = json_decode(file_get_contents("php://input"), true);
    $pdo->prepare("DELETE FROM users WHERE id=?")->execute([$data['user_id']]);
    echo json_encode(["success"=>true, "message"=>"Utilisateur supprimé"]);
    exit;
}

// changer le rôle d'un utilisateur
if ($_GET['action'] === 'change_role') {
    $data = json_decode(file_get_contents("php://input"), true);
    $stmt = $pdo->prepare("UPDATE users SET role_id=? WHERE id=?");
    $stmt->execute([$data['role_id'], $data['user_id']]);
    echo json_encode(["success"=>true, "message"=>"Rôle mis à jour"]);
    exit;
} 