<?php
header('Content-Type: application/json');
include '../config/db.php';

// récupérer le profil
if ($_GET['action'] === 'profile') {
    $stmt = $pdo->prepare("SELECT id, firstname, lastname, email, avatar FROM users WHERE id=?");
    $stmt->execute([$_GET['user_id']]);
    echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
    exit;
}

// update infos
if ($_GET['action'] === 'update') {
    $id = $_POST['user_id'];
    $firstname = htmlspecialchars($_POST['firstname']);
    $lastname = htmlspecialchars($_POST['lastname']);
    $email = htmlspecialchars($_POST['email']);

    $stmt = $pdo->prepare("UPDATE users SET firstname=?, lastname=?, email=? WHERE id=?");
    $stmt->execute([$firstname, $lastname, $email, $id]);

    echo json_encode(["success"=>true, "message"=>"Profil mis à jour"]);
    exit;
}

// create
if ($_GET['action'] === 'register') {
    $firstname = htmlspecialchars($_POST['firstname']);
    $lastname = htmlspecialchars($_POST['lastname']);
    $email = htmlspecialchars($_POST['email']);
    $password= sha1(htmlspecialchars($_POST['password']));

    $stmt = $pdo->prepare("INSERT INTO users (firstname, lastname, email, password) VALUES (?, ?, ?, ?)");
    $stmt->execute([$firstname, $lastname, $email, $password]);

    echo json_encode(["success"=>true, "message"=>"Profil mis à jour"]);
    exit;
}

// update password
if ($_GET['action'] === 'update_password') {
    $id = $_POST['user_id'];
    $old = $_POST['old_password'];
    $new = password_hash($_POST['new_password'], PASSWORD_BCRYPT);

    $stmt = $pdo->prepare("SELECT password FROM users WHERE id=?");
    $stmt->execute([$id]);
    $u = $stmt->fetch();
    if ($u && password_verify($old, $u['password'])) {
        $pdo->prepare("UPDATE users SET password=? WHERE id=?")->execute([$new, $id]);
        echo json_encode(["success"=>true, "message"=>"Mot de passe modifié"]);
    } else {
        echo json_encode(["success"=>false, "message"=>"Ancien mot de passe incorrect"]);
    }
    exit;
}

// update avatar
if ($_GET['action'] === 'update_avatar') {
    $id = $_POST['user_id'];
    if(isset($_FILES['avatar']) && $_FILES['avatar']['error']==0){
        $file = $_FILES['avatar']['name'];
        $target = "../assets/images/" . $file;
        move_uploaded_file($_FILES['avatar']['tmp_name'], $target);

        $stmt = $pdo->prepare("UPDATE users SET avatar=? WHERE id=?");
        $stmt->execute([$file, $id]);

        echo json_encode(["success"=>true, "message"=>"Photo de profil mise à jour"]);
    }
    exit;
}